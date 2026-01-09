<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ServiceRate;
use App\Models\ServiceType;
use Illuminate\Http\Request;
use App\Http\Resources\ServiceRateResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;


class ServiceRateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $serviceRates = ServiceRate::with(['serviceType', 'vehicleSize'])->get();
    
        $serviceRates->transform(function ($serviceRate) {
            if ($serviceRate->serviceType && $serviceRate->serviceType->serviceTypeImage) {
                // Only prepend the full URL if it doesn't already start with http
                if (!str_starts_with($serviceRate->serviceType->serviceTypeImage, 'http')) {
                    $serviceRate->serviceType->serviceTypeImage =
                        url('storage/' . $serviceRate->serviceType->serviceTypeImage);
                }
            }
            return $serviceRate;
        });
    
        return response()->json($serviceRates);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        Log::info('Received Request Fields:', $request->except('serviceTypeImage'));
        Log::info('Received Request File:', ['hasFile' => $request->hasFile('serviceTypeImage')]);
    
        $validator = Validator::make($request->all(), [
            'vehicleSizeCode' => 'required|string|max:10',
            'serviceTypeID' => 'required|exists:service_types,serviceTypeID',
            'price' => 'required|numeric',
            'serviceTypeImage' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
    
        if ($validator->fails()) {
            Log::warning('Validation Failed:', $validator->errors()->toArray());
            return response()->json(['errors' => $validator->errors()], 422);
        }
    
        $validatedData = $validator->validated();
    
        $serviceRate = ServiceRate::create([
            'vehicleSizeCode' => $validatedData['vehicleSizeCode'],
            'serviceTypeID' => $validatedData['serviceTypeID'],
            'price' => $validatedData['price']
        ]);
    
        if ($request->hasFile('serviceTypeImage')) {
            $image = $request->file('serviceTypeImage');
            $imageName = time() . '_' . $image->getClientOriginalName();
            
            // Save the image in the public/service_images directory
            $image->move(public_path('storage/service_images'), $imageName);
            
            // Save the relative image URL in the database
            $imageUrl = 'service_images/' . $imageName; // Relative URL
            
            // Update the serviceType record with the image URL
            $serviceType = ServiceType::find($validatedData['serviceTypeID']);
            if ($serviceType) {
                $serviceType->serviceTypeImage = $imageUrl; // Store the relative URL in the database
                $serviceType->save();
            }
        }
    
        $serviceRate->load(['serviceType', 'vehicleSize']);
    
        Log::info('Saved to Database:', $serviceRate->toArray());
    
        return response()->json([
            'message' => 'Service rate successfully added!',
            'serviceRate' => $serviceRate
        ], 201);
    }
    
    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ServiceRate  $serviceRate
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(ServiceRate $serviceRate)
    {
        return response()->json(new ServiceRateResource($serviceRate));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ServiceRate  $serviceRate
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, ServiceRate $serviceRate)
    {
        $validator = Validator::make($request->all(), [
            'vehicleSizeCode' => 'string',
            'serviceTypeID' => 'integer|exists:service_types,serviceTypeID',
            'price' => 'numeric',
            'serviceTypeName' => 'string|max:255',
            'serviceTypeDescription' => 'string|max:255',
            'serviceTypeImage' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }
    
        $serviceRate->update($request->only(['vehicleSizeCode', 'serviceTypeID', 'price']));
    
        if ($serviceRate->serviceType) {
            $updates = $request->only(['serviceTypeName', 'serviceTypeDescription']);
    
            if ($request->hasFile('serviceTypeImage')) {
                $image = $request->file('serviceTypeImage');
                $imageName = time() . '_' . $image->getClientOriginalName();
                $image->move(public_path('storage/service_images'), $imageName);
                $updates['serviceTypeImage'] = 'service_images/' . $imageName;
            }
    
            $serviceRate->serviceType->update($updates);
        }
    
        $serviceRate->refresh()->load('serviceType');
    
        return response()->json([
            'message' => 'Service rate and type updated with image!',
            'serviceRate' => new ServiceRateResource($serviceRate),
        ]);
    }    
    
    

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ServiceRate  $serviceRate
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(ServiceRate $serviceRate)
    {
        $serviceRate->delete();

        return response()->json([
            'message' => 'Service rate deleted successfully'
        ], 200);
    }

}
