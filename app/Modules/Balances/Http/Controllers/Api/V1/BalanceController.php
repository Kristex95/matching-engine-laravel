<?php

declare(strict_types=1);

namespace App\Modules\Balances\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Modules\Accounts\Application\Services\AccountService;
use App\Modules\Balances\Application\Services\BalanceService;
use App\Modules\Balances\Domain\Balance;
use App\Modules\Balances\Http\Requests\Api\V1\StoreBalanceRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class BalanceController extends Controller
{
    public function __construct(
        private BalanceService $balanceService,
        private AccountService $accountService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        return response()->json(['message' => 'Not implemented'], 501);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBalanceRequest $request): void
    {
        $dto = $request->toDto();

        $user = Auth::user();
        /** @var object{account_id:int} $user */
        $account = $this->accountService->getById($user->account_id);

        $this->balanceService->deposit($account->id, $dto->currency, $dto->amount);
    }

    /**
     * Display the specified resource.
     */
    public function show(Balance $balance): JsonResponse
    {
        return response()->json($balance);
    }
}
