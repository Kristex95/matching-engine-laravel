<?php

namespace App\Modules\Orders\Domain\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderUpdateNotificationEvent implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public int $accountId,
        public string $message
    ) {}

    public function broadcastOn(): array
    {
        return [new PrivateChannel("account.notification.{$this->accountId}")];
    }

    /**
     * Explicitly set the broadcast name to simplify front-end listeners.
     */
    public function broadcastAs(): string
    {
        return 'order.updated';
    }
}
