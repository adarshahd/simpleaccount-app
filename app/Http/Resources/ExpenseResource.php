<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ExpenseResource extends JsonResource
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
            'date' => $this->date,
            'total' => $this->total,
            'notes' => $this->notes,
            'payment_method' => $this->payment_method,
            'payment_reference' => $this->payment_reference,
        ];
    }
}
