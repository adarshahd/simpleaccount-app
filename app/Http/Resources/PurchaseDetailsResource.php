<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PurchaseDetailsResource extends JsonResource
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
            'bill_date' => $this->bill_date,
            'sub_total' => round($this->sub_total, 2),
            'discount' => round($this->discount, 2),
            'tax' => round($this->tax, 2),
            'total' => round($this->total),
            'vendor' => new VendorResource($this->vendor),
            'items' => new PurchaseItemCollection($this->purchaseItems()->get())
        ];
    }
}
