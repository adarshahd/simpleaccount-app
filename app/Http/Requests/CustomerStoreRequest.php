<?php

namespace App\Http\Requests;

use App\Http\Controllers\Api\AppSettingController;
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
        $shouldAllowSimpleCustomerData = AppSettingController::shouldAllowSimpleCustomerVendorData();

        return [
            'name' => ['required', 'string', 'max:300'],
            'identification' => $shouldAllowSimpleCustomerData ? ['nullable', 'string', 'max:200'] : ['required', 'string', 'max:200'],
            'address_line_1' => $shouldAllowSimpleCustomerData ? ['nullable', 'string'] : ['required', 'string'],
            'address_line_2' => ['nullable', 'string'],
            'city' => $shouldAllowSimpleCustomerData ? ['nullable', 'string', 'max:300'] : ['required', 'string', 'max:300'],
            'state' => $shouldAllowSimpleCustomerData ? ['nullable', 'string', 'max:300'] : ['required', 'string', 'max:300'],
            'country' => $shouldAllowSimpleCustomerData ? ['nullable', 'string', 'max:300'] : ['required', 'string', 'max:300'],
            'pin' => $shouldAllowSimpleCustomerData ? ['nullable', 'string', 'max:300'] : ['required', 'string', 'max:50'],
            'contact_name' => ['required', 'string', 'max:200'],
            'contact_email' => ['nullable', 'string', 'max:200'],
            'contact_phone' => ['required', 'string', 'max:50'],
            'website' => ['nullable', 'string', 'max:400'],
            'id_type_id' => $shouldAllowSimpleCustomerData ? ['nullable', 'integer', 'exists:id_types,id'] : ['required', 'integer', 'exists:id_types,id'],
            'bank_name' => ['nullable', 'string'],
            'account_name' => ['nullable', 'string'],
            'account_number' => ['nullable', 'string'],
            'ifsc_code' => ['nullable', 'string'],
            'vpa' => ['nullable', 'string'],
            'image' => ['nullable', 'image']
        ];
    }
}
