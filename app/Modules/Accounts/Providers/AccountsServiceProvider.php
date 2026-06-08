<?php

declare(strict_types=1);

namespace App\Modules\Accounts\Providers;

use App\Modules\Accounts\Infrastructure\Repositories\AccountRepository;
use App\Modules\Accounts\Infrastructure\Repositories\EloquentAccountRepository;
use Illuminate\Support\ServiceProvider;

class AccountsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            AccountRepository::class,
            EloquentAccountRepository::class
        );
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
    }
}
