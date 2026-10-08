<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-amber-700">Dashboard · {{ now()->locale('id')->translatedFormat('l, d F Y') }}</p>
                <h2 class="mt-2 text-3xl font-black leading-tight tracking-tight text-slate-950 sm:text-4xl">Ringkasan Toko</h2>
                <p class="mt-2 max-w-xl text-sm leading-6 text-slate-500">Pantau stok, produk, dan aktivitas minimarket Anda dalam satu tampilan.</p>
            </div>
            <a href="{{ route('products.create') }}" class="btn-primary w-full gap-2 sm:w-auto">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14m-7-7h14"/></svg>
                Tambah Produk
            </a>
        </div>
    </x-slot>

    <div class="dashboard-page mx-auto max-w-7xl space-y-7 px-4 py-7 sm:px-6 sm:py-9 lg:px-8">
        <section class="dashboard-hero relative overflow-hidden rounded-[1.75rem] p-6 text-white shadow-[0_24px_60px_rgba(15,23,42,0.2)] sm:p-9">
            <div class="relative z-10 flex flex-col gap-7 sm:flex-row sm:items-center sm:justify-between">
                <div class="max-w-xl">
                    <span class="inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/10 px-3 py-1.5 text-[10px] font-bold uppercase tracking-[0.18em] text-amber-200">
                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-400"></span>
                        Operasional berjalan
                    </span>
                    <h3 class="mt-5 text-2xl font-black tracking-tight sm:text-3xl">Halo, {{ Auth::user()->name }} <span class="text-amber-300">👋</span></h3>
                    <p class="mt-2 text-sm leading-6 text-slate-300">Semua yang Anda butuhkan untuk mengelola toko, siap diakses dari sini.</p>
                </div>
                <div class="flex flex-col gap-2 sm:items-end">
                    <a href="{{ route('cashier.index') }}" class="inline-flex items-center justify-center gap-2 rounded-xl bg-amber-300 px-5 py-3 text-sm font-black text-slate-950 shadow-lg shadow-black/10 transition hover:-translate-y-0.5 hover:bg-amber-200">
                        Buka Kasir
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14m-6-6 6 6-6 6"/></svg>
                    </a>
                    <span class="text-xs text-slate-400">Akses cepat ke halaman transaksi</span>
                </div>
            </div>
            <div class="pointer-events-none absolute -right-16 -top-36 h-80 w-80 rounded-full border border-white/[0.07]"></div>
            <div class="pointer-events-none absolute -right-4 -top-24 h-64 w-64 rounded-full border border-white/[0.07]"></div>
            <div class="pointer-events-none absolute -bottom-32 right-1/3 h-64 w-64 rounded-full bg-amber-400/[0.07] blur-3xl"></div>
        </section>

        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4" aria-label="Statistik inventaris">
            <article class="stat-card">
                <div class="flex items-start justify-between">
                    <div><p class="stat-label">Total Produk</p><p class="stat-value">{{ number_format($totalProducts) }}</p></div>
                    <span class="stat-icon bg-slate-100 text-slate-700">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m12 3 9 5-9 5-9-5 9-5Zm-9 9 9 5 9-5M3 16l9 5 9-5"/></svg>
                    </span>
                </div>
                <p class="stat-footnote">Semua produk dalam katalog</p>
            </article>
            <article class="stat-card">
                <div class="flex items-start justify-between">
                    <div><p class="stat-label">Produk Aktif</p><p class="stat-value text-emerald-700">{{ number_format($activeProducts) }}</p></div>
                    <span class="stat-icon bg-emerald-50 text-emerald-700">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                    </span>
                </div>
                <p class="stat-footnote">Siap dijual di halaman kasir</p>
            </article>
            <article class="stat-card">
                <div class="flex items-start justify-between">
                    <div><p class="stat-label">Stok Menipis</p><p class="stat-value text-amber-700">{{ number_format($lowStockCount) }}</p></div>
                    <span class="stat-icon bg-amber-50 text-amber-800">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9.303 3.376L13.303 3.25a1.5 1.5 0 0 0-2.606 0L2.697 16.126a1.5 1.5 0 0 0 1.303 2.25h16a1.5 1.5 0 0 0 1.303-2.25Z"/><path stroke-linecap="round" d="M12 16.5h.008"/></svg>
                    </span>
                </div>
                <p class="stat-footnote">Batas peringatan {{ $lowStockThreshold }} unit</p>
            </article>
            <article class="stat-card stat-card-dark">
                <div class="flex items-start justify-between">
                    <div><p class="stat-label !text-slate-400">Total Stok</p><p class="stat-value !text-white">{{ number_format($stockProducts) }}</p></div>
                    <span class="stat-icon bg-white/10 text-amber-300">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6 7.5h12m-12 4.5h12m-12 4.5h7.5M4.5 3.75h15A1.5 1.5 0 0 1 21 5.25v13.5a1.5 1.5 0 0 1-1.5 1.5h-15A1.5 1.5 0 0 1 3 18.75V5.25a1.5 1.5 0 0 1 1.5-1.5Z"/></svg>
                    </span>
                </div>
                <p class="stat-footnote !text-slate-400">Unit tersedia di inventaris</p>
            </article>
        </section>

        <section class="grid gap-6 lg:grid-cols-[minmax(0,1.4fr)_minmax(280px,0.8fr)]">
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-[0_12px_35px_rgba(15,23,42,0.05)]">
                <div class="flex items-center justify-between gap-4 border-b border-slate-100 px-5 py-4 sm:px-6">
                    <div>
                        <h3 class="font-black text-slate-900">Perlu perhatian</h3>
                        <p class="mt-1 text-xs text-slate-500">Produk aktif dengan stok {{ $lowStockThreshold }} unit atau kurang</p>
                    </div>
                    <a href="{{ route('products.index', ['stock' => 'low']) }}" class="inline-flex shrink-0 items-center gap-1 text-xs font-bold text-amber-800 hover:text-amber-950">
                        Lihat semua <span aria-hidden="true">→</span>
                    </a>
                </div>
                @if ($lowStockProducts->isEmpty())
                    <div class="flex items-center gap-3 px-5 py-7 sm:px-6">
                        <span class="flex h-10 w-10 items-center justify-center rounded-full bg-emerald-50 text-emerald-700">✓</span>
                        <div><p class="text-sm font-bold text-slate-800">Stok aman</p><p class="mt-0.5 text-xs text-slate-500">Tidak ada produk aktif yang stoknya menipis.</p></div>
                    </div>
                @else
                    <ul class="divide-y divide-slate-100">
                        @foreach ($lowStockProducts as $product)
                            <li class="flex items-center justify-between gap-4 px-5 py-3.5 transition hover:bg-amber-50/40 sm:px-6">
                                <div class="flex min-w-0 items-center gap-3">
                                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl {{ $product->stock === 0 ? 'bg-rose-50 text-rose-700' : 'bg-amber-50 text-amber-800' }}">
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3 3.75 7.5 12 12l8.25-4.5L12 3Zm-8.25 9L12 16.5 20.25 12M3.75 16.5 12 21l8.25-4.5"/></svg>
                                    </span>
                                    <a href="{{ route('products.show', $product) }}" class="truncate text-sm font-semibold text-slate-800 hover:text-amber-800">{{ $product->name }}</a>
                                </div>
                                <span class="shrink-0 rounded-full px-3 py-1 text-xs font-black {{ $product->stock === 0 ? 'bg-rose-100 text-rose-800' : 'bg-amber-100 text-amber-900' }}">
                                    {{ $product->stock === 0 ? 'Habis' : 'Sisa '.$product->stock }}
                                </span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-[0_12px_35px_rgba(15,23,42,0.05)] sm:p-6">
                <div class="flex items-center gap-3">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-900 text-amber-300">
                        <x-application-logo class="h-6 w-6"/>
                    </span>
                    <div><p class="text-xs font-bold uppercase tracking-[0.14em] text-slate-400">Minimarket POS</p><h3 class="font-black text-slate-900">Akses cepat</h3></div>
                </div>
                <div class="mt-5 space-y-2">
                    <a href="{{ route('products.index') }}" class="quick-link">
                        <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-slate-100 text-slate-700">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m12 3 9 5-9 5-9-5 9-5Zm-9 9 9 5 9-5M3 16l9 5 9-5"/></svg>
                        </span>
                        <span class="min-w-0 flex-1"><span class="block text-sm font-bold text-slate-800">Katalog produk</span><span class="block text-xs text-slate-500">Kelola harga dan inventaris</span></span>
                        <span class="text-slate-400" aria-hidden="true">→</span>
                    </a>
                    <a href="{{ route('sales.index') }}" class="quick-link">
                        <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-amber-50 text-amber-800">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M4 19V5m0 14h16M8 15l3-4 3 2 5-7"/></svg>
                        </span>
                        <span class="min-w-0 flex-1"><span class="block text-sm font-bold text-slate-800">Laporan penjualan</span><span class="block text-xs text-slate-500">Riwayat dan rekap omzet</span></span>
                        <span class="text-slate-400" aria-hidden="true">→</span>
                    </a>
                    <a href="{{ route('cashier.index') }}" class="quick-link">
                        <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-emerald-50 text-emerald-700">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M4 5.5A2.5 2.5 0 0 1 6.5 3h11A2.5 2.5 0 0 1 20 5.5v13a2.5 2.5 0 0 1-2.5 2.5h-11A2.5 2.5 0 0 1 4 18.5v-13ZM8 8h8m-8 4h8m-8 4h4"/></svg>
                        </span>
                        <span class="min-w-0 flex-1"><span class="block text-sm font-bold text-slate-800">Halaman kasir</span><span class="block text-xs text-slate-500">Mulai transaksi baru</span></span>
                        <span class="text-slate-400" aria-hidden="true">→</span>
                    </a>
                </div>
                <div class="mt-5 flex items-center gap-3 rounded-xl border border-slate-100 bg-slate-50 px-4 py-3">
                    <span class="flex h-8 w-8 items-center justify-center rounded-full bg-white text-xs font-black text-slate-700 shadow-sm">{{ mb_strtoupper(mb_substr(Auth::user()->name, 0, 1)) }}</span>
                    <div class="min-w-0"><p class="truncate text-xs font-bold text-slate-800">{{ Auth::user()->name }}</p><p class="text-[10px] uppercase tracking-wide text-slate-500">Administrator</p></div>
                    <span class="ml-auto flex h-2 w-2 rounded-full bg-emerald-500"></span>
                </div>
            </div>
        </section>
    </div>
</x-app-layout>
