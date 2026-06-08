<?php

declare(strict_types=1);

namespace App\Modules\Users\Repository;

use App\Modules\Users\Application\DTO\NewUserDTO;
use App\Modules\Users\Domain\User;

interface UserRepository
{
    public function storeUser(NewUserDTO $dto): User;
}
