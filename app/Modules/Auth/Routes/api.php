<?php

declare(strict_types=1);

use App\Modules\Auth\Http\Controllers\Api\V1\AuthController;
use Illuminate\Support\Facades\Route;

Route::middleware('throttle:60,1')->group(function (): void {
    Route::post('/register', [AuthController::class, 'register'])
        ->name('register');
    Route::post('/login', [AuthController::class, 'login'])
        ->name('login');
});
