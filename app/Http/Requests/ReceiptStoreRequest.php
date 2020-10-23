<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReceiptStoreRequest extends FormRequest
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
            'total' => ['required', 'numeric'],
            'payment_method' => ['string', 'max:100'],
            'payment_reference' => ['string', 'max:200'],
            'notes' => [''],
            'customer_id' => ['required', 'integer', 'exists:customers,id'],
        ];
    }
}
