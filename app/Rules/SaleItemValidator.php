<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class SaleItemValidator implements Rule
{
    private $parameterBag = [
        'price' => "required|numeric",
        'quantity' => "required|integer",
        'discount' => "required|numeric",
        'product_stock_id' => "required|integer"
    ];

    private $errorMessage;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->errorMessage = 'Invalid sale items';
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if (!is_array($value) || !$this->isMultidimensionalArray($value)) {
            $this->errorMessage = "Invalid sale items";
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

    private function isMultidimensionalArray($array) {
        foreach ($array as $arr) {
            if (is_array($arr))
                return true;
        }
        return false;
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
