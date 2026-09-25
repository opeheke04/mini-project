<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CashierController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    $totalProducts = \App\Models\Product::count();
    $activeProducts = \App\Models\Product::where('is_active', true)->count();
    $inactiveProducts = \App\Models\Product::where('is_active', false)->count();
    $stockProducts = \App\Models\Product::sum('stock');

    return view('dashboard', compact(
        'totalProducts',
        'activeProducts',
        'inactiveProducts',
        'stockProducts'
    ));
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/cashier', [CashierController::class, 'index'])->name('cashier.index');
    Route::resource('products', ProductController::class);

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
