<?php

namespace App\Http\Resources\Purchases;

use Illuminate\Http\Resources\Json\ResourceCollection;

class PurchaseCollection extends ResourceCollection
{
    public $collects = PurchaseResource::class;
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'data' => $this->collection,
        ];
    }
}
