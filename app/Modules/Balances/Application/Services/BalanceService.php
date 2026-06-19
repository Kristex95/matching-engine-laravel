<?php

declare(strict_types=1);

namespace App\Modules\Balances\Application\Services;

use App\Modules\Balances\Application\Exceptions\InsufficientBalanceException;
use App\Modules\Balances\Domain\Balance;
use App\Modules\Balances\Infrastructure\Repositories\BalanceRepository;
use InvalidArgumentException;
use RuntimeException;

class BalanceService
{
    public function __construct(private BalanceRepository $balanceRepository) {}

    public function deposit(int $accountId, string $currency, string $amount): Balance
    {
        if (bccomp($amount, '0', 8) <= 0) {
            throw new InvalidArgumentException('Deposit amount must be greater than zero.');
        }

        return $this->balanceRepository->addFunds($accountId, $currency, $amount);
    }

    public function placeOrderOrEscrow(int $accountId, string $currency, string $amount): Balance
    {
        $currentBalance = $this->balanceRepository->findByAccountIdAndCurrency($accountId, $currency);

        if (bccomp($currentBalance->available, $amount, 8) === -1) {
            throw new InsufficientBalanceException($accountId, $currentBalance->available, $amount);
        }

        $updatedBalance = $this->balanceRepository->lockFunds($accountId, $currency, $amount);

        return $updatedBalance;
    }

    public function cancelOrder(int $accountId, string $currency, string $amount): Balance
    {
        $currentBalance = $this->balanceRepository->findByAccountIdAndCurrency($accountId, $currency);

        if (bccomp($currentBalance->locked, $amount, 8) === -1) {
            throw new RuntimeException('Cannot release more funds than what is currently locked.');
        }

        return $this->balanceRepository->releaseFunds($accountId, $currency, $amount);
    }

    public function completeTradeOrOrder(int $accountId, string $currency, string $amount): Balance
    {
        $currentBalance = $this->balanceRepository->findByAccountIdAndCurrency($accountId, $currency);

        if (bccomp($currentBalance->locked, $amount, 8) === -1) {
            throw new RuntimeException('Cannot deduct more funds than what is currently locked.');
        }

        return $this->balanceRepository->deductLockedFunds($accountId, $currency, $amount);
    }
}
