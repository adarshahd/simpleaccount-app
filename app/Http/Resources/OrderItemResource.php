<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
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
            'quantity' => $this->quantity,
            'price' => $this->price,
            'tax_percent' => $this->tax_percent,
            'tax' => $this->tax,
            'discount' => $this->discount,
            'sub_total' => $this->sub_total,
            'total' => $this->total,
            'product' => new ProductResource($this->product),
            'order_id' => $this->order_id,
        ];
    }
}
