<?php

declare(strict_types=1);

namespace App\Modules\Orderbook\Providers;

use App\Modules\Orders\Application\Services\OrderService;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class OrderbooksServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(OrderService::class);
    }

    public function boot(): void
    {
        Route::middleware('api')
            ->name('api.v1.')
            ->prefix('api/v1')
            ->group(__DIR__ . '/../Routes/api.php');
    }
}
