<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Product;
use App\Models\User;
use App\Models\Vendor;
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
        User::factory()
            ->has(Product::factory(5)->usd())
            ->has(Vendor::factory(1))
            ->create();
        User::factory()
            ->has(Product::factory(5)->gbp())
            ->has(Vendor::factory(1))
            ->create();
        User::factory()
            ->has(Product::factory(5)->eur())
            ->has(Vendor::factory(1))
            ->create();
    }
}
