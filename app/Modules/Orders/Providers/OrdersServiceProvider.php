<?php

declare(strict_types=1);

namespace App\Modules\Orders\Providers;

use App\Modules\Orders\Application\Services\OrderService;
use App\Modules\Orders\Console\Commands\ConsumeOrderUpdatesStream;
use App\Modules\Orders\Domain\ActiveOrder;
use App\Modules\Orders\Domain\Order;
use App\Modules\Orders\Infrastructure\ActiveOrderRepository;
use App\Modules\Orders\Infrastructure\EloquentActiveOrderRepository;
use App\Modules\Orders\Infrastructure\EloquentOrderRepository;
use App\Modules\Orders\Infrastructure\OrderRepository;
use App\Modules\Orders\Livewire\HistoryOrders;
use App\Modules\Orders\Livewire\OpenOrders;
use App\Modules\Orders\Livewire\OrderForm;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class OrdersServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(OrderService::class);

        $this->app->bind(OrderRepository::class, fn () => new EloquentOrderRepository(Order::class));

        $this->app->bind(ActiveOrderRepository::class, fn () => new EloquentActiveOrderRepository(ActiveOrder::class));

        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'orders');
        Livewire::component('orders::form', OrderForm::class);
        Livewire::component('trading.open-orders', OpenOrders::class);
        Livewire::component('trading.history-orders', HistoryOrders::class);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');

        Route::middleware('api')
            ->name('api.v1.')
            ->prefix('api/v1')
            ->group(__DIR__ . '/../Routes/api.php');

        $this->commands([
            ConsumeOrderUpdatesStream::class,
        ]);
    }
}
