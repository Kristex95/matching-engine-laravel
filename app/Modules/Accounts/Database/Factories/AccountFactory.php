<?php

declare(strict_types=1);

namespace App\Modules\Accounts\Database\Factories;

use App\Modules\Accounts\Domain\Account;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Account>
 */
class AccountFactory extends Factory
{
    protected $model = Account::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'account_number' => $this->faker->unique()->numerify('ACC########'),
        ];
    }
}
