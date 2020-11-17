<?php

namespace App\Http\Requests;

use App\Rules\DebitItemRule;
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
            'discount' => ['required', 'numeric'],
            'vendor_id' => ['required', 'integer', 'exists:vendors,id'],
            'items' => ['required', new DebitItemRule()]
        ];
    }
}
