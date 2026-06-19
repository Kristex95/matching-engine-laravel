<?php

declare(strict_types=1);

namespace App\Modules\Trade\Infrastructure\Repositories;

use App\Modules\Trade\Application\DTO\StoreTradeDTO;
use App\Modules\Trade\Domain\Trade;

class EloquentTradeRepository implements TradeRepository
{
    public function findExistingTrade(StoreTradeDTO $dto): ?Trade
    {
        return Trade::query()
            ->where('taker_order_id', $dto->takerOrderId)
            ->where('maker_order_id', $dto->makerOrderId)
            ->where('price', $dto->price)
            ->where('amount', $dto->amount)
            ->first();
    }

    public function storeTrade(StoreTradeDTO $dto): Trade
    {
        return Trade::query()->create([
            'taker_order_id' => $dto->takerOrderId,
            'maker_order_id' => $dto->makerOrderId,
            'price'          => $dto->price,
            'amount'         => $dto->amount,
        ]);
    }
}
