<?php

declare(strict_types=1);

namespace App\Modules\Balances\Providers;

use App\Modules\Balances\Infrastructure\Repositories\BalanceRepository;
use App\Modules\Balances\Infrastructure\Repositories\EloquentBalanceRepository;
use App\Modules\Balances\Livewire\BalancesTab;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class BalancesServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            BalanceRepository::class,
            EloquentBalanceRepository::class
        );

        Livewire::component('trading.balances', BalancesTab::class);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');

        Route::middleware('api')
            ->name('api.v1.')
            ->prefix('api/v1')
            ->group(__DIR__ . '/../Routes/api.php');
    }
}
