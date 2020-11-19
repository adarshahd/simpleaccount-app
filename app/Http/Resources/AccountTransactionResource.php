<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AccountTransactionResource extends JsonResource
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
            'type' => $this->type,
            'total' => $this->total,
            'date' => $this->date,
            'method' => $this->method,
            'reference' => $this->reference,
            'notes' => $this->notes,
            'account_id' => $this->account_id
        ];
    }
}
