<?php

declare(strict_types=1);

namespace App\Modules\Balances\PublicApi;

use App\Modules\Balances\Domain\Balance;

readonly class BalanceResponseDto
{
    public function __construct(
        public string $currency,
        public string $available,
        public string $locked
    ) {}

    public static function fromDomain(Balance $balance): self
    {
        return new self(
            currency: $balance->currency,
            available: $balance->available,
            locked: $balance->locked
        );
    }
}
