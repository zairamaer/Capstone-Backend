<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\VehicleSize;
use Illuminate\Http\Request;
use App\Http\Resources\VehicleSizeResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Response; // Import Response facade

class VehicleSizeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $vehicleSizes = VehicleSize::all();
        return response()->json(VehicleSizeResource::collection($vehicleSizes));
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
            'vehicleSizeCode' => 'required|string|unique:vehicle_sizes',
            'vehicleSizeDescription' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $vehicleSize = VehicleSize::create($request->all());
        return response()->json(new VehicleSizeResource($vehicleSize), 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\VehicleSize  $vehicleSize
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(VehicleSize $vehicleSize)
    {
        return response()->json(new VehicleSizeResource($vehicleSize));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\VehicleSize  $vehicleSize
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, VehicleSize $vehicleSize)
    {
        $validator = Validator::make($request->all(), [
            'vehicleSizeDescription' => 'string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $vehicleSize->update($request->all());
        return response()->json(new VehicleSizeResource($vehicleSize), 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\VehicleSize  $vehicleSize
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(VehicleSize $vehicleSize)
    {
        $vehicleSize->delete();
        return response()->json(null, 204);
    }
}
