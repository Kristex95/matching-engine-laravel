<?php

declare(strict_types=1);

namespace App\Modules\Orders\Infrastructure;

use App\Modules\Orders\Application\DTO\OrderFilterDTO;
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
    /**
     * @return LengthAwarePaginator<int, Order>
     */
    public function getPaginated(OrderFilterDTO $filter): LengthAwarePaginator;
}
