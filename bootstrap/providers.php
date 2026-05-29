<?php

use App\Modules\Accounts\Providers\AccountsServiceProvider;
use App\Modules\Auth\Providers\AuthServiceProvider;
use App\Modules\Balances\Providers\BalanceServiceProvider;
use App\Providers\AppServiceProvider;

return [
    AppServiceProvider::class,
    AuthServiceProvider::class,
    AccountsServiceProvider::class,
    BalanceServiceProvider::class,
];
