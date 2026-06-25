<?php

declare(strict_types=1);

namespace App\Modules\Orders\Livewire;

use App\Modules\Orders\Application\DTO\OrderFilterDTO;
use App\Modules\Orders\Application\Services\OrderService;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class HistoryOrders extends Component
{
    use WithPagination;

    public string $symbol;

    public function mount(string $symbol = 'BTC'): void
    {
        $this->symbol = $symbol;
    }

    public function render(OrderService $orderService): View
    {
        $filterDto = new OrderFilterDTO(
            currency: $this->symbol
        );

        $orders = $orderService->getAllOrdersPaginated($filterDto);

        return view('livewire.trading.history-orders', [
            'orders' => $orders,
        ]);
    }
}
