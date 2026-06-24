<?php

declare(strict_types=1);

namespace App\Modules\Orderbook\Providers;

use App\Modules\Orderbook\Livewire\OrderbookTracker;
use App\Modules\Orders\Application\Services\OrderService;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class OrderbooksServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(OrderService::class);

        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'orderbook');

        Livewire::component('orderbook::tracker', OrderbookTracker::class);
    }

    public function boot(): void
    {
        Route::middleware('api')
            ->name('api.v1.')
            ->prefix('api/v1')
            ->group(__DIR__ . '/../Routes/api.php');
    }
}
