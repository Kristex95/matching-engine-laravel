<?php

declare(strict_types=1);

use App\Modules\Orderbook\Http\Controllers\Api\V1\OrderbookController;
use Illuminate\Support\Facades\Route;

Route::middleware(['api', 'throttle:120,1'])->group(function (): void {
    Route::get("/orderbook/{symbol}", [OrderbookController::class, 'show']);
});
