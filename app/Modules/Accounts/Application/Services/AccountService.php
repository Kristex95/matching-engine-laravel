<?php

declare(strict_types=1);

namespace App\Modules\Accounts\Application\Services;

use App\Modules\Accounts\Domain\Account;
use App\Modules\Accounts\Infrastructure\Repositories\AccountRepository;

class AccountService
{
    public function __construct(
        private AccountRepository $accountRepository,
    ) {}

    public function getById(int $id): Account
    {
        return $this->accountRepository->findById($id);
    }

    public function createAccount(): Account
    {
        $account = $this->accountRepository->newAccount();
        return $account;
    }
}
