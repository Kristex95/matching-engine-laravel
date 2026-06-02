<?php

declare(strict_types=1);

namespace App\Modules\Outbox\Application\Contracts;

use App\Modules\Outbox\Domain\Models\OutboxEvent;

interface EventStreamPublisher
{
    public function publish(OutboxEvent $event, string $stream): string;
}
