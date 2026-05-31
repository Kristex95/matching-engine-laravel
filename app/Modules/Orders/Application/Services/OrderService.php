<?php

declare(strict_types=1);

namespace App\Modules\Orders\Application\Services;

use App\Modules\Accounts\Domain\Account;
use App\Modules\Orders\Application\DTO\OrderFilterDTO;
use App\Modules\Orders\Application\DTO\StoreOrderDTO;
use App\Modules\Orders\Domain\Order;
use App\Modules\Orders\Infrastructure\ActiveOrderRepository;
use App\Modules\Orders\Infrastructure\OrderRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderService
{
    public function __construct(
        private OrderRepository $orderRepository,
        private ActiveOrderRepository $activeOrderRepository,
    ) {}

    /**
     * @return LengthAwarePaginator<int, Order>
     */
    public function getAllActiveOrdersPaginated(OrderFilterDTO $dto): LengthAwarePaginator
    {
        return $this->activeOrderRepository->getPaginated($dto);
    }

    /**
     * @return LengthAwarePaginator<int, Order>
     */
    public function getAllOrdersPaginated(OrderFilterDTO $dto): LengthAwarePaginator
    {
        return $this->orderRepository->getPaginated($dto);
    }

    public function getById(int $id): Order
    {
        return $this->orderRepository->findById($id);
    }

    public function storeNewOrder(StoreOrderDTO $dto, Account $account): Order
    {
        $uuid = Str::uuid()->toString();
        $dto = $dto->withUuid($uuid);

        return DB::transaction(function () use ($account, $dto) {
            $order = $this->orderRepository->storeOrder($dto, $account->id);
            $this->activeOrderRepository->storeOrder($dto, $account->id);

            return $order;
        });
    }
}
