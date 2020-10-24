<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SaleResource extends JsonResource
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
            'bill_id' => $this->bill_id,
            'bill_number' => $this->bill_number,
            'bill_date' => $this->bill_date,
            'sub_total' => $this->sub_total,
            'discount' => $this->discount,
            'tax' => $this->tax,
            'total' => $this->total,
            'customer_id' => $this->customer_id,
            'items' => new SaleItemCollection($this->saleItems()->get())
        ];
    }
}
