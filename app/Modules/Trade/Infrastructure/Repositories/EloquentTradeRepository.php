<?php

declare(strict_types=1);

namespace App\Modules\Trade\Infrastructure\Repositories;

use App\Modules\Trade\Application\DTO\StoreTradeDTO;
use App\Modules\Trade\Domain\Trade;
use Illuminate\Pagination\LengthAwarePaginator;

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

    public function storeTrade(StoreTradeDTO $dto, int $takerAccountId, int $makerAccountId): Trade
    {
        return Trade::query()->create([
            'taker_account_id' => $takerAccountId,
            'maker_account_id' => $makerAccountId,
            'taker_order_id'   => $dto->takerOrderId,
            'maker_order_id'   => $dto->makerOrderId,
            'price'            => $dto->price,
            'amount'           => $dto->amount,
            'currency'         => $dto->baseCurrency,
        ]);
    }

    public function getPaginatedByAccountId(int $accountId): LengthAwarePaginator
    {
        return Trade::query()
            ->where(function ($query) use ($accountId): void {
                $query->where('maker_account_id', $accountId)
                    ->orWhere('taker_account_id', $accountId);
            })
            ->paginate(15)
            ->appends(request()->query());
    }
}
