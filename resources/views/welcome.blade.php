<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-slate-100 text-slate-800 antialiased">
        <div class="min-h-screen bg-[radial-gradient(circle_at_top,_rgba(191,219,254,0.45),_rgba(248,250,252,1)_35%,_rgba(226,232,240,1)_100%)]">
            <header class="mx-auto max-w-7xl px-4 pt-6 sm:px-6 lg:px-8">
                <nav class="glass-panel flex items-center justify-between px-4 py-3 sm:px-6">
                    <div class="flex items-center gap-3">
                        <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-600 to-indigo-600 text-white shadow-[0_12px_20px_rgba(37,99,235,0.3)]">
                            <x-application-logo class="h-6 w-6 fill-current" />
                        </div>
                        <div>
                            <p class="text-[10px] font-bold uppercase tracking-[0.24em] text-blue-600">Profile Prodi</p>
                            <h1 class="text-lg font-black text-slate-800">Product Management</h1>
                        </div>
                    </div>

                    @if (Route::has('login'))
                        <div class="flex items-center gap-3">
                            @auth
                                <a href="{{ route('dashboard') }}" class="btn-secondary">Dashboard</a>
                                <a href="{{ route('products.index') }}" class="btn-primary">Products</a>
                            @else
                                <a href="{{ route('login') }}" class="btn-secondary">Login</a>
                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="btn-primary">Register</a>
                                @endif
                            @endauth
                        </div>
                    @endif
                </nav>
            </header>

            <main class="mx-auto flex max-w-7xl flex-col gap-10 px-4 py-12 sm:px-6 lg:flex-row lg:items-center lg:justify-between lg:px-8">
                <div class="max-w-xl">
                    <span class="soft-label">Inventory System</span>
                    <h2 class="mt-5 text-4xl font-black leading-tight text-slate-900 sm:text-5xl">
                        Kelola produk dengan lebih efisien.
                    </h2>
                    <p class="mt-5 text-lg text-slate-600">
                        Pantau stok, lihat status produk, dan kelola katalog Anda dari satu dashboard yang rapi dan profesional.
                    </p>

                    <div class="mt-8 flex flex-wrap gap-3">
                        @auth
                            <a href="{{ route('dashboard') }}" class="btn-primary">Masuk Dashboard</a>
                            <a href="{{ route('products.index') }}" class="btn-secondary">Lihat Produk</a>
                        @else
                            <a href="{{ route('login') }}" class="btn-primary">Login Sekarang</a>
                            <a href="{{ route('register') }}" class="btn-secondary">Daftar</a>
                        @endif
                    </div>

                    @php
                        $productTableExists = \Illuminate\Support\Facades\Schema::hasTable('products');
                        $productCount = $productTableExists ? \App\Models\Product::count() : 0;
                        $activeProductCount = $productTableExists ? \App\Models\Product::where('is_active', true)->count() : 0;
                        $stockTotal = $productTableExists ? \App\Models\Product::sum('stock') : 0;
                    @endphp

                    <div class="mt-10 grid gap-4 sm:grid-cols-3">
                        <div class="glass-panel p-4">
                            <p class="text-xs font-bold uppercase tracking-[0.18em] text-slate-500">Produk</p>
                            <p class="mt-3 text-2xl font-black text-slate-900">{{ $productCount }}</p>
                        </div>
                        <div class="glass-panel p-4">
                            <p class="text-xs font-bold uppercase tracking-[0.18em] text-slate-500">Aktif</p>
                            <p class="mt-3 text-2xl font-black text-emerald-600">{{ $activeProductCount }}</p>
                        </div>
                        <div class="glass-panel p-4">
                            <p class="text-xs font-bold uppercase tracking-[0.18em] text-slate-500">Stok</p>
                            <p class="mt-3 text-2xl font-black text-indigo-600">{{ $stockTotal }}</p>
                        </div>
                    </div>
                </div>

                <div class="hero-ring w-full max-w-xl rounded-[30px] border border-slate-200 bg-white/80 p-5 shadow-[0_25px_70px_rgba(59,130,246,0.16)] backdrop-blur-sm">
                        <div class="rounded-[24px] border border-slate-200 bg-gradient-to-br from-slate-900 via-blue-900 to-indigo-900 p-6 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs font-bold uppercase tracking-[0.2em] text-blue-200">Overview</p>
                                <h3 class="mt-2 text-2xl font-black">Dashboard Produk</h3>
                            </div>
                            <span class="rounded-full bg-emerald-500/20 px-2.5 py-1 text-xs font-bold text-emerald-300">Online</span>
                        </div>

                        <div class="mt-8 grid gap-4 sm:grid-cols-2">
                            <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                                <p class="text-xs uppercase tracking-[0.18em] text-slate-300">Total Produk</p>
                                    <p class="mt-3 text-3xl font-black">{{ $productCount }}</p>
                            </div>
                            <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                                <p class="text-xs uppercase tracking-[0.18em] text-slate-300">Stok</p>
                                    <p class="mt-3 text-3xl font-black">{{ $stockTotal }}</p>
                            </div>
                            <div class="rounded-2xl border border-white/10 bg-white/5 p-4 sm:col-span-2">
                                <p class="text-xs uppercase tracking-[0.18em] text-slate-300">Status</p>
                                <p class="mt-3 text-base font-semibold text-blue-100">Semua produk siap dipantau dan dikelola.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </body>
</html>
