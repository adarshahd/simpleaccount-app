<?php

namespace App\Http\Requests;

use App\Rules\CreditItemRule;
use Illuminate\Foundation\Http\FormRequest;

class CreditUpdateRequest extends FormRequest
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
            'customer_id' => ['required', 'integer', 'exists:customers,id'],
            'items' => ['required', new CreditItemRule()]
        ];
    }
}
