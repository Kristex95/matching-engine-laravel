<?php

declare(strict_types=1);

namespace App\Modules\Users\Application\Services;

use App\Modules\Users\Application\DTO\NewUserDTO;
use App\Modules\Users\Domain\User;
use App\Modules\Users\Repository\UserRepository;

class UserService
{
    public function __construct(
        private UserRepository $userRepository,
    ) {}

    public function createUser(NewUserDTO $dto): User
    {
        $user = $this->userRepository->storeUser($dto);
        return $user;
    }
}
