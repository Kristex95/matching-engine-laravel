<?php

declare(strict_types=1);

namespace App\Modules\Balances\Infrastructure\Repositories;

use App\Modules\Balances\Domain\Balance;

interface BalanceRepository
{
    public function findByAccountIdAndCurrency(int $accountId, string $currency): Balance;
    public function lockFunds(int $accountId, string $currency, string $amount): Balance;
    public function releaseFunds(int $accountId, string $currency, string $amount): Balance;
    public function deductLockedFunds(int $accountId, string $currency, string $amount): Balance;
    public function addFunds(int $accountId, string $currency, string $amount): Balance;
}
