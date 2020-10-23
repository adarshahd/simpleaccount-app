<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\ProductStock;
use App\Models\Sale;
use App\Models\SaleItem;

class SaleItemFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = SaleItem::class;

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
            'sale_id' => Sale::factory(),
            'product_stock_id' => ProductStock::factory(),
        ];
    }
}
