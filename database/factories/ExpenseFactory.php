<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Expense;

class ExpenseFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Expense::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'total' => $this->faker->randomFloat(0, 0, 9999999999.),
            'notes' => $this->faker->text,
            'payment_method' => $this->faker->word,
            'payment_reference' => $this->faker->word,
        ];
    }
}
