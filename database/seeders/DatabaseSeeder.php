<?php

namespace Database\Seeders;

use App\Modules\Accounts\Database\Seeders\AccountSeeder;
use App\Modules\Accounts\Domain\Account;
use App\Modules\Auth\Domain\User;
use App\Modules\Balances\Domain\Balance;
use App\Modules\Orders\Domain\Order;
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
        $account = Account::factory()->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'account_id' => $account->id,
        ]);

        User::factory(10)->create();
        Account::factory(10)->create();
        Balance::factory(10)->create();
        Order::factory(10)->create();
    }
}
