<?php

declare(strict_types=1);

namespace App\Modules\Orders\Tests\Feature;

use App\Modules\Accounts\Domain\Account;
use App\Modules\Orders\Domain\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_order_belongs_to_account(): void
    {
        $account = Account::factory()->create();
        $order = Order::factory()->create([
            'account_id' => $account->id,
        ]);

        $this->assertEquals($account->id, $order->account->id);
    }
}
