<?php

return [
    App\Modules\Accounts\Providers\AccountsServiceProvider::class,
    App\Modules\Auth\Providers\AuthServiceProvider::class,
    App\Modules\Balances\Providers\BalancesServiceProvider::class,
    App\Modules\Orders\Providers\OrdersServiceProvider::class,
    App\Providers\AppServiceProvider::class,
    App\Providers\TelescopeServiceProvider::class,
    App\Modules\Outbox\Providers\OutboxServiceProvider::class,
    App\Modules\Users\Providers\UserServiceProvider::class,
];
