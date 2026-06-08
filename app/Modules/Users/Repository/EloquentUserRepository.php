<?php

declare(strict_types=1);

namespace App\Modules\Users\Repository;

use App\Modules\Users\Application\DTO\NewUserDTO;
use App\Modules\Users\Domain\User;

class EloquentUserRepository implements UserRepository
{
    public function storeUser(NewUserDTO $dto): User
    {
        $user = User::query()->create([
            'name'       => $dto->name,
            'email'      => $dto->email,
            'password'   => $dto->password,
            'account_id' => $dto->accountId,
        ]);

        return $user;
    }
}
