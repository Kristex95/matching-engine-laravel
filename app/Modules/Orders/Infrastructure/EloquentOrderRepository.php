<?php

declare(strict_types=1);

namespace App\Modules\Orders\Infrastructure;

use App\Modules\Orders\Application\DTO\OrderFilterDTO;
use App\Modules\Orders\Domain\Order;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class EloquentOrderRepository implements OrderRepository
{
    /**
     * @return Collection<int, Order>
     */
    public function findAll(): Collection
    {
        return Order::all();
    }

    public function findById(int $id): Order
    {
        return Order::query()->findOrFail($id);
    }

    /**
     * @return LengthAwarePaginator<int, Order>
     */
    public function getPaginated(OrderFilterDTO $filter): LengthAwarePaginator
    {
        $query = Order::query()
            ->orderByDesc('created_at');

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

        return $query->paginate($filter->perPage)
            ->appends(request()->query());
    }
}
