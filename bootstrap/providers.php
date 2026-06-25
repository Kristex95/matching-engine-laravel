<?php

return [
    App\Modules\Accounts\Providers\AccountsServiceProvider::class,
    App\Modules\Auth\Providers\AuthServiceProvider::class,
    App\Modules\Balances\Providers\BalancesServiceProvider::class,
    App\Modules\Orderbook\Providers\OrderbooksServiceProvider::class,
    App\Modules\Orders\Providers\OrdersServiceProvider::class,
    App\Modules\Outbox\Providers\OutboxServiceProvider::class,
    App\Modules\Trade\Providers\TradeServiceProvider::class,
    App\Modules\Users\Providers\UserServiceProvider::class,
    App\Providers\AppServiceProvider::class,
    App\Providers\TelescopeServiceProvider::class,
    App\Providers\VoltServiceProvider::class,
    App\Modules\Shared\Providers\SharedServiceProvider::class,
];
