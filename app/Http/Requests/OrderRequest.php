<?php

namespace App\Http\Requests;

use App\Rules\OrderItemRule;
use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
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
            'order_type' => ['required', 'in:purchase_order,sale_order'],
            'order_status' => ['nullable', 'in:created,processing,completed'],
            'customer_id' => ['nullable', 'integer', 'exists:customers,id'],
            'vendor_id' => ['nullable', 'integer', 'exists:vendors,id'],
            'items' => ['required', new OrderItemRule()]
        ];
    }
}
