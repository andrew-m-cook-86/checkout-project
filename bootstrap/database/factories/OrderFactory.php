<?php

namespace bootstrap\database\factories;

use App\Models\Order;
use App\Models\User;
use Faker\Provider\Stripe;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Foundation\Testing\WithFaker;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class OrderFactory extends Factory
{
    use WithFaker;

    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Order::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $this->faker->addProvider(new Stripe($this->faker));

        return [
            'total' => $this->faker->randomFloat(2, 100, 1000),
            'currency_id' => $this->faker->numberBetween(1, 3),
            'transaction_id' => $this->faker->stripeCoreChargeId(),
            'user_id' => User::factory()
        ];
    }
}
