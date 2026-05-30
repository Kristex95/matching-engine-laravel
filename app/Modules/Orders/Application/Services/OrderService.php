<?php

declare(strict_types=1);

namespace App\Modules\Orders\Application\Services;

use App\Modules\Orders\Application\DTO\OrderFilterDTO;
use App\Modules\Orders\Domain\Order;
use App\Modules\Orders\Infrastructure\OrderRepository;
use Illuminate\Pagination\LengthAwarePaginator;

class OrderService
{
    public function __construct(private OrderRepository $orderRepository) {}

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
}
