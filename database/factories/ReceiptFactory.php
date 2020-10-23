<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Customer;
use App\Models\Receipt;

class ReceiptFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Receipt::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'bill_number' => $this->faker->word,
            'total' => $this->faker->randomFloat(0, 0, 9999999999.),
            'payment_method' => $this->faker->word,
            'payment_reference' => $this->faker->word,
            'notes' => $this->faker->text,
            'customer_id' => Customer::factory(),
        ];
    }
}
