<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ReceiptResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'bill_number' => $this->bill_number,
            'total' => $this->total,
            'payment_method' => $this->payment_method,
            'payment_reference' => $this->payment_reference,
            'notes' => $this->notes,
            'customer_id' => $this->customer_id,
        ];
    }
}
