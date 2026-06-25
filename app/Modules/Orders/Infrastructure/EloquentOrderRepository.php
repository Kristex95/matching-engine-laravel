<?php

declare(strict_types=1);

namespace App\Modules\Orders\Infrastructure;

use App\Modules\Orders\Application\DTO\OrderFilterDTO;
use App\Modules\Orders\Application\DTO\OrderUpdateDTO;
use App\Modules\Orders\Application\DTO\StoreOrderDTO;
use App\Modules\Orders\Domain\Order;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

/**
 * @template TModel of Order
 */
class EloquentOrderRepository implements OrderRepository
{
    /**
     * @param class-string<TModel> $modelClass
     */
    public function __construct(
        private string $modelClass
    ) {}

    /**
     * @return Builder<TModel>
     */
    private function query(): Builder
    {
        /** @var Builder<TModel> */
        return (new $this->modelClass())->newQuery();
    }

    /**
     * @return Collection<int, TModel>
     */
    public function findAll(): Collection
    {
        /** @var Collection<int, TModel> */
        return $this->query()->get();
    }

    public function findById(int $id): Order
    {
        return $this->query()->findOrFail($id);
    }

    public function findByUuid(string $uuid): Order
    {
        return $this->query()
            ->where('uuid', $uuid)
            ->firstOrFail();
    }

    public function findByUuidWithoutScopes(string $uuid): Order
    {
        return $this->query()
            ->withoutGlobalScopes()
            ->where('uuid', $uuid)
            ->firstOrFail();
    }

    /**
     * @return LengthAwarePaginator<int, TModel>
     */
    public function getPaginated(OrderFilterDTO $filter): LengthAwarePaginator
    {
        $query = $this->query()
            ->orderByDesc('created_at');

        if ($filter->orderId !== null) {
            $query->where('uuid', $filter->orderId);
        }

        if ($filter->accountId !== null) {
            $query->where('account_id', $filter->accountId);
        }

        if ($filter->side !== null) {
            $query->where('side', $filter->side);
        }

        if ($filter->type !== null) {
            $query->where('type', $filter->type);
        }

        if ($filter->currency !== null) {
            $query->where('currency', $filter->currency);
        }

        if ($filter->status !== null) {
            $query->where('status', $filter->status);
        }

        /** @var LengthAwarePaginator<int, TModel> */
        return $query->paginate($filter->perPage)
            ->appends(request()->query());
    }

    /**
     * @return TModel
     */
    public function storeOrder(StoreOrderDTO $dto, int $accountId): Order
    {
        /** @var TModel */
        return $this->query()->forceCreate([
            'uuid' => $dto->uuid,
            'account_id' => $accountId,
            'side' => $dto->side,
            'type' => $dto->type,
            'currency' => $dto->currency,
            'price' => $dto->price,
            'amount' => $dto->amount,
            'filled_amount' => 0,
            'status' => 'new',
        ]);
    }

    public function updateOrder(OrderUpdateDTO $dto): Order
    {
        $order = $this->query()
            ->withoutGlobalScopes()
            ->where('uuid', $dto->uuid)
            ->firstOrFail();

        $updateData = [];

        if ($dto->status !== null) {
            $updateData['status'] = $dto->status;
        }

        if ($dto->filledAmount !== null) {
            $order->increment('filled_amount', (float) $dto->filledAmount);
        }

        if (!empty($updateData)) {
            $order->update($updateData);
        }

        return $order;
    }

    public function deleteOrderById(int $id): void
    {
        $this->query()
            ->withoutGlobalScopes()
            ->where('id', $id)
            ->delete();
    }

    public function deleteOrderByUuid(string $uuid): void
    {
        $this->query()
            ->withoutGlobalScopes()
            ->where('uuid', $uuid)
            ->delete();
    }
}
