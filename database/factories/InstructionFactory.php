<?php

namespace Database\Factories;

use App\Models\Instruction;
use App\Models\User;
use App\Models\Vendor;
use Faker\Provider\Stripe;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Vendor>
 */
class InstructionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Instruction::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $this->faker->addProvider(new Stripe($this->faker));

        return [
            'transaction_id' => $this->faker->stripeConnectTransferId(),
            'vendor_id' => Vendor::factory()
        ];
    }
}
