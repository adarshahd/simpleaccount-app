<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AccountRequest extends FormRequest
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
            'bank_name' => ['required', 'string'],
            'bank_branch' => ['nullable', 'string'],
            'account_name' => ['required', 'string'],
            'account_number' => ['required', 'string'],
            'account_type' => ['required', 'in:savings,current,fixed_deposit,recurring_deposit'],
            'ifsc' => ['nullable', 'string'],
            'active' => ['nullable', 'boolean']
        ];
    }
}
