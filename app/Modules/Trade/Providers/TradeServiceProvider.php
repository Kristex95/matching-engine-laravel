<?php

declare(strict_types=1);

namespace App\Modules\Trade\Providers;

use App\Modules\Trade\Console\Commands\ConsumeTradesStream;
use App\Modules\Trade\Console\Commands\RunDemoTradeWorkflow;
use App\Modules\Trade\Database\seeders\TradeSeeder;
use App\Modules\Trade\Infrastructure\Repositories\EloquentTradeRepository;
use App\Modules\Trade\Infrastructure\Repositories\TradeRepository;
use App\Modules\Trade\Livewire\Chart;
use App\Modules\Trade\Livewire\RecentTrades;
use App\Modules\Trade\Livewire\TradeHistory;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class TradeServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            TradeRepository::class,
            EloquentTradeRepository::class
        );

        $this->app->tag([TradeSeeder::class], 'domain_seeders');

        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'trades');
        Livewire::component('trades::recent-trades', RecentTrades::class);
        Livewire::component('trading.trade-history', TradeHistory::class);
        Livewire::component('trading.crypto-chart', Chart::class);
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
