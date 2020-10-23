<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExpenseStoreRequest extends FormRequest
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
            'total' => ['required', 'numeric'],
            'notes' => ['required'],
            'payment_method' => ['string', 'max:100'],
            'payment_reference' => ['string', 'max:200'],
        ];
    }
}
