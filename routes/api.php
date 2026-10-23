<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BookController;

// Menggunakan apiResource agar semua fungsi (index, store, show, update, destroy) otomatis aktif
Route::apiResource('/books', BookController::class);