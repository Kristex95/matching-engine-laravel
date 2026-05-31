<?php

declare(strict_types=1);

namespace App\Modules\Orders\Application\DTO;

final readonly class OrderFilterDTO
{
    public function __construct(
        public ?string $orderId = null,
        public ?int $accountId = null,
        public ?string $status = null,
        public ?string $side = null,
        public ?string $type = null,
        public ?string $currency = null,
        public ?int $perPage = 20,
    ) {}
}
