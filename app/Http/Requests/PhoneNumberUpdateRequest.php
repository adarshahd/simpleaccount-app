<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PhoneNumberUpdateRequest extends FormRequest
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
            'name' => ['string', 'max:200'],
            'phone_number' => ['required', 'string', 'max:50'],
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'verified' => ['required'],
        ];
    }
}
