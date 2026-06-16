<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        User::factory(10)->create()->each(function ($user) {

            $clients = Client::factory(20)->create([
                'user_id' => $user->id,
            ]);

            $products = Product::factory(30)->create([
                'user_id' => $user->id,
            ]);

            Invoice::factory(15)->create([
                'user_id' => $user->id,
                'client_id' => $clients->random()->id,
            ])->each(function ($invoice) use ($products) {

                InvoiceItem::factory(
                    rand(1, 5)
                )->create([
                    'invoice_id' => $invoice->id,
                    'product_id' => $products->random()->id,
                ]);
            });
        });
    }
}
