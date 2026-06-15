<?php

declare(strict_types=1);

namespace App\Modules\Balances\Infrastructure\Repositories;

use App\Modules\Balances\Domain\Balance;
use Illuminate\Support\Facades\DB;

class EloquentBalanceRepository implements BalanceRepository
{
    public function lockFunds(int $accountId, string $currency, string $amount): Balance
    {
        Balance::query()
            ->where('account_id', $accountId)
            ->where('currency', $currency)
            ->where('available', '>=', $amount)
            ->decrement('available', (float) $amount, [
                'locked' => DB::raw("locked + {$amount}"),
            ]);

        return $this->findByAccountIdAndCurrency($accountId, $currency);
    }

    public function releaseFunds(int $accountId, string $currency, string $amount): Balance
    {
        Balance::query()
            ->where('account_id', $accountId)
            ->where('currency', $currency)
            ->where('locked', '>=', $amount)
            ->decrement('locked', (float) $amount, [
                'available' => DB::raw("available + {$amount}"),
            ]);

        return $this->findByAccountIdAndCurrency($accountId, $currency);
    }

    public function deductLockedFunds(int $accountId, string $currency, string $amount): Balance
    {
        Balance::query()
            ->where('account_id', $accountId)
            ->where('currency', $currency)
            ->where('locked', '>=', $amount)
            ->decrement('locked', (float) $amount);

        return $this->findByAccountIdAndCurrency($accountId, $currency);
    }

    public function findByAccountIdAndCurrency(int $accountId, string $currency): Balance
    {
        return Balance::query()
            ->where('account_id', $accountId)
            ->where('currency', $currency)
            ->firstOrFail();
    }

    public function addFunds(int $accountId, string $currency, string $amount): Balance
    {
        Balance::query()->firstOrCreate(
            [
                'account_id' => $accountId,
                'currency' => $currency,
            ],
        );

        Balance::query()
            ->where('account_id', $accountId)
            ->where('currency', $currency)
            ->increment('available', (float) $amount);

        return $this->findByAccountIdAndCurrency($accountId, $currency);
    }
}
