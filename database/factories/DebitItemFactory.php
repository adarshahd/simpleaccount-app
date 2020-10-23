<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Debit;
use App\Models\DebitItem;
use App\Models\ProductStock;

class DebitItemFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = DebitItem::class;

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
            'debit_id' => Debit::factory(),
            'product_stock_id' => ProductStock::factory(),
        ];
    }
}
