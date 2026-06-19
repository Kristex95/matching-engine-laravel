<?php

declare(strict_types=1);

namespace App\Modules\Trade\Application\Services;

use App\Modules\Balances\PublicApi\BalancesApi;
use App\Modules\Orders\Application\Services\OrderService;
use App\Modules\Orders\Domain\Order;
use App\Modules\Trade\Application\DTO\StoreTradeDTO;
use App\Modules\Trade\Domain\Trade;
use App\Modules\Trade\Infrastructure\Repositories\TradeRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class TradeService
{
    public function __construct(
        private TradeRepository $tradeRepository,
        private OrderService $orderService,
        private BalancesApi $balanceApi
    ) {}

    public function processNewTrade(StoreTradeDTO $dto): Trade
    {
        Log::info('Incoming trade', [
            'takerOrderId' => $dto->takerOrderId,
            'makerOrderId' => $dto->makerOrderId,
            'amount' => $dto->amount,
            'price' => $dto->price,
            'baseCurrency' => $dto->baseCurrency,
            'quoteCurrency' => $dto->quoteCurrency,
        ]);
        return DB::transaction(function () use ($dto) {
            $existingTrade = $this->tradeRepository->findExistingTrade($dto);

            if ($existingTrade !== null) {
                return $existingTrade;
            }

            // Retrieve maker and taker orders
            $makerOrder = $this->orderService->getByUuidForTrading($dto->makerOrderId);
            $takerOrder = $this->orderService->getByUuidForTrading($dto->takerOrderId);

            $this->settleBalancesForOrder($makerOrder, $dto);
            Log::info("Maker balance updated");

            $this->settleBalancesForOrder($takerOrder, $dto);
            Log::info("Taker balance updated");

            return $this->tradeRepository->storeTrade($dto);
        });
    }

    /**
     * Settles balances for a specific order involved in a trade.
     * Handles price improvement refunds for BUY orders.
     */
    private function settleBalancesForOrder(Order $order, StoreTradeDTO $trade): void
    {
        $tradeAmount = (string) $trade->amount;
        $tradePrice = (string) $trade->price;
        $tradeQuoteTotal = bcmul($tradeAmount, $tradePrice, 8);

        if ($order->side === 'sell') {
            // Seller: Deduct base (locked), add quote (available)
            $this->balanceApi->confirmDeduction($order->account_id, $trade->baseCurrency, $tradeAmount);
            $this->balanceApi->addFunds($order->account_id, $trade->quoteCurrency, $tradeQuoteTotal);
            return;
        }

        if ($order->side === 'buy') {
            // Buyer: Deduct quote (locked at order limit price), add base (available)
            // If order price > trade price, refund the difference to available quote balance.
            $lockedQuoteAmount = bcmul($tradeAmount, (string) $order->price, 8);

            $this->balanceApi->confirmDeduction($order->account_id, $trade->quoteCurrency, $lockedQuoteAmount);
            $this->balanceApi->addFunds($order->account_id, $trade->baseCurrency, $tradeAmount);

            $refund = bcsub($lockedQuoteAmount, $tradeQuoteTotal, 8);
            if (bccomp($refund, '0', 8) === 1) {
                $this->balanceApi->addFunds($order->account_id, $trade->quoteCurrency, $refund);
            }
            return;
        }

        throw new RuntimeException("Invalid order side encountered for order {$order->uuid}: {$order->side}");
    }
}
