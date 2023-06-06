<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Vendor;
use Faker\Provider\Stripe;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Vendor>
 */
class VendorFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Vendor::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $this->faker->addProvider(new Stripe($this->faker));

        return [
            'currency_id' => $this->faker->numberBetween(1, 3),
            'store_id' => $this->faker->stripeConnectAccountId(),
            'user_id' => User::factory()
        ];
    }
}
