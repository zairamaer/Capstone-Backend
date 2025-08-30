<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
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
            'paymentID' => $this->paymentID,
            'appointmentID' => $this->appointmentID,
            'paymentDateTime' => $this->paymentDateTime,
            'amount' => $this->amount,
            'paymentMethod' => $this->paymentMethod,
            'transactionID' => $this->transactionID,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
