<?php

use App\Http\Controllers\Api\BookController;
use App\Http\Controllers\Api\MobileAuthController;
use App\Http\Controllers\Api\MobileInventoryController;
use Illuminate\Support\Facades\Route;

// Menggunakan apiResource agar semua fungsi (index, store, show, update, destroy) otomatis aktif
Route::apiResource('/books', BookController::class);

Route::prefix('mobile')->group(function () {
    Route::post('/login', [MobileAuthController::class, 'login'])->middleware('throttle:5,1');

    Route::middleware(['auth:sanctum', 'admin'])->group(function () {
        Route::get('/me', [MobileAuthController::class, 'me'])->middleware('abilities:inventory:read');
        Route::post('/logout', [MobileAuthController::class, 'logout']);
        Route::get('/products', [MobileInventoryController::class, 'products'])->middleware('abilities:inventory:read');
        Route::get('/restocks', [MobileInventoryController::class, 'restocks'])->middleware('abilities:inventory:read');
        Route::post('/restocks', [MobileInventoryController::class, 'storeRestock'])->middleware('abilities:inventory:write');
    });
});
