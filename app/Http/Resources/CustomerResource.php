<?php
namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\JsonResource;
class CustomerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->customerID,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'appointments' => $this->appointments->map(function ($appointment) {
                return [
                    'id' => $appointment->appointmentID,
                    'service_rate_id' => $appointment->serviceRateID,
                    'datetime' => $appointment->appointmentDateTime,
                    'status' => $appointment->status,
                    'service_rate' => [
                        'serviceRateID' => $appointment->serviceRate->serviceRateID,
                        'vehicleSizeCode' => $appointment->serviceRate->vehicleSizeCode,
                        'serviceTypeID' => $appointment->serviceRate->serviceTypeID,
                        'price' => $appointment->serviceRate->price,
                        'created_at' => $appointment->serviceRate->created_at,
                        'updated_at' => $appointment->serviceRate->updated_at,
                        'service_type' => [
                            'serviceTypeID' => $appointment->serviceRate->serviceType->serviceTypeID,
                            'serviceTypeName' => $appointment->serviceRate->serviceType->serviceTypeName,
                            'serviceTypeDescription' => $appointment->serviceRate->serviceType->serviceTypeDescription,
                            'serviceTypeImage' => $appointment->serviceRate->serviceType->serviceTypeImage,
                            'created_at' => $appointment->serviceRate->serviceType->created_at,
                            'updated_at' => $appointment->serviceRate->serviceType->updated_at,
                        ],
                        'vehicle_size' => [
                            'vehicleSizeCode' => $appointment->serviceRate->vehicleSize->vehicleSizeCode,
                            'vehicleSizeDescription' => $appointment->serviceRate->vehicleSize->vehicleSizeDescription,
                            'created_at' => $appointment->serviceRate->vehicleSize->created_at,
                            'updated_at' => $appointment->serviceRate->vehicleSize->updated_at,
                        ],
                    ],
                ];
            }),
        ];
    }
}