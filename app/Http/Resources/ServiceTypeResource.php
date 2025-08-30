<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ServiceTypeResource extends JsonResource
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
            'serviceTypeID' => $this->serviceTypeID,
            'serviceTypeName' => $this->serviceTypeName,
            'serviceTypeDescription' => $this->serviceTypeDescription,
            'serviceTypeImage' => $this->serviceTypeImage,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
