<?php

declare(strict_types=1);

namespace App\Modules\Orders\Database\Factories;

use App\Modules\Accounts\Domain\Account;
use App\Modules\Balances\Domain\Currency;
use App\Modules\Orders\Domain\ActiveOrder;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ActiveOrder>
 */
class ActiveOrderFactory extends Factory
{
    protected $model = ActiveOrder::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = $this->faker->randomElement(['limit', 'market']);
        $side = $this->faker->randomElement(['buy', 'sell']);
        $uuid = $this->faker->uuid;

        return [
            'uuid' => $uuid,
            'account_id' => Account::factory(),
            'side' => $side,
            'type' => $type,
            'currency' => $this->faker->randomElement(Currency::values()),
            // Market orders don't have price
            'price' => $type === 'market'
                ? null
                : $this->faker->randomFloat(8, 10, 10000),
            'amount' => $this->faker->randomFloat(8, 0.001, 5),
            'filled_amount' => 0,
            'status' => 'new',
        ];
    }
}
