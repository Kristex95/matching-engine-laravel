<?php

use App\Modules\Accounts\Providers\AccountsServiceProvider;
use App\Modules\Auth\Providers\AuthServiceProvider;
use App\Modules\Balances\Providers\BalancesServiceProvider;
use App\Modules\Orders\Providers\OrdersServiceProvider;
use App\Providers\AppServiceProvider;

return [
    AppServiceProvider::class,
    AuthServiceProvider::class,
    AccountsServiceProvider::class,
    BalancesServiceProvider::class,
    OrdersServiceProvider::class,
];
