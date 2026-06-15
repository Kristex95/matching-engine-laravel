<?php

declare(strict_types=1);

namespace App\Modules\Outbox\Application;

use App\Modules\Outbox\Application\Contracts\EventStreamPublisher;
use App\Modules\Outbox\Domain\Models\OutboxEvent;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class OutboxApi
{
    public function __construct(
        private EventStreamPublisher $publisher
    ) {}

    /**
     * @param array<string, mixed> $payload
     */
    public function record(
        string $aggregateType,
        string $aggregateId,
        string $eventType,
        array $payload,
    ): void {
        $event = OutboxEvent::query()->create([
            'aggregate_type' => $aggregateType,
            'aggregate_id' => $aggregateId,
            'event_type' => $eventType,
            'payload' => $payload,
        ]);

        $publish = function () use ($event): void {
            try {
                $this->publisher->publish($event, 'matching-stream');
            } catch (Throwable $e) {
                Log::error('Outbox fast-track to Redis failed. Falling back to DB queue.', [
                    'event_id' => $event->id,
                    'error' => $e->getMessage(),
                ]);
            }
        };

        if (DB::transactionLevel() > 0) {
            DB::afterCommit($publish);
            return;
        }

        $publish();
    }

    public function pendingCount(): int
    {
        return OutboxEvent::query()->whereNull('processed_at')->count();
    }
}
