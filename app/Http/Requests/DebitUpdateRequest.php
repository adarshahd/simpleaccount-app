<?php

namespace App\Http\Requests;

use App\Rules\DebitItemRule;
use Illuminate\Foundation\Http\FormRequest;

class DebitUpdateRequest extends FormRequest
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
            'vendor_id' => ['required', 'integer', 'exists:vendors,id'],
            'items' => ['required', new DebitItemRule()]
        ];
    }
}
