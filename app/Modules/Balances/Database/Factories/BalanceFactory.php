<?php

declare(strict_types=1);

namespace App\Modules\Balances\Database\Factories;

use App\Modules\Accounts\Domain\Account;
use App\Modules\Balances\Domain\Balance;
use App\Modules\Balances\Domain\Currency;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Balance>
 */
class BalanceFactory extends Factory
{
    protected $model = Balance::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // Create account if was not defined in create()
            'account_id' => Account::factory(),
            'currency'   => $this->faker->randomElement(Currency::values()),
            'available'  => (string) $this->faker->randomFloat(8, 10, 5000),
            'locked'     => (string) $this->faker->randomElement([
                '0.00000000',
                (string) $this->faker->randomFloat(8, 0, 50),
            ]),
        ];
    }

    public function empty(): static
    {
        return $this->state(fn (array $attributes) => [
            'available' => '0.00000000',
            'locked'    => '0.00000000',
        ]);
    }

    public function currency(string $currency): static
    {
        return $this->state(fn (array $attributes) => [
            'currency' => strtoupper($currency),
        ]);
    }
}
