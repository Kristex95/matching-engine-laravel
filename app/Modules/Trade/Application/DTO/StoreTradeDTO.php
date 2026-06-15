<?php

declare(strict_types=1);

namespace App\Modules\Trade\Application\DTO;

final readonly class StoreTradeDTO
{
    public function __construct(
        public string $takerOrderId,
        public string $makerOrderId,
        public string $amount,
        public string $price,
        public string $baseCurrency,
        public string $quoteCurrency,
    ) {}
}
