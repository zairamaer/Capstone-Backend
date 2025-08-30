<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ServiceType;
use Illuminate\Http\Request;
use App\Http\Resources\ServiceTypeResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage; // Import Storage facade
use Illuminate\Support\Facades\Response;

class ServiceTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $serviceTypes = ServiceType::all();
        return response()->json(ServiceTypeResource::collection($serviceTypes));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'serviceTypeName' => 'required|string|max:255',
            'serviceTypeDescription' => 'nullable|string',
            'serviceTypeImage' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Add validation for image
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        // Handle file upload if an image is provided
        if ($request->hasFile('serviceTypeImage')) {
            $imagePath = $request->file('serviceTypeImage')->store('serviceTypeImages', 'public');
        } else {
            $imagePath = null; // No image provided
        }

        $serviceType = ServiceType::create([
            'serviceTypeName' => $request->serviceTypeName,
            'serviceTypeDescription' => $request->serviceTypeDescription,
            'serviceTypeImage' => $imagePath, // Store the image path
        ]);

        return response()->json(new ServiceTypeResource($serviceType), 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ServiceType  $serviceType
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(ServiceType $serviceType)
    {
        return response()->json(new ServiceTypeResource($serviceType));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ServiceType  $serviceType
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, ServiceType $serviceType)
    {
        $validator = Validator::make($request->all(), [
            'serviceTypeName' => 'nullable|string|max:255',
            'serviceTypeDescription' => 'nullable|string',
            'serviceTypeImage' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Add validation for image
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        // Handle file upload if an image is provided
        if ($request->hasFile('serviceTypeImage')) {
            // Delete old image if it exists
            if ($serviceType->serviceTypeImage && Storage::exists('public/' . $serviceType->serviceTypeImage)) {
                Storage::delete('public/' . $serviceType->serviceTypeImage);
            }
            $imagePath = $request->file('serviceTypeImage')->store('serviceTypeImages', 'public');
        } else {
            $imagePath = $serviceType->serviceTypeImage; // Keep old image if none is uploaded
        }

        $serviceType->update([
            'serviceTypeName' => $request->serviceTypeName ?? $serviceType->serviceTypeName,
            'serviceTypeDescription' => $request->serviceTypeDescription ?? $serviceType->serviceTypeDescription,
            'serviceTypeImage' => $imagePath, // Update the image path if a new one is uploaded
        ]);

        return response()->json(new ServiceTypeResource($serviceType), 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ServiceType  $serviceType
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(ServiceType $serviceType)
    {
        // Delete image if it exists
        if ($serviceType->serviceTypeImage && Storage::exists('public/' . $serviceType->serviceTypeImage)) {
            Storage::delete('public/' . $serviceType->serviceTypeImage);
        }

        $serviceType->delete();
        return response()->json(null, 204);
    }
}
