<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Exception;
use Illuminate\Database\QueryException;

class AuthController extends Controller
{
    /**
     * Register a new customer.
     */
    public function register(Request $request)
    {
        try {
            // Validate request
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:customers,email',
                'phone' => 'required|string|max:20',
                'password' => 'required|string|min:8|confirmed',
            ], [
                'email.unique' => 'The email address is already registered',
                'email.required' => 'Email address is required',
                'email.email' => 'Please provide a valid email address',
                'password.confirmed' => 'Password confirmation does not match',
                'password.min' => 'Password must be at least 8 characters long'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422); // Changed to 422 for validation errors
            }

            // Create customer
            $customer = Customer::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
            ]);

            // Check if customer was created successfully
            if (!$customer) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create customer account'
                ], 500);
            }

            // Create token
            $token = $customer->createToken('auth_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Customer registered successfully',
                'data' => [
                    'id' => $customer->customerID,
                    'name' => $customer->name,
                    'email' => $customer->email,
                    'phone' => $customer->phone,
                ],
                'access_token' => $token,
                'token_type' => 'Bearer',
            ], 201);

        } catch (QueryException $e) {
            // Database errors (like duplicate email)
            return response()->json([
                'success' => false,
                'message' => 'Database error occurred',
                'error' => 'Registration failed due to database constraint'
            ], 500);
        } catch (Exception $e) {
            // General server errors
            return response()->json([
                'success' => false,
                'message' => 'Server error occurred',
                'error' => 'Registration failed'
            ], 500);
        }
    }

    /**
     * Log in an existing customer.
     */
    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|string|email|max:255',
                'password' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $customer = Customer::where('email', $request->email)->first();

            if (!$customer) {
                return response()->json([
                    'success' => false,
                    'message' => 'Customer not found with this email address'
                ], 404);
            }

            if (!Hash::check($request->password, $customer->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid password'
                ], 401);
            }

            $token = $customer->createToken('auth_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Login successful',
                'data' => [
                    'id' => $customer->customerID,
                    'name' => $customer->name,
                    'email' => $customer->email,
                    'phone' => $customer->phone,
                ],
                'access_token' => $token,
                'token_type' => 'Bearer',
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Server error occurred during login',
                'error' => 'Login failed'
            ], 500);
        }
    }

    /**
     * Log out the customer.
     */
    public function logout(Request $request)
    {
        try {
            $user = $request->user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'No authenticated user found'
                ], 401);
            }

            $user->currentAccessToken()->delete();

            return response()->json([
                'success' => true,
                'message' => 'Successfully logged out'
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error occurred during logout',
                'error' => 'Logout failed'
            ], 500);
        }
    }
}