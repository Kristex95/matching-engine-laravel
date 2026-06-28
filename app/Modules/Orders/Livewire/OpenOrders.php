<?php

declare(strict_types=1);

namespace App\Modules\Orders\Livewire;

use App\Modules\Orders\Application\DTO\OrderFilterDTO;
use App\Modules\Orders\Application\Services\OrderService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class OpenOrders extends Component
{
    use WithPagination;

    public string $symbol;

    public function getListeners(): array
    {
        $user = Auth::user();

        if (! $user) {
            return [];
        }

        $accountId = $user->account_id;

        return [
            "echo-private:account.notification.{$accountId},.order.updated" => '$refresh',
        ];
    }

    public function mount(): void {}

    public function render(OrderService $orderService): View
    {
        $orders = $orderService->getAllActiveOrdersPaginated(new OrderFilterDTO());

        return view('livewire.trading.open-orders', [
            'orders' => $orders,
        ]);
    }

    public function cancelOrder(string $uuid, OrderService $orderService): void
    {
        $orderService->cancelOrder($uuid);
    }
}
