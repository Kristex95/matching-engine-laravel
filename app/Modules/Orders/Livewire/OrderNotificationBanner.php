<?php

namespace App\Modules\Orders\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class OrderNotificationBanner extends Component
{
    public ?string $message = null;

    public function getListeners(): array
    {
        $user = Auth::user();

        if (! $user) {
            return [];
        }

        $accountId = $user->account_id;

        return [
            "echo-private:account.notification.{$accountId},.order.updated" => 'showBanner',
        ];
    }

    public function showBanner(array $event): void
    {
        $this->message = $event['message'];
    }

    public function clearBanner(): void
    {
        $this->message = null;
    }

    public function render()
    {
        return view('orders::livewire.order-notification-banner');
    }
}
