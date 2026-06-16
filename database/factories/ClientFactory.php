<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Client>
 */
class ClientFactory extends Factory
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
            'name' => fake()->company(),
            'email' => fake()->unique()->companyEmail(),
            'phone' => fake()->numerify('98########'),
            'gstin' => strtoupper(fake()->bothify('22AAAAA####A1Z#')),
            'address' => fake()->address(),
            'state' => fake()->state(),
            'pincode' => fake()->postcode(),
        ];
    }
}
