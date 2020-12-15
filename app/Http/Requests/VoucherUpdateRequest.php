<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VoucherUpdateRequest extends FormRequest
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
            'bill_date' => ['required', 'date_format:Y-m-d'],
            'total' => ['required', 'numeric'],
            'payment_method' => ['required', 'string', 'max:100'],
            'payment_reference' => ['nullable', 'string', 'max:200'],
            'notes' => ['nullable', 'string'],
            'vendor_id' => ['required', 'integer', 'exists:vendors,id'],
        ];
    }
}
