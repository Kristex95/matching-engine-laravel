<?php

declare(strict_types=1);

namespace App\Modules\Outbox\Providers;

use App\Modules\Outbox\Application\Contracts\EventStreamPublisher;
use App\Modules\Outbox\Application\OutboxApi;
use App\Modules\Outbox\Console\Commands\OutboxDrainCommand;
use App\Modules\Outbox\Infrastructure\Stream\RedisStreamPublisher;
use Illuminate\Support\ServiceProvider;

class OutboxServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(OutboxApi::class);

        $this->app->bind(EventStreamPublisher::class, RedisStreamPublisher::class);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');

        $this->commands([
            OutboxDrainCommand::class,
        ]);
    }
}
