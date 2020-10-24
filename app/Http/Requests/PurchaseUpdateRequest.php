<?php

namespace App\Http\Requests;

use App\Rules\PurchaseItemValidator;
use Illuminate\Foundation\Http\FormRequest;

class PurchaseUpdateRequest extends FormRequest
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
            'bill_number' => ['required', 'string', 'max:100'],
            'bill_date' => ['required', 'date'],
            'discount' => ['required', 'numeric'],
            'vendor_id' => ['required', 'integer', 'exists:vendors,id'],
            'purchase_items' => ['required', new PurchaseItemValidator()]
        ];
    }

    public function messages()
    {
        return [
            'purchase_items.required' => 'Purchase items should be array of items',
        ];
    }
}
