<?php

declare(strict_types=1);

namespace App\Modules\Orders\Providers;

use App\Modules\Orders\Application\Services\OrderService;
use App\Modules\Orders\Domain\ActiveOrder;
use App\Modules\Orders\Domain\Order;
use App\Modules\Orders\Infrastructure\ActiveOrderRepository;
use App\Modules\Orders\Infrastructure\EloquentActiveOrderRepository;
use App\Modules\Orders\Infrastructure\EloquentOrderRepository;
use App\Modules\Orders\Infrastructure\OrderRepository;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class OrdersServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(OrderService::class);

        $this->app->bind(OrderRepository::class, fn () => new EloquentOrderRepository(Order::class));

        $this->app->bind(ActiveOrderRepository::class, fn () => new EloquentActiveOrderRepository(ActiveOrder::class));
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
