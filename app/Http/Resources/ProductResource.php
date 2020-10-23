<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $images = collect();
        foreach($this->getMedia('products') as $media) {
            $images->push($media->getUrl());
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'tax_id' => $this->tax_id,
            'product_type_id' => $this->product_type_id,
            'images' => $images
        ];
    }
}
