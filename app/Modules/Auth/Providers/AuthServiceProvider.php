<?php

declare(strict_types=1);

namespace App\Modules\Auth\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    public function register(): void {}
    public function boot(): void
    {
        Route::middleware('api')
            ->name('api.v1.')
            ->prefix('api/v1')
            ->group(__DIR__ . '/../Routes/api.php');
    }
}
