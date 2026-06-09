<?php

declare(strict_types=1);

namespace App\Modules\Orders\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Modules\Accounts\Application\Services\AccountService;
use App\Modules\Orders\Application\Services\OrderService;
use App\Modules\Orders\Domain\Order;
use App\Modules\Orders\Http\Requests\Api\V1\ListOrdersFilteredRequest;
use App\Modules\Orders\Http\Requests\Api\V1\StoreOrderRequest;
use App\Modules\Orders\Http\Resources\Api\V1\OrderResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function __construct(
        private OrderService $orderService,
        private AccountService $accountService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(ListOrdersFilteredRequest $request): JsonResponse
    {
        $dto = $request->toDto();

        $orders = $this->orderService->getAllActiveOrdersPaginated($dto);

        return OrderResource::collection($orders)
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOrderRequest $request): JsonResponse
    {
        $user = Auth::user();
        /** @var object{account_id:int} $user */
        $account = $this->accountService->getById($user->account_id);

        $order = $this->orderService->storeNewOrder($request->toDto(), $account);

        return (new OrderResource($order))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): JsonResponse
    {
        $order = $this->orderService->getById($id);

        return (new OrderResource($order))
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Order $order): void
    {
        // TODO
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order): void
    {
        // TODO
    }

    public function ordersHistory(ListOrdersFilteredRequest $request): JsonResponse
    {
        $dto = $request->toDto();

        $orders = $this->orderService->getAllOrdersPaginated($dto);

        return OrderResource::collection($orders)
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }
}
