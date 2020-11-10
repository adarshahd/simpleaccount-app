<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SaleItemResource extends JsonResource
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
            'discount' => $this->discount,
            'tax' => $this->tax,
            'sub_total' => $this->sub_total,
            'total' => $this->total,
            'sale_id' => $this->sale_id,
            'product_stock' => new ProductStockResource($this->productStock),
        ];
    }
}
