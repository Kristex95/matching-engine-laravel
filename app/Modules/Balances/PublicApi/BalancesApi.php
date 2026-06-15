<?php

declare(strict_types=1);

namespace App\Modules\Balances\PublicApi;

use App\Modules\Balances\Application\Services\BalanceService;

class BalancesApi
{
    public function __construct(
        private BalanceService $balanceService
    ) {}

    public function addFunds(int $accountId, string $currency, string $amount): BalanceResponseDto
    {
        $balance = $this->balanceService->deposit($accountId, $currency, $amount);
        return BalanceResponseDto::fromDomain($balance);
    }

    public function lockFundsForOrder(int $accountId, string $currency, string $amount): BalanceResponseDto
    {
        $balance = $this->balanceService->placeOrderOrEscrow($accountId, $currency, $amount);
        return BalanceResponseDto::fromDomain($balance);
    }

    public function releaseLockedFunds(int $accountId, string $currency, string $amount): BalanceResponseDto
    {
        $balance = $this->balanceService->cancelOrder($accountId, $currency, $amount);
        return BalanceResponseDto::fromDomain($balance);
    }

    public function confirmDeduction(int $accountId, string $currency, string $amount): BalanceResponseDto
    {
        $balance = $this->balanceService->completeTradeOrOrder($accountId, $currency, $amount);
        return BalanceResponseDto::fromDomain($balance);
    }
}
