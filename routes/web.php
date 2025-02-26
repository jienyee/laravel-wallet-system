<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WalletController;

Route::prefix('wallet')->group(function () {
    Route::post('/deposit/{userId}', [WalletController::class, 'deposit']);
    Route::post('/withdraw/{userId}', [WalletController::class, 'withdraw']);
    Route::get('/balance/{userId}', [WalletController::class, 'balance']);
    Route::get('/transactions/{userId}', [WalletController::class, 'transactions']);
});

Route::get('/', function () {
    return view('welcome');
});
