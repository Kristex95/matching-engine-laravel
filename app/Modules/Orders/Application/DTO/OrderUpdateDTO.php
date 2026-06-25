<?php

declare(strict_types=1);

namespace App\Modules\Orders\Application\DTO;

final readonly class OrderUpdateDTO
{
    public function __construct(
        public string $uuid,
        public ?string $status,
        public ?string $filledAmount,
    ) {}
}
