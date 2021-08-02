<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductStockResource extends JsonResource
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
            'identification' => $this->identification,
            'stock' => $this->stock,
            'total_stock' => $this->total_stock,
            'mrp' => round($this->mrp, 2),
            'price' => round($this->price, 2),
            'tax_percent' => $this->tax_percent,
            'tax' => round($this->tax, 2),
            'hsn' => $this->hsn,
            'batch' => $this->batch,
            'expiry' => $this->expiry,
            'product' => new ProductResource($this->product),
            'manufacturer_id' => $this->manufacturer_id,
        ];
    }
}
