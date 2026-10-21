<?php

use App\Http\Controllers\CashierController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CashierAccountController;
use App\Http\Controllers\SalesReportController;
use App\Http\Controllers\StrukController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return auth()->user()->role === 'admin'
        ? redirect()->route('admin.dashboard')
        : redirect()->route('cashier.index');
})->middleware('auth')->name('dashboard');

Route::get('/admin/dashboard', AdminDashboardController::class)->middleware('admin')->name('admin.dashboard');

Route::get('/admin', function () {
    return "Selamat datang di halaman admin!";
})->middleware('admin')->name('admin.middleware-demo');

Route::get('/role-check', function () {
    return 'Akses kasir diberikan.';
})->middleware('cek.role:kasir')->name('role-check');

Route::get('/struk/{sale?}', [StrukController::class, 'struk'])->name('struk');

Route::middleware(['auth', 'cek.role:admin,kasir'])->group(function () {
    Route::get('/cashier', [CashierController::class, 'index'])->name('cashier.index');
    Route::post('/cashier/transactions', [CashierController::class, 'store'])->name('cashier.transactions.store');
    Route::get('/cashier/receipt/{sale}', [StrukController::class, 'print'])->name('cashier.receipt.print');
    Route::get('/cashier/receipt-preview/{sale}', [StrukController::class, 'preview'])->name('cashier.receipt.preview');
});

Route::middleware(['auth', 'admin'])->group(function () {
    Route::resource('products', ProductController::class);
    Route::get('/admin/cashier-accounts', [CashierAccountController::class, 'index'])->name('admin.cashier-accounts.index');
    Route::post('/admin/cashier-accounts', [CashierAccountController::class, 'store'])->name('admin.cashier-accounts.store');
    Route::get('/admin/store-settings', [\App\Http\Controllers\StoreSettingsController::class, 'index'])->name('admin.store-settings.index');
    Route::post('/admin/store-settings', [\App\Http\Controllers\StoreSettingsController::class, 'update'])->name('admin.store-settings.update');
    Route::post('/admin/store-settings/test-print', [\App\Http\Controllers\StoreSettingsController::class, 'testPrint'])->name('admin.store-settings.test-print');
    Route::get('/sales', [SalesReportController::class, 'index'])->name('sales.index');
    Route::get('/sales/{sale}', [SalesReportController::class, 'show'])->name('sales.show');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
