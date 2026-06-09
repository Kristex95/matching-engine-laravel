<?php

declare(strict_types=1);

namespace App\Modules\Accounts\Infrastructure\Repositories;

use App\Modules\Accounts\Domain\Account;

class EloquentAccountRepository implements AccountRepository
{
    public function findById(int $id): Account
    {
        return Account::query()->find($id);
    }

    public function newAccount(): Account
    {
        $account = Account::factory()->create();
        return $account;
    }
}
