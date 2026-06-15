<?php

declare(strict_types=1);

namespace App\Modules\Trade\Providers;

use App\Modules\Trade\Console\Commands\ConsumeTradesStream;
use App\Modules\Trade\Console\Commands\RunDemoTradeWorkflow;
use App\Modules\Trade\Infrastructure\Repositories\EloquentTradeRepository;
use App\Modules\Trade\Infrastructure\Repositories\TradeRepository;
use Illuminate\Support\ServiceProvider;

class TradeServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            TradeRepository::class,
            EloquentTradeRepository::class
        );
    }
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');

        $this->commands([
            ConsumeTradesStream::class,
            RunDemoTradeWorkflow::class,
        ]);
    }
}
