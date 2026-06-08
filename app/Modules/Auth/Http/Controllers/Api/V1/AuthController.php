<?php

declare(strict_types=1);

namespace App\Modules\Auth\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Modules\Accounts\Application\Services\AccountService;
use App\Modules\Auth\Http\Requests\Api\V1\RegisterRequest;
use App\Modules\Users\Application\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function __construct(
        private UserService $userService,
        private AccountService $accountService,
    ) {}

    public function register(RegisterRequest $request): JsonResponse
    {
        $dto = $request->toDto();

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

        return response()->json([
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ],
                'account' => [
                    'id' => $account->id,
                    'account_number' => $account->account_number,
                ],
                'token' => $token,
            ],
        ], Response::HTTP_CREATED);
    }
}
