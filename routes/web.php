<?php

use App\Http\Controllers\CashierController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return auth()->user()->role === 'admin'
        ? redirect()->route('admin.dashboard')
        : redirect()->route('cashier.index');
})->middleware('auth')->name('dashboard');

Route::get('/admin/dashboard', function () {
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
})->middleware('admin')->name('admin.dashboard');

Route::get('/admin', function () {
    return "Selamat datang di halaman admin!";
})->middleware('admin')->name('admin.middleware-demo');

Route::get('/role-check', function () {
    return 'Akses kasir diberikan.';
})->middleware('cek.role:kasir')->name('role-check');

Route::middleware(['auth', 'cek.role:admin,kasir'])->group(function () {
    Route::get('/cashier', [CashierController::class, 'index'])->name('cashier.index');
    Route::post('/cashier/transactions', [CashierController::class, 'store'])->name('cashier.transactions.store');
});

Route::middleware(['auth', 'admin'])->group(function () {
    Route::resource('products', ProductController::class);
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
