<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Customer;
use App\Models\Sale;

class SaleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Sale::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'bill_id' => $this->faker->numberBetween(-100000, 100000),
            'bill_number' => $this->faker->word,
            'bill_date' => $this->faker->date(),
            'sub_total' => $this->faker->randomFloat(0, 0, 9999999999.),
            'discount' => $this->faker->randomFloat(0, 0, 9999999999.),
            'tax' => $this->faker->randomFloat(0, 0, 9999999999.),
            'total' => $this->faker->randomFloat(0, 0, 9999999999.),
            'customer_id' => Customer::factory(),
        ];
    }
}
