<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductStoreRequest extends FormRequest
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
            'description' => [''],
            'tax_id' => ['required', 'integer', 'exists:taxes,id'],
            'product_type_id' => ['required', 'integer', 'exists:product_types,id'],
            'images' => ['nullable', 'image']
        ];
    }
}
