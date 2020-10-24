<?php

namespace App\Http\Requests;

use App\Rules\SaleItemValidator;
use Illuminate\Foundation\Http\FormRequest;

class SaleUpdateRequest extends FormRequest
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
            'bill_date' => ['required', 'date'],
            'discount' => ['required', 'numeric'],
            'customer_id' => ['required', 'integer', 'exists:customers,id'],
            'sale_items' => ['required', new SaleItemValidator()]
        ];
    }

    public function messages()
    {
        return [
            'sale_items.required' => 'Sale items should be array of items',
        ];
    }
}
