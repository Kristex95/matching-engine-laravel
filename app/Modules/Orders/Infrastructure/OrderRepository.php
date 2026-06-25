<?php

declare(strict_types=1);

namespace App\Modules\Orders\Infrastructure;

use App\Modules\Orders\Application\DTO\OrderFilterDTO;
use App\Modules\Orders\Application\DTO\OrderUpdateDTO;
use App\Modules\Orders\Application\DTO\StoreOrderDTO;
use App\Modules\Orders\Domain\Order;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface OrderRepository
{
    /**
     * @return Collection<int, Order>
     */
    public function findAll(): Collection;
    public function findById(int $id): Order;
    public function findByUuid(string $uuid): Order;
    public function findByUuidWithoutScopes(string $uuid): Order;
    /**
     * @return LengthAwarePaginator<int, Order>
     */
    public function getPaginated(OrderFilterDTO $filter): LengthAwarePaginator;
    public function storeOrder(StoreOrderDTO $dto, int $accountId): Order;
    public function updateOrder(OrderUpdateDTO $dto): Order;
    public function deleteOrderById(int $id): void;
    public function deleteOrderByUuid(string $uuid): void;
}
