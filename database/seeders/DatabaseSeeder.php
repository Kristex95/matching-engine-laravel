<?php

namespace Database\Seeders;

use App\Modules\Accounts\Domain\Account;
use App\Modules\Balances\Domain\Balance;
use App\Modules\Orders\Domain\Order;
use App\Modules\Trade\Database\seeders\TradeSeeder;
use App\Modules\Users\Domain\User;
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
        $domainSeeders = app()->tagged('domain_seeders');

        foreach ($domainSeeders as $seeder) {
            $this->call(get_class($seeder));
        }

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
