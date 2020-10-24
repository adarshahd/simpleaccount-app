<?php

namespace App\Rules;

use App\Models\AppSetting;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class PurchaseItemValidator implements Rule
{
    private $expiryRule;

    private $parameterBag;

    private $errorMessage;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->errorMessage = 'Invalid purchase items';

        $appSettingExpiry = AppSetting::query()->where('key', 'expiry_required')->first();
        if($appSettingExpiry) {
            if($appSettingExpiry->value) {
                $this->expiryRule = "required|date";
            } else {
                $this->expiryRule = "nullable|date";
            }
        } else {
            $this->expiryRule = "required|date";
        }

        $this->parameterBag = [
            'product_id' => "required|integer",
            'manufacturer_id' => "required|integer",
            'mrp' => "required|numeric",
            'price' => "required|numeric",
            'quantity' => "required|integer",
            'discount' => "required|numeric",
            'hsn' => "nullable|string",
            'batch' => "required|string",
            'expiry' => $this->expiryRule,
        ];
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if(sizeof($value) < 1) {
            $this->errorMessage = "Invalid purchase items";
            return false;
        }

        foreach($value as $productItem) {
            $validator = Validator::make($productItem, $this->parameterBag);
            if($validator->fails()) {
                $this->errorMessage = $validator->getMessageBag()->getMessages();
                return false;
            }
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->errorMessage;
    }
}
