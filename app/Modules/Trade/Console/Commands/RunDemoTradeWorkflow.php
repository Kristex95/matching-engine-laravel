<?php

declare(strict_types=1);

namespace App\Modules\Trade\Console\Commands;

use App\Modules\Accounts\Domain\Account;
use App\Modules\Balances\Domain\Balance;
use App\Modules\Orders\Application\DTO\StoreOrderDTO;
use App\Modules\Orders\Application\Services\OrderService;
use App\Modules\Trade\Application\DTO\StoreTradeDTO;
use App\Modules\Trade\Application\Services\TradeService;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

class RunDemoTradeWorkflow extends Command
{
    protected $signature = 'trade:demo-workflow {--force : Run in production without confirmation}';

    protected $description = 'Run a full demo trade workflow against the configured database';

    public function __construct(
        private OrderService $orderService,
        private TradeService $tradeService
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        if (app()->isProduction() && !$this->option('force')) {
            $this->error('This command writes real data. Re-run with --force if you really want this in production.');

            return self::FAILURE;
        }

        $this->info('Creating demo accounts and balances...');

        $sellerAccount = Account::query()->create([
            'account_number' => 'demo-seller-' . Str::uuid()->toString(),
        ]);

        $buyerAccount = Account::query()->create([
            'account_number' => 'demo-buyer-' . Str::uuid()->toString(),
        ]);

        Balance::query()->create([
            'account_id' => $sellerAccount->id,
            'currency' => 'BTC',
            'available' => '1.00000000',
            'locked' => '0.00000000',
        ]);

        Balance::query()->create([
            'account_id' => $buyerAccount->id,
            'currency' => 'USDT',
            'available' => '60000.00000000',
            'locked' => '0.00000000',
        ]);

        $this->info('Placing matching orders...');

        $sellOrder = $this->orderService->storeNewOrder(new StoreOrderDTO(
            side: 'sell',
            type: 'limit',
            currency: 'BTC',
            amount: '1.00000000',
            price: '50000.00000000'
        ), $sellerAccount);

        $buyOrder = $this->orderService->storeNewOrder(new StoreOrderDTO(
            side: 'buy',
            type: 'limit',
            currency: 'BTC',
            amount: '1.00000000',
            price: '50000.00000000'
        ), $buyerAccount);

        $this->info('Processing trade settlement...');

        $trade = $this->tradeService->processNewTrade(new StoreTradeDTO(
            takerOrderId: $buyOrder->uuid,
            makerOrderId: $sellOrder->uuid,
            amount: '1.00000000',
            price: '50000.00000000',
            baseCurrency: 'BTC',
            quoteCurrency: 'USDT',
        ));

        $this->info('Demo trade workflow completed.');

        $this->table(
            ['Type', 'ID', 'UUID / Number'],
            [
                ['Seller account', (string) $sellerAccount->id, $sellerAccount->account_number],
                ['Buyer account', (string) $buyerAccount->id, $buyerAccount->account_number],
                ['Sell order', (string) $sellOrder->id, $sellOrder->uuid],
                ['Buy order', (string) $buyOrder->id, $buyOrder->uuid],
                ['Trade', (string) $trade->id, $trade->maker_order_id . ' -> ' . $trade->taker_order_id],
            ]
        );

        $balances = Balance::query()
            ->whereIn('account_id', [$sellerAccount->id, $buyerAccount->id])
            ->orderBy('account_id')
            ->orderBy('currency')
            /** @var Collection<int, Balance> $balances */
            ->get(['account_id', 'currency', 'available', 'locked'])
            ->map(fn (Balance $balance): array => [
                'account_id' => $balance->account_id,
                'currency' => $balance->currency,
                'available' => $balance->available,
                'locked' => $balance->locked,
            ])
            ->all();

        $this->table(['Account ID', 'Currency', 'Available', 'Locked'], $balances);

        return self::SUCCESS;
    }
}
