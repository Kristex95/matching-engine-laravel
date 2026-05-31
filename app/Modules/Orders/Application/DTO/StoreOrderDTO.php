<?php

declare(strict_types=1);

namespace App\Modules\Orders\Application\DTO;

final readonly class StoreOrderDTO
{
    public function __construct(
        public string $side,
        public string $type,
        public string $currency,
        public string $amount,
        public ?string $price = null,
        public ?string $uuid = null,
    ) {}

    /**
     * Returns a copy of the DTO with the specified UUID.
     */
    public function withUuid(string $uuid): self
    {
        return new self(
            side: $this->side,
            type: $this->type,
            currency: $this->currency,
            price: $this->price,
            amount: $this->amount,
            uuid: $uuid
        );
    }
}
