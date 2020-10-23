<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Manufacturer;
use App\Models\Product;
use App\Models\ProductStock;

class ProductStockFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ProductStock::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'identification' => $this->faker->text,
            'stock' => $this->faker->numberBetween(-100000, 100000),
            'total_stock' => $this->faker->numberBetween(-100000, 100000),
            'mrp' => $this->faker->randomFloat(0, 0, 9999999999.),
            'price' => $this->faker->randomFloat(0, 0, 9999999999.),
            'tax' => $this->faker->randomFloat(0, 0, 9999999999.),
            'hsn' => $this->faker->word,
            'batch' => $this->faker->word,
            'expiry' => $this->faker->date(),
            'product_id' => Product::factory(),
            'manufacturer_id' => Manufacturer::factory(),
        ];
    }
}
