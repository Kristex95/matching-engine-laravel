<?php

declare(strict_types=1);

namespace App\Modules\Users\Application\DTO;

final readonly class NewUserDTO
{
    public function __construct(
        public string $name,
        public string $email,
        public string $password,
        public ?int $accountId
    ) {}

    public function withAccountId(int $accountId): NewUserDTO
    {
        return new self(
            name: $this->name,
            email: $this->email,
            password: $this->password,
            accountId: $accountId,
        );
    }
}
