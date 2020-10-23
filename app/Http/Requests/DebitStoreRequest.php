<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DebitStoreRequest extends FormRequest
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
            'sub_total' => ['required', 'numeric'],
            'discount' => ['required', 'numeric'],
            'tax' => ['required', 'numeric'],
            'total' => ['required', 'numeric'],
            'vendor_id' => ['required', 'integer', 'exists:vendors,id'],
        ];
    }
}
