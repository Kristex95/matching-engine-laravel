<?php

declare(strict_types=1);

namespace App\Modules\Accounts\Infrastructure\Repositories;

use App\Modules\Accounts\Domain\Account;

interface AccountRepository
{
    public function newAccount(): Account;
}
