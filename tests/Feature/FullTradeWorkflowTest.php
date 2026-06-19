<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Modules\Accounts\Domain\Account;
use App\Modules\Balances\Domain\Balance;
use App\Modules\Orders\Application\DTO\StoreOrderDTO;
use App\Modules\Orders\Application\Services\OrderService;
use App\Modules\Trade\Application\DTO\StoreTradeDTO;
use App\Modules\Trade\Application\Services\TradeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FullTradeWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_full_trade_between_two_users_updates_balances_correctly(): void
    {
        // 1. Setup Accounts
        $sellerAccount = Account::factory()->create();
        $buyerAccount = Account::factory()->create();

        // 2. Setup Initial Balances
        // Seller has 1 BTC to sell
        Balance::factory()->create([
            'account_id' => $sellerAccount->id,
            'currency'   => 'BTC',
            'available'  => '1.00000000',
            'locked'     => '0.00000000',
        ]);

        // Buyer has 60,000 USDT to buy BTC
        Balance::factory()->create([
            'account_id' => $buyerAccount->id,
            'currency'   => 'USDT',
            'available'  => '60000.00000000',
            'locked'     => '0.00000000',
        ]);

        $orderService = app(OrderService::class);
        $tradeService = app(TradeService::class);

        // 3. Place Orders
        // Seller places SELL order for 1 BTC at 50,000 USDT
        $sellOrder = $orderService->storeNewOrder(new StoreOrderDTO(
            side: 'sell',
            type: 'limit',
            currency: 'BTC',
            amount: '1.00000000',
            price: '50000.00000000'
        ), $sellerAccount);

        // Buyer places BUY order for 1 BTC at 50,000 USDT
        $buyOrder = $orderService->storeNewOrder(new StoreOrderDTO(
            side: 'buy',
            type: 'limit',
            currency: 'BTC',
            amount: '1.00000000',
            price: '50000.00000000'
        ), $buyerAccount);

        // Verify funds are locked after order creation
        $this->assertDatabaseHas('balances', [
            'account_id' => $sellerAccount->id,
            'currency'   => 'BTC',
            'available'  => '0.00000000',
            'locked'     => '1.00000000',
        ]);
        $this->assertDatabaseHas('balances', [
            'account_id' => $buyerAccount->id,
            'currency'   => 'USDT',
            'available'  => '10000.00000000',
            'locked'     => '50000.00000000',
        ]);

        // 4. Execute Trade (Settlement)
        // Simulate the matching engine finding a match and triggering the TradeService
        $tradeService->processNewTrade(new StoreTradeDTO(
            takerOrderId:  $buyOrder->uuid,
            makerOrderId:  $sellOrder->uuid,
            amount:        '1.00000000',
            price:         '50000.00000000',
            baseCurrency:  'BTC',
            quoteCurrency: 'USDT'
        ));

        // 5. Final Assertions
        // Verify the Trade record exists
        $this->assertDatabaseHas('trades', [
            'taker_order_id' => $buyOrder->uuid,
            'maker_order_id' => $sellOrder->uuid,
            'amount'         => '1.00000000',
            'price'          => '50000.00000000',
        ]);

        // Verify Seller balances: BTC locked is gone, USDT available is added
        $this->assertDatabaseHas('balances', [
            'account_id' => $sellerAccount->id,
            'currency'   => 'BTC',
            'locked'     => '0.00000000',
        ]);
        $this->assertDatabaseHas('balances', [
            'account_id' => $sellerAccount->id,
            'currency'   => 'USDT',
            'available'  => '50000.00000000',
        ]);

        // Verify Buyer balances: USDT locked is gone, BTC available is added
        $this->assertDatabaseHas('balances', [
            'account_id' => $buyerAccount->id,
            'currency'   => 'USDT',
            'locked'     => '0.00000000',
        ]);
        $this->assertDatabaseHas('balances', [
            'account_id' => $buyerAccount->id,
            'currency'   => 'BTC',
            'available'  => '1.00000000',
        ]);
    }
}
