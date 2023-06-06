<?php

namespace bootstrap\database\seeders;

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
        // Create 10 buyers
        User::factory(10)->create();

        // Create 3 sellers
        User::factory()->has(Product::factory(5)->usd())->create();
        User::factory()->has(Product::factory(5)->gbp())->create();
        User::factory()->has(Product::factory(5)->eur())->create();
    }
}
