<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ServiceRateResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'serviceRateID' => $this->serviceRateID,
            'vehicleSizeCode' => $this->vehicleSizeCode,
            'serviceTypeID' => $this->serviceTypeID,
            'serviceTypeName' => $this->serviceType->serviceTypeName ?? null,
            'serviceTypeDescription' => $this->serviceType->serviceTypeDescription ?? null, 
            'serviceTypeImage' => $this->serviceType->serviceTypeImage ?? null, // FIX: Get from serviceType relationship
            'price' => $this->price,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
