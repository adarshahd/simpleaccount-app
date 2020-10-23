<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\ProductStock;
use App\Models\Purchase;
use App\Models\PurchaseItem;

class PurchaseItemFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PurchaseItem::class;

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
            'purchase_id' => Purchase::factory(),
            'product_stock_id' => ProductStock::factory(),
        ];
    }
}
