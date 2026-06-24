<?php

declare(strict_types=1);

namespace App\Modules\Auth\Http\App\Services;

use App\Modules\Accounts\Application\Services\AccountService;
use App\Modules\Users\Application\DTO\NewUserDTO;
use App\Modules\Users\Application\Services\UserService;
use App\Modules\Users\Domain\User;
use Illuminate\Support\Facades\DB;

class AuthService
{
    public function __construct(
        private UserService $userService,
        private AccountService $accountService,
    ) {}

    /**
     * Handle the registration transaction and token generation.
     * * @param NewUserDTO $dto
     * @return array{user: \App\Modules\Users\Domain\User, account: mixed, token: string}
     */
    public function register(NewUserDTO $dto): array
    {
        ['user' => $user, 'account' => $account] = DB::transaction(function () use ($dto): array {
            $account = $this->accountService->createAccount();
            $dto = $dto->withAccountId($account->id);
            $user = $this->userService->createUser($dto);

            return [
                'user' => $user,
                'account' => $account,
            ];
        });

        $token = $user->createToken(name: 'api-token')->plainTextToken;

        return [
            'user' => $user,
            'account' => $account,
            'token' => $token,
        ];
    }

    public function registerWeb(NewUserDTO $dto): User
    {
        ['user' => $user] = DB::transaction(function () use ($dto): array {
            $account = $this->accountService->createAccount();
            $dto = $dto->withAccountId($account->id);
            $user = $this->userService->createUser($dto);

            return ['user' => $user];
        });

        return $user;
    }
}
