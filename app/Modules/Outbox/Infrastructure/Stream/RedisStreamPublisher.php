<?php

declare(strict_types=1);

namespace App\Modules\Outbox\Infrastructure\Stream;

use App\Modules\Outbox\Application\Contracts\EventStreamPublisher;
use App\Modules\Outbox\Domain\Models\OutboxEvent;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Throwable;

class RedisStreamPublisher implements EventStreamPublisher
{
    public function publish(OutboxEvent $event, string $stream): string
    {
        try {
            $xadd = Redis::connection('streams')->xadd(
                $stream,
                '*',
                [
                    'outbox_id' => (string) $event->id,
                    'aggregate_type' => $event->aggregate_type,
                    'aggregate_id' => $event->aggregate_id,
                    'event_type' => $event->event_type,
                    'payload' => json_encode($event->payload),
                ]
            );

            $event->update([
                'processed_at' => now(),
            ]);
            return $xadd;
        } catch (Throwable $e) {
            $event->increment('attempts');

            Log::error('Publishing failed', [
                'event_id' => $event->id,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
