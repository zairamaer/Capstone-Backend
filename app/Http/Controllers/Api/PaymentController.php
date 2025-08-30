<?php 

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Appointment;
use Illuminate\Http\Request;
use App\Http\Resources\PaymentResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller 
{
    private $paymongoSecretKey;
    private $paymongoPublicKey;

    public function __construct()
    {
        $this->paymongoSecretKey = config('services.paymongo.secret_key');
        $this->paymongoPublicKey = config('services.paymongo.public_key');
    }

    /** 
     * Display all payments 
     */
    public function index() 
    {
        $payments = Payment::all();
        return response()->json(PaymentResource::collection($payments));
    }

    /**
     * Create PayMongo checkout session
     */
    public function createCheckout(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'appointmentID' => 'required|integer|exists:appointments,appointmentID',
            'amount' => 'required|numeric|min:1',
            'description' => 'required|string',
            'success_url' => 'required|url',
            'cancel_url' => 'required|url',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        try {
            $appointment = Appointment::findOrFail($request->appointmentID);
            
            // Create checkout session with PayMongo
            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . base64_encode($this->paymongoSecretKey . ':'),
                'Content-Type' => 'application/json',
            ])->post('https://api.paymongo.com/v1/checkout_sessions', [
                'data' => [
                    'attributes' => [
                        'send_email_receipt' => true,
                        'show_description' => true,
                        'show_line_items' => true,
                        'line_items' => [
                            [
                                'currency' => 'PHP',
                                'amount' => (int)($request->amount * 100), // Convert to centavos
                                'description' => $request->description,
                                'name' => 'Car Wash Service',
                                'quantity' => 1,
                            ]
                        ],
                        'payment_method_types' => [
                            'gcash',
                            'grab_pay',
                            'paymaya',
                            'card'
                        ],
                        'description' => $request->description,
                        'success_url' => $request->success_url,
                        'cancel_url' => $request->cancel_url,
                        'metadata' => [
                            'appointment_id' => $appointment->appointmentID,
                            'customer_id' => $appointment->customerID,
                        ]
                    ]
                ]
            ]);

            if ($response->successful()) {
                $checkoutData = $response->json();
                
                // Store pending payment record
                $payment = Payment::create([
                    'appointmentID' => $request->appointmentID,
                    'paymentDateTime' => now(),
                    'amount' => $request->amount,
                    'paymentMethod' => 'PayMongo',
                    'transactionID' => $checkoutData['data']['id'],
                    'status' => 'pending',
                    'paymongo_checkout_id' => $checkoutData['data']['id'],
                ]);

                return response()->json([
                    'success' => true,
                    'checkout_url' => $checkoutData['data']['attributes']['checkout_url'],
                    'checkout_id' => $checkoutData['data']['id'],
                    'payment_id' => $payment->paymentID
                ], 200);
                
            } else {
                Log::error('PayMongo checkout creation failed', [
                    'response' => $response->json(),
                    'status' => $response->status()
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create checkout session'
                ], 400);
            }
            
        } catch (\Exception $e) {
            Log::error('PayMongo checkout error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while creating checkout session'
            ], 500);
        }
    }

    /**
     * Handle PayMongo webhook
     */
    public function handleWebhook(Request $request)
    {
        try {
            $event = $request->all();
            Log::info('Webhook received:', $event);

            $eventType = $event['data']['attributes']['type'] ?? null;

            if ($eventType === 'payment.paid' || $eventType === 'checkout_session.payment.paid') {
                $payload = $event['data']['attributes']['data']; // dito yung main object (payment OR checkout_session)

                $objectId = $payload['id'] ?? null;
                $attributes = $payload['attributes'] ?? [];

                // Safe extract metadata
                $metadata = $attributes['metadata'] ?? [];
                $appointmentId = $metadata['appointment_id'] ?? null;
                $customerId = $metadata['customer_id'] ?? null;

                if (!$appointmentId || !$objectId) {
                    Log::warning('Webhook missing metadata or objectId', [
                        'appointmentId' => $appointmentId,
                        'objectId' => $objectId
                    ]);
                    return response()->json(['error' => 'Invalid webhook payload'], 400);
                }

                // Find payment
                $payment = Payment::where('paymongo_checkout_id', $objectId)
                    ->orWhere('transactionID', $objectId)
                    ->first();

                if ($payment) {
                    $transactionId = $attributes['payments'][0]['id']
                        ?? $objectId;

                    $payment->update([
                        'status' => 'paid',
                        'paymentDateTime' => now(),
                        'transactionID' => $transactionId,
                    ]);

                    Appointment::where('appointmentID', $appointmentId)
                        ->update(['status' => 'confirmed']);
                } else {
                    Log::warning('No matching payment found for webhook', [
                        'objectId' => $objectId,
                        'appointmentId' => $appointmentId
                    ]);
                }
            } else {
                Log::info('Webhook event ignored', ['eventType' => $eventType]);
            }

            return response()->json(['success' => true], 200);

        } catch (\Exception $e) {
            Log::error('Webhook error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Webhook failed'], 500);
        }
    }




    /**
     * Check payment status
     */
    public function checkPaymentStatus($paymentId)
    {
        try {
            $payment = Payment::findOrFail($paymentId);
            
            if ($payment->status === 'pending' && $payment->paymongo_checkout_id) {
                // Check with PayMongo API
                $response = Http::withHeaders([
                    'Authorization' => 'Basic ' . base64_encode($this->paymongoSecretKey . ':'),
                ])->get("https://api.paymongo.com/v1/checkout_sessions/{$payment->paymongo_checkout_id}");
                
                if ($response->successful()) {
                    $checkoutData = $response->json();
                    $status = $checkoutData['data']['attributes']['status'];
                    
                    if ($status === 'paid') {
                        $payment->update([
                            'status' => 'paid',
                            'paymentDateTime' => now(),
                        ]);
                        
                        // Update appointment status
                        $appointment = Appointment::find($payment->appointmentID);
                        if ($appointment) {
                            $appointment->update(['status' => 'confirmed']);
                        }
                    }
                }
            }
            
            return response()->json([
                'payment_id' => $payment->paymentID,
                'status' => $payment->status,
                'amount' => $payment->amount,
                'appointment_id' => $payment->appointmentID
            ]);
            
        } catch (\Exception $e) {
            return response()->json(['error' => 'Payment not found'], 404);
        }
    }

    /** 
     * Store a new payment (legacy method - kept for backward compatibility)
     */
    public function store(Request $request) 
    {
        $validator = Validator::make($request->all(), [
            'appointmentID' => 'required|integer',
            'paymentDateTime' => 'required|date',
            'amount' => 'required|numeric',
            'paymentMethod' => 'required|string|max:255',
            'transactionID' => 'nullable|string|max:255',
            'status' => 'string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $payment = Payment::create($request->all());
        return response()->json(new PaymentResource($payment), 201);
    }

    /** 
     * Show a single payment 
     */
    public function show(Payment $payment) 
    {
        return response()->json(new PaymentResource($payment));
    }

    /** 
     * Update a payment 
     */
    public function update(Request $request, Payment $payment) 
    {
        $validator = Validator::make($request->all(), [
            'appointmentID' => 'integer',
            'paymentDateTime' => 'date',
            'amount' => 'numeric',
            'paymentMethod' => 'string|max:255',
            'transactionID' => 'nullable|string|max:255',
            'status' => 'string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $payment->update($request->all());
        return response()->json(new PaymentResource($payment), 200);
    }

    /** 
     * Delete a payment 
     */
    public function destroy(Payment $payment) 
    {
        $payment->delete();
        return response()->json(null, 204);
    }
}