<?php

declare(strict_types=1);

namespace App\Modules\Balances\Application\Exceptions;

use Exception;

class InsufficientBalanceException extends Exception
{
    public function __construct(
        public int $accountId,
        public string $available,
        public string $amount,
    ) {
        parent::__construct(
            "Insufficient balance for account: {$accountId}. Available: {$available}, amount: {$amount}",
            400
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function context(): array
    {
        return [
            'account_id' => $this->accountId,
        ];
    }
}
