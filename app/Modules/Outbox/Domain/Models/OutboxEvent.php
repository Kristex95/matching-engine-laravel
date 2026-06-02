<?php

declare(strict_types=1);

namespace App\Modules\Outbox\Domain\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $aggregate_type
 * @property int $aggregate_id
 * @property string $event_type
 * @property array<string, mixed> $payload
 * @property int $attempts
 * @property \Illuminate\Support\Carbon|null $processed_at
 */
class OutboxEvent extends Model
{
    protected $table = 'outbox_events';

    protected $fillable = [
        'aggregate_type',
        'aggregate_id',
        'event_type',
        'payload',
        'attempts',
        'processed_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'processed_at' => 'datetime',
        'attempts' => 'integer',
    ];
}
