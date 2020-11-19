<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AccountResource extends JsonResource
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
            'bank_name' => $this->bank_name,
            'bank_branch' => $this->bank_branch,
            'account_name' => $this->account_name,
            'account_number' => $this->account_number,
            'account_type' => $this->account_type,
            'ifsc' => $this->ifsc,
            'balance' => $this->balance,
            'active' => $this->active
        ];
    }
}
