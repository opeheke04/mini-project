<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-amber-700">Minimarket POS</p>
                <h2 class="mt-1 text-2xl font-black text-slate-900">Riwayat & Laporan Penjualan</h2>
                <p class="mt-1 text-sm text-slate-500">Cari transaksi, lihat rincian, dan bandingkan omzet per hari atau bulan.</p>
            </div>
        </div>
    </x-slot>

    <div class="py-8 sm:py-10">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            <form method="GET" action="{{ route('sales.index') }}" class="grid gap-4 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:grid-cols-2 sm:p-5 lg:grid-cols-[minmax(0,1.5fr)_repeat(3,minmax(0,1fr))_auto] lg:items-end">
                <div>
                    <label for="search" class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-slate-600">Cari transaksi</label>
                    <input id="search" name="search" type="search" value="{{ $search }}" placeholder="No. transaksi, kasir, produk, pelanggan..." class="w-full rounded-xl border-slate-200 bg-slate-50 text-sm focus:border-amber-500 focus:ring-amber-200">
                </div>
                <div>
                    <label for="from" class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-slate-600">Dari tanggal</label>
                    <input id="from" name="from" type="date" value="{{ $from->toDateString() }}" required class="w-full rounded-xl border-slate-200 bg-slate-50 text-sm focus:border-amber-500 focus:ring-amber-200">
                </div>
                <div>
                    <label for="to" class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-slate-600">Sampai tanggal</label>
                    <input id="to" name="to" type="date" value="{{ $to->toDateString() }}" required class="w-full rounded-xl border-slate-200 bg-slate-50 text-sm focus:border-amber-500 focus:ring-amber-200">
                </div>
                <div>
                    <label for="period" class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-slate-600">Rekap berdasarkan</label>
                    <select id="period" name="period" class="w-full rounded-xl border-slate-200 bg-slate-50 text-sm focus:border-amber-500 focus:ring-amber-200">
                        <option value="day" @selected($period === 'day')>Harian</option>
                        <option value="month" @selected($period === 'month')>Bulanan</option>
                    </select>
                </div>
                <div class="flex gap-2 sm:col-span-2 lg:col-span-1">
                    <button type="submit" class="btn-primary w-full">Terapkan</button>
                    <a href="{{ route('sales.index') }}" class="btn-secondary">Reset</a>
                </div>
            </form>

            <section class="grid gap-4 sm:grid-cols-2">
                <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="text-xs font-bold uppercase tracking-[0.16em] text-slate-500">Jumlah Transaksi</p>
                    <p class="mt-2 text-3xl font-black text-slate-900">{{ number_format($transactionCount) }}</p>
                    <p class="mt-1 text-xs text-slate-500">{{ $from->format('d/m/Y') }} — {{ $to->format('d/m/Y') }}</p>
                </article>
                <article class="rounded-2xl border border-amber-200 bg-gradient-to-br from-amber-50 to-white p-5 shadow-sm">
                    <p class="text-xs font-bold uppercase tracking-[0.16em] text-amber-800">Total Omzet</p>
                    <p class="mt-2 text-3xl font-black text-slate-900">Rp {{ number_format($revenue, 0, ',', '.') }}</p>
                    <p class="mt-1 text-xs text-slate-500">Dihitung dari transaksi pada rentang tanggal terpilih.</p>
                </article>
            </section>

            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-100 px-5 py-4">
                    <h3 class="font-black text-slate-900">Rekap {{ $period === 'day' ? 'Harian' : 'Bulanan' }}</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-left text-sm">
                        <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                            <tr><th class="px-5 py-3">{{ $period === 'day' ? 'Tanggal' : 'Bulan' }}</th><th class="px-5 py-3 text-right">Transaksi</th><th class="px-5 py-3 text-right">Omzet</th></tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($periodSummaries as $summary)
                                <tr>
                                    <td class="whitespace-nowrap px-5 py-3 font-semibold text-slate-800">{{ $period === 'day' ? \Illuminate\Support\Carbon::parse($summary->bucket)->locale('id')->translatedFormat('d F Y') : \Illuminate\Support\Carbon::createFromFormat('!Y-m', $summary->bucket)->locale('id')->translatedFormat('F Y') }}</td>
                                    <td class="px-5 py-3 text-right text-slate-600">{{ number_format($summary->transaction_count) }}</td>
                                    <td class="whitespace-nowrap px-5 py-3 text-right font-bold text-slate-900">Rp {{ number_format((float) $summary->revenue, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="px-5 py-8 text-center text-slate-500">Belum ada transaksi pada rentang tanggal tersebut.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-100 px-5 py-4">
                    <h3 class="font-black text-slate-900">Transaksi</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-left text-sm">
                        <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                            <tr><th class="px-5 py-3">Waktu / Nomor</th><th class="px-5 py-3">Kasir</th><th class="px-5 py-3">Pelanggan</th><th class="px-5 py-3">Pembayaran</th><th class="px-5 py-3 text-right">Total</th><th class="px-5 py-3"></th></tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($sales as $sale)
                                <tr class="hover:bg-amber-50/40">
                                    <td class="whitespace-nowrap px-5 py-3">
                                        <span class="block font-mono text-xs font-bold text-slate-800">{{ $sale->transaction_number }}</span>
                                        <span class="mt-1 block text-xs text-slate-500">{{ $sale->created_at->format('d/m/Y H:i') }}</span>
                                    </td>
                                    <td class="whitespace-nowrap px-5 py-3 text-slate-700">{{ $sale->user?->name ?? 'Akun dihapus' }}</td>
                                    <td class="whitespace-nowrap px-5 py-3 text-slate-700">{{ $sale->customer_type ?: 'Umum' }}</td>
                                    <td class="whitespace-nowrap px-5 py-3 text-slate-700">{{ $sale->payment_method }}</td>
                                    <td class="whitespace-nowrap px-5 py-3 text-right font-bold text-slate-900">Rp {{ number_format((float) $sale->total, 0, ',', '.') }}</td>
                                    <td class="whitespace-nowrap px-5 py-3 text-right"><a href="{{ route('sales.show', $sale) }}" class="font-bold text-amber-800 underline">Detail</a></td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="px-5 py-8 text-center text-slate-500">Tidak ada transaksi yang cocok dengan filter.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="border-t border-slate-100 p-4">{{ $sales->links() }}</div>
            </section>
        </div>
    </div>
</x-app-layout>
