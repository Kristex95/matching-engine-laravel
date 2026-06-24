<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::get('trade', function () {
    return redirect()->route('trade', ['currency' => 'BTC']);
});

Route::get('trade/{currency}', function ($currency) {
    return view('trade', ['currency' => $currency]);
})->name('trade');

require __DIR__.'/auth.php';
