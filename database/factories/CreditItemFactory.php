<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Credit;
use App\Models\CreditItem;
use App\Models\ProductStock;

class CreditItemFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CreditItem::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'quantity' => $this->faker->randomNumber(),
            'discount' => $this->faker->randomFloat(0, 0, 9999999999.),
            'credit_id' => Credit::factory(),
            'product_stock_id' => ProductStock::factory(),
        ];
    }
}
