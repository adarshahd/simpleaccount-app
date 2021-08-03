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
            'price' => round($this->price, 2),
            'discount' => round($this->discount, 2),
            'tax_percent' => $this->tax_percent,
            'tax' => round($this->tax, 2),
            'sub_total' => round($this->sub_total, 2),
            'total' => round($this->total),
            'sale_id' => $this->sale_id,
            'product_stock' => new ProductStockResource($this->productStock),
        ];
    }
}
