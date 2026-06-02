<?php

declare(strict_types=1);

namespace Tests\Mocks;

use App\Modules\Outbox\Application\Contracts\EventStreamPublisher;
use App\Modules\Outbox\Domain\Models\OutboxEvent;

class NullStreamPublisher implements EventStreamPublisher
{
    /** @var list<array{stream: string, content: array<string, mixed>}> */
    public static array $published = [];

    public static function reset(): void
    {
        self::$published = [];
    }

    public function publish(OutboxEvent $event, string $stream): string
    {
        self::$published[] = ['stream' => $stream,
            'content' => [
                'outbox_id' => (string) $event->id,
                'aggregate_type' => $event->aggregate_type,
                'aggregate_id' => $event->aggregate_id,
                'event_type' => $event->event_type,
                'payload' => json_encode($event->payload),
            ]];

        return '0-0';
    }
}
