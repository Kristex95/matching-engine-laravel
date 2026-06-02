<?php

declare(strict_types=1);

namespace App\Modules\Outbox\Console\Commands;

use App\Modules\Outbox\Application\Contracts\EventStreamPublisher;
use App\Modules\Outbox\Domain\Models\OutboxEvent;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class OutboxDrainCommand extends Command
{
    protected $signature = 'outbox:drain';

    protected $description = 'Dispatch to drain pending outbox events';

    private int $batchLimit = 50;

    public function __construct(
        private EventStreamPublisher $publisher
    ) {
        parent::__construct();
    }

    public function handle(): void
    {
        DB::transaction(function (): void {
            $events = OutboxEvent::query()
                ->whereNull('processed_at')
                ->orderBy('id')
                ->limit($this->batchLimit)
                ->lockForUpdate()
                ->get();

            foreach ($events as $event) {
                try {
                    $this->publisher->publish($event, 'matching-stream');
                } catch (Throwable $e) {
                    Log::error('Outbox recovery drain failed for event', [
                        'id' => $event->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        });
    }
}
