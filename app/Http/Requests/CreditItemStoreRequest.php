<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreditItemStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'quantity' => ['required', 'integer', 'gt:0'],
            'discount' => ['required', 'numeric'],
            'credit_id' => ['required', 'integer', 'exists:credits,id'],
            'product_stock_id' => ['required', 'integer', 'exists:product_stocks,id'],
        ];
    }
}
