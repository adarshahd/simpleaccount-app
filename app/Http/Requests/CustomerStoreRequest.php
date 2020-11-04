<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CustomerStoreRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:300'],
            'identification' => ['required', 'string', 'max:200'],
            'address_line_1' => ['required'],
            'address_line_2' => ['nullable', 'string'],
            'city' => ['required', 'string', 'max:300'],
            'state' => ['required', 'string', 'max:200'],
            'country' => ['required', 'string', 'max:300'],
            'pin' => ['required', 'string', 'max:50'],
            'contact_name' => ['required', 'string', 'max:200'],
            'contact_email' => ['nullable', 'string', 'max:200'],
            'contact_phone' => ['required', 'string', 'max:50'],
            'website' => ['nullable', 'string', 'max:400'],
            'id_type_id' => ['required', 'integer', 'exists:id_types,id'],
            'image' => ['nullable', 'image']
        ];
    }
}
