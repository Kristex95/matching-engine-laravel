<?php

declare(strict_types=1);

namespace App\Modules\Orders\Database\Factories;

use App\Modules\Accounts\Domain\Account;
use App\Modules\Balances\Domain\Currency;
use App\Modules\Orders\Domain\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    protected $model = Order::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = $this->faker->randomElement(['limit', 'market']);
        $side = $this->faker->randomElement(['buy', 'sell']);

        return [
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
            'status' => 'pending',
        ];
    }

    public function buy(): static
    {
        return $this->state(fn () => [
            'side' => 'buy',
        ]);
    }

    public function sell(): static
    {
        return $this->state(fn () => [
            'side' => 'sell',
        ]);
    }

    public function limit(): static
    {
        return $this->state(fn () => [
            'type' => 'limit',
            'price' => $this->faker->randomFloat(8, 10, 10000),
        ]);
    }

    public function market(): static
    {
        return $this->state(fn () => [
            'type' => 'market',
            'price' => null,
        ]);
    }

    public function filled(float $amount = null): static
    {
        return $this->state(fn () => [
            'filled_amount' => $amount ?? $this->faker->randomFloat(8, 0.001, 5),
            'status' => 'filled',
        ]);
    }

    public function partiallyFilled(): static
    {
        return $this->state(fn () => [
            'filled_amount' => $this->faker->randomFloat(8, 0.0001, 0.5),
            'status' => 'partially_filled',
        ]);
    }

    public function canceled(): static
    {
        return $this->state(fn () => [
            'status' => 'canceled',
        ]);
    }
}
