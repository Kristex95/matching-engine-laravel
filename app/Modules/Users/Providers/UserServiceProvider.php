<?php

declare(strict_types=1);

namespace App\Modules\Users\Providers;

use App\Modules\Users\Repository\EloquentUserRepository;
use App\Modules\Users\Repository\UserRepository;
use Illuminate\Support\ServiceProvider;

class UserServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            UserRepository::class,
            EloquentUserRepository::class
        );
    }
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
    }
}
