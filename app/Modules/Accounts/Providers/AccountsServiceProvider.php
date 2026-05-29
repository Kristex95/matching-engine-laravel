<?php

declare(strict_types=1);

namespace App\Modules\Accounts\Providers;

use Illuminate\Support\ServiceProvider;

class AccountsServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
    }
}
