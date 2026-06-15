<?php

declare(strict_types=1);

namespace App\Modules\Balances\Application\DTO;

final readonly class BalanceDTO
{
    public function __construct(
        public ?int   $accountId,
        public string $currency,
        public string $amount,
    ) {}

    public function withAccountId(int $accountId): self
    {
        return new self(
            accountId: $accountId,
            currency:  $this->currency,
            amount:    $this->amount
        );
    }
}
