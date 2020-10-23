<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductStockUpdateRequest extends FormRequest
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
            'identification' => ['required'],
            'stock' => ['required', 'integer'],
            'total_stock' => ['required', 'integer'],
            'mrp' => ['required', 'numeric'],
            'price' => ['required', 'numeric'],
            'tax' => ['required', 'numeric'],
            'hsn' => ['required', 'string'],
            'batch' => ['required', 'string'],
            'expiry' => ['date'],
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'manufacturer_id' => ['required', 'integer', 'exists:manufacturers,id'],
        ];
    }
}
