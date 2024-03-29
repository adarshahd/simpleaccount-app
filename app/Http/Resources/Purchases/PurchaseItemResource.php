<?php

namespace App\Http\Resources\Purchases;

use App\Http\Resources\ProductStockResource;
use Illuminate\Http\Resources\Json\JsonResource;

class PurchaseItemResource extends JsonResource
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
            'discount' => $this->discount,
            'purchase_id' => $this->purchase_id,
            'product_stock' => new ProductStockResource($this->productStock),
        ];
    }
}
