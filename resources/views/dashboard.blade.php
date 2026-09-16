<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <span class="soft-label">Overview</span>
                <h2 class="mt-3 text-2xl font-black leading-tight text-slate-900 dark:text-gray-100">
                    {{ __('Dashboard') }}
                </h2>
                <p class="mt-1 text-sm text-slate-600 dark:text-gray-300">
                    {{ __('Selamat datang kembali di sistem produk Anda.') }}
                </p>
            </div>

            <a href="{{ route('products.index') }}" class="btn-primary">
                {{ __('Lihat Produk') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-4">
                <div class="rounded-2xl border border-blue-100 bg-white p-6 shadow-[0_10px_30px_rgba(15,23,42,0.04)]">
                    <p class="text-sm font-semibold uppercase tracking-[0.18em] text-slate-500">{{ __('Total Produk') }}</p>
                    <p class="mt-3 text-3xl font-black text-slate-900">{{ $totalProducts }}</p>
                </div>

                <div class="rounded-2xl border border-emerald-100 bg-white p-6 shadow-[0_10px_30px_rgba(15,23,42,0.04)]">
                    <p class="text-sm font-semibold uppercase tracking-[0.18em] text-slate-500">{{ __('Produk Aktif') }}</p>
                    <p class="mt-3 text-3xl font-black text-emerald-600">{{ $activeProducts }}</p>
                </div>

                <div class="rounded-2xl border border-amber-100 bg-white p-6 shadow-[0_10px_30px_rgba(15,23,42,0.04)]">
                    <p class="text-sm font-semibold uppercase tracking-[0.18em] text-slate-500">{{ __('Produk Nonaktif') }}</p>
                    <p class="mt-3 text-3xl font-black text-amber-600">{{ $inactiveProducts }}</p>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-slate-900 p-6 text-white shadow-[0_10px_30px_rgba(15,23,42,0.12)]">
                    <p class="text-sm font-semibold uppercase tracking-[0.18em] text-slate-300">{{ __('Total Stok') }}</p>
                    <p class="mt-3 text-3xl font-black text-white">{{ $stockProducts }}</p>
                </div>
            </div>

            <div class="mt-8 grid gap-6 lg:grid-cols-[1.6fr_1fr]">
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-[0_10px_30px_rgba(15,23,42,0.04)] dark:bg-gray-800">
                    <div class="mb-4 flex items-center justify-between">
                        <h3 class="text-lg font-bold text-slate-900 dark:text-gray-100">
                            {{ __('Ringkasan') }}
                        </h3>
                    </div>

                    <div class="space-y-4">
                        <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 dark:bg-gray-700/60">
                            <p class="text-sm font-semibold uppercase tracking-[0.16em] text-slate-500 dark:text-gray-300">{{ __('Status sistem') }}</p>
                            <p class="mt-2 text-base font-semibold text-slate-900 dark:text-gray-100">
                                {{ __('Semua fungsi produk berjalan dengan baik.') }}
                            </p>
                        </div>

                        <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 dark:bg-gray-700/60">
                            <p class="text-sm font-semibold uppercase tracking-[0.16em] text-slate-500 dark:text-gray-300">{{ __('Aksi cepat') }}</p>
                            <div class="mt-3 flex flex-wrap gap-3">
                                <a href="{{ route('products.create') }}" class="btn-primary">
                                    {{ __('Tambah Produk') }}
                                </a>
                                <a href="{{ route('products.index') }}" class="btn-secondary">
                                    {{ __('Kelola Produk') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl border border-emerald-200 bg-gradient-to-br from-emerald-50 to-white p-6 shadow-[0_10px_30px_rgba(15,23,42,0.04)] dark:bg-gray-800">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-gray-100">
                        {{ __('Status Login') }}
                    </h3>

                    <div class="mt-5 rounded-xl border border-emerald-200 bg-emerald-50 p-4 dark:border-emerald-800 dark:bg-emerald-900/20">
                        <p class="text-sm font-semibold uppercase tracking-[0.16em] text-emerald-700 dark:text-emerald-300">
                            {{ __('Anda sedang login sebagai:') }}
                        </p>
                        <p class="mt-2 text-lg font-black text-emerald-900 dark:text-emerald-100">
                            {{ Auth::user()->name }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
