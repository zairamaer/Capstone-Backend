<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ServiceType;
use Illuminate\Http\Request;
use App\Http\Resources\ServiceTypeResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage; // Import Storage facade
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Log;

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
        
        // Transform image URLs like in ServiceRateController
        $serviceTypes->transform(function ($serviceType) {
            if ($serviceType->serviceTypeImage) {
                // Only prepend the full URL if it doesn't already start with http
                if (!str_starts_with($serviceType->serviceTypeImage, 'http')) {
                    $serviceType->serviceTypeImage = 
                        url('storage/' . $serviceType->serviceTypeImage);
                }
            }
            return $serviceType;
        });
        
        return response()->json($serviceTypes);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        Log::info('ServiceType Store - Received Request Fields:', $request->except('serviceTypeImage'));
        Log::info('ServiceType Store - Received Request File:', ['hasFile' => $request->hasFile('serviceTypeImage')]);
        
        $validator = Validator::make($request->all(), [
            'serviceTypeName' => 'required|string|max:255',
            'serviceTypeDescription' => 'nullable|string',
            'serviceTypeImage' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            Log::warning('ServiceType Store - Validation Failed:', $validator->errors()->toArray());
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validatedData = $validator->validated();
        $imageUrl = null;

        // Handle file upload if an image is provided
        if ($request->hasFile('serviceTypeImage')) {
            $image = $request->file('serviceTypeImage');
            $imageName = time() . '_' . $image->getClientOriginalName();
            
            // Save the image in the public/storage/service_images directory
            $image->move(public_path('storage/service_images'), $imageName);
            
            // Save the relative image URL in the database
            $imageUrl = 'service_images/' . $imageName;
            
            Log::info('ServiceType Store - Image uploaded:', [
                'original_name' => $image->getClientOriginalName(),
                'saved_name' => $imageName,
                'relative_url' => $imageUrl
            ]);
        }

        $serviceType = ServiceType::create([
            'serviceTypeName' => $validatedData['serviceTypeName'],
            'serviceTypeDescription' => $validatedData['serviceTypeDescription'],
            'serviceTypeImage' => $imageUrl,
        ]);

        Log::info('ServiceType Store - Saved to Database:', $serviceType->toArray());

        return response()->json([
            'message' => 'Service type successfully created!',
            'serviceType' => $serviceType
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ServiceType  $serviceType
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(ServiceType $serviceType)
    {
        // Transform image URL for single item
        if ($serviceType->serviceTypeImage) {
            if (!str_starts_with($serviceType->serviceTypeImage, 'http')) {
                $serviceType->serviceTypeImage = 
                    url('storage/' . $serviceType->serviceTypeImage);
            }
        }
        
        return response()->json($serviceType);
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
        Log::info('ServiceType Update - Received Request Fields:', $request->except('serviceTypeImage'));
        Log::info('ServiceType Update - Received Request File:', ['hasFile' => $request->hasFile('serviceTypeImage')]);
        
        $validator = Validator::make($request->all(), [
            'serviceTypeName' => 'nullable|string|max:255',
            'serviceTypeDescription' => 'nullable|string',
            'serviceTypeImage' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            Log::warning('ServiceType Update - Validation Failed:', $validator->errors()->toArray());
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $imageUrl = $serviceType->serviceTypeImage; // Keep existing image by default

        // Handle file upload if an image is provided
        if ($request->hasFile('serviceTypeImage')) {
            // Delete old image if it exists and it's a relative path
            if ($serviceType->serviceTypeImage && !str_starts_with($serviceType->serviceTypeImage, 'http')) {
                $oldImagePath = public_path('storage/' . $serviceType->serviceTypeImage);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                    Log::info('ServiceType Update - Deleted old image:', ['path' => $oldImagePath]);
                }
            }
            
            $image = $request->file('serviceTypeImage');
            $imageName = time() . '_' . $image->getClientOriginalName();
            
            // Save the image in the public/storage/service_images directory
            $image->move(public_path('storage/service_images'), $imageName);
            
            // Save the relative image URL in the database
            $imageUrl = 'service_images/' . $imageName;
            
            Log::info('ServiceType Update - New image uploaded:', [
                'original_name' => $image->getClientOriginalName(),
                'saved_name' => $imageName,
                'relative_url' => $imageUrl
            ]);
        }

        $serviceType->update([
            'serviceTypeName' => $request->serviceTypeName ?? $serviceType->serviceTypeName,
            'serviceTypeDescription' => $request->serviceTypeDescription ?? $serviceType->serviceTypeDescription,
            'serviceTypeImage' => $imageUrl,
        ]);

        Log::info('ServiceType Update - Updated in Database:', $serviceType->toArray());

        return response()->json([
            'message' => 'Service type successfully updated!',
            'serviceType' => $serviceType
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ServiceType  $serviceType
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(ServiceType $serviceType)
    {
        // Delete image if it exists and it's a relative path
        if ($serviceType->serviceTypeImage && !str_starts_with($serviceType->serviceTypeImage, 'http')) {
            $imagePath = public_path('storage/' . $serviceType->serviceTypeImage);
            if (file_exists($imagePath)) {
                unlink($imagePath);
                Log::info('ServiceType Destroy - Deleted image:', ['path' => $imagePath]);
            }
        }

        $serviceType->delete();
        
        Log::info('ServiceType Destroy - Deleted from Database:', ['serviceTypeID' => $serviceType->serviceTypeID]);
        
        return response()->json([
            'message' => 'Service type deleted successfully'
        ], 200);
    }

    /**
     * Debug endpoint to see raw service types data
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function debug()
    {
        $serviceTypes = ServiceType::all();
        
        $debugData = $serviceTypes->map(function ($serviceType) {
            return [
                'serviceTypeID' => $serviceType->serviceTypeID,
                'serviceTypeName' => $serviceType->serviceTypeName,
                'serviceTypeImage' => $serviceType->serviceTypeImage,
                'serviceTypeImage_length' => strlen($serviceType->serviceTypeImage ?? ''),
                'created_at' => $serviceType->created_at,
                'updated_at' => $serviceType->updated_at,
            ];
        });
        
        return response()->json([
            'message' => 'Debug data for service types',
            'data' => $debugData,
            'app_url' => config('app.url'),
            'storage_path' => storage_path('app/public'),
            'public_path' => public_path('storage')
        ]);
    }
}