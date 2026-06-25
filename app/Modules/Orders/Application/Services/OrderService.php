<?php

declare(strict_types=1);

namespace App\Modules\Orders\Application\Services;

use App\Modules\Accounts\Domain\Account;
use App\Modules\Balances\Domain\Currency;
use App\Modules\Balances\PublicApi\BalancesApi;
use App\Modules\Orders\Application\DTO\OrderFilterDTO;
use App\Modules\Orders\Application\DTO\OrderUpdateDTO;
use App\Modules\Orders\Application\DTO\StoreOrderDTO;
use App\Modules\Orders\Domain\Order;
use App\Modules\Orders\Infrastructure\ActiveOrderRepository;
use App\Modules\Orders\Infrastructure\OrderRepository;
use App\Modules\Outbox\Application\OutboxApi;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderService
{
    public function __construct(
        private OrderRepository $orderRepository,
        private ActiveOrderRepository $activeOrderRepository,
        private BalancesApi $balanceApi,
        private OutboxApi $outbox,
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

    public function getByUuid(string $uuid): Order
    {
        return $this->orderRepository->findByUuid($uuid);
    }

    public function getByUuidForTrading(string $uuid): Order
    {
        return $this->orderRepository->findByUuidWithoutScopes($uuid);
    }

    public function storeNewOrder(StoreOrderDTO $dto, Account $account): Order
    {
        $uuid = Str::uuid()->toString();
        $dto = $dto->withUuid($uuid);

        if ($dto->side === 'buy') {
            $lockCurrency = Currency::USDT->value;
            $lockAmount = bcmul($dto->amount, $dto->price ?? '0', 8);
        } else {
            // For a SELL order, lock the asset they are selling (e.g., BTC)
            $lockCurrency = $dto->currency;
            $lockAmount = $dto->amount;
        }

        return DB::transaction(function () use ($account, $dto, $lockCurrency, $lockAmount) {
            $this->balanceApi->lockFundsForOrder(
                accountId: $account->id,
                currency: $lockCurrency,
                amount: $lockAmount
            );
            $order = $this->orderRepository->storeOrder($dto, $account->id);
            $this->activeOrderRepository->storeOrder($dto, $account->id);

            $this->outbox->record(
                aggregateType: 'Order',
                aggregateId: $order->uuid,
                eventType: 'order-created',
                payload: [
                    'order_id' => $order->uuid,
                    'side'     => $order->side,
                    'type'     => $order->type,
                    'currency' => $order->currency,
                    'price'    => $order->price,
                    'amount'   => $order->amount,
                ]
            );

            return $order;
        });
    }

    public function processOrderUpdate(OrderUpdateDTO $dto): void
    {
        DB::transaction(function () use ($dto): void {
            if ($dto->status === "filled" || $dto->status === "cancelled") {
                $this->activeOrderRepository->deleteOrderByUuid($dto->uuid);
                $this->orderRepository->updateOrder($dto);
            } elseif ($dto->status === "partially_filled") {
                $this->activeOrderRepository->updateOrder($dto);
                $this->orderRepository->updateOrder($dto);
            } elseif ($dto->status === "cancelled") {
                $this->activeOrderRepository->deleteOrderByUuid($dto->uuid);
                $this->orderRepository->updateOrder($dto);
            }
        });
    }
}
