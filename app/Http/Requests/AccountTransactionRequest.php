<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AccountTransactionRequest extends FormRequest
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
            'type' => ['required', 'in:credit,debit'],
            'total' => ['required', 'numeric', 'min:1'],
            'method' => ['required', 'in:cash,cheque,online'],
            'reference' => ['nullable', 'string'],
            'date' => ['required', 'date_format:yy-m-d'],
            'notes' => ['nullable', 'string'],
            'account_id' => ['required', 'integer']
        ];
    }
}