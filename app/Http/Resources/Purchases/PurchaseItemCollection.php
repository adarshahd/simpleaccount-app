<?php

namespace App\Http\Resources\Purchases;

use Illuminate\Http\Resources\Json\ResourceCollection;

class PurchaseItemCollection extends ResourceCollection
{
    public $collects = PurchaseItemResource::class;
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Support\Collection
     */
    public function toArray($request)
    {
        return $this->collection;
    }
}
