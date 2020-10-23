<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Customer;
use App\Models\IdType;

class CustomerFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Customer::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'identification' => $this->faker->word,
            'address_line_1' => $this->faker->text,
            'address_line_2' => $this->faker->text,
            'city' => $this->faker->city,
            'state' => $this->faker->word,
            'country' => $this->faker->country,
            'pin' => $this->faker->word,
            'contact_name' => $this->faker->word,
            'contact_email' => $this->faker->word,
            'contact_phone' => $this->faker->word,
            'website' => $this->faker->word,
            'id_type_id' => IdType::factory(),
        ];
    }
}
