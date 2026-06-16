<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Invoice>
 */
class InvoiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $invoiceDate = fake()->dateTimeBetween('-3 months', 'now');

        return [
            'user_id' => User::factory(),
            'client_id' => Client::factory(),
            'invoice_number' => 'INV-' . fake()->unique()->numberBetween(1000, 9999),
            'invoice_date' => $invoiceDate,
            'due_date' => fake()->dateTimeBetween($invoiceDate, '+30 days'),
            'notes' => fake()->optional()->sentence(),
            'status' => fake()->randomElement([
                'draft',
                'sent',
                'paid',
                'overdue'
            ]),
        ];
    }
}
