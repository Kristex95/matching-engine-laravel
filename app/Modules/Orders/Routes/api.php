<?php

declare(strict_types=1);

use App\Modules\Orders\Http\Controllers\Api\V1\OrderController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'throttle:60,1'])->group(function (): void {
    Route::apiResource('orders', OrderController::class);
    Route::get('/orders-history', [OrderController::class, 'ordersHistory']);
});
