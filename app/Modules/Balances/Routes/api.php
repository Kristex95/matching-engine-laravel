<?php

declare(strict_types=1);

use App\Modules\Balances\Http\Controllers\Api\V1\BalanceController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'throttle:60,1'])
    ->prefix('balances')
    ->group(function (): void {
        Route::post('/', [BalanceController::class, 'store']);
    });
