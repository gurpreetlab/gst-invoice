<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => fake()->words(3, true),
            'hsn_code' => fake()->numerify('######'),
            'unit' => fake()->randomElement([
                'PCS',
                'KG',
                'LTR',
                'BOX',
                'MTR'
            ]),
            'price' => fake()->randomFloat(2, 100, 10000),
            'tax_rate' => fake()->randomElement([
                0,
                5,
                12,
                18,
                28
            ]),
        ];
    }
}
