<?php

namespace Database\Factories;

use App\Enums\CurrencyEnum;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->colorName() . ' ' . $this->faker->randomElement(['shirt', 'hat', 'trousers', 'coat', 'shoes']),
            'currency_id' => $this->faker->numberBetween(1, 3),
            'price' => $this->faker->numberBetween(10, 1000),
            'user_id' => User::factory()
        ];
    }

    public function usd(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'currency_id' => CurrencyEnum::USD->value,
            ];
        });
    }

    public function eur(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'currency_id' => CurrencyEnum::EUR->value,
            ];
        });
    }

    public function gbp(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'currency_id' => CurrencyEnum::GBP->value,
            ];
        });
    }
}
