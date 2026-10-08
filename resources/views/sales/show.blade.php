<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-amber-700">Detail Penjualan</p>
                <h2 class="mt-1 font-mono text-xl font-black text-slate-900 sm:text-2xl">{{ $sale->transaction_number }}</h2>
            </div>
            <div class="flex items-center gap-3">
                <button type="button" id="reprintReceiptButton" class="btn-primary">Cetak Ulang Struk</button>
                <a href="{{ route('sales.index') }}" class="btn-secondary">Kembali ke riwayat</a>
            </div>
        </div>
    </x-slot>

    <div class="py-8 sm:py-10">
        <div class="mx-auto max-w-5xl space-y-6 px-4 sm:px-6 lg:px-8">
            <section class="grid gap-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:grid-cols-2 lg:grid-cols-4">
                <div><p class="text-xs font-bold uppercase tracking-wide text-slate-500">Tanggal</p><p class="mt-1 font-semibold text-slate-900">{{ $sale->created_at->format('d/m/Y H:i') }}</p></div>
                <div><p class="text-xs font-bold uppercase tracking-wide text-slate-500">Kasir</p><p class="mt-1 font-semibold text-slate-900">{{ $sale->user?->name ?? 'Akun dihapus' }}</p></div>
                <div><p class="text-xs font-bold uppercase tracking-wide text-slate-500">Pelanggan</p><p class="mt-1 font-semibold text-slate-900">{{ $sale->customer_type ?: 'Umum' }}{{ $sale->customer_contact ? ' · '.$sale->customer_contact : '' }}</p></div>
                <div><p class="text-xs font-bold uppercase tracking-wide text-slate-500">Metode Pembayaran</p><p class="mt-1 font-semibold text-slate-900">{{ $sale->payment_method }}</p></div>
            </section>

            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-100 px-5 py-4"><h3 class="font-black text-slate-900">Barang dalam transaksi</h3></div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-left text-sm">
                        <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                            <tr><th class="px-5 py-3">Produk</th><th class="px-5 py-3 text-right">Harga Satuan</th><th class="px-5 py-3 text-right">Jumlah</th><th class="px-5 py-3 text-right">Subtotal</th></tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($sale->items as $item)
                                <tr><td class="px-5 py-3 font-semibold text-slate-800">{{ $item->product_name }}</td><td class="whitespace-nowrap px-5 py-3 text-right text-slate-600">Rp {{ number_format((float) $item->unit_price, 0, ',', '.') }}</td><td class="px-5 py-3 text-right text-slate-600">{{ number_format($item->quantity) }}</td><td class="whitespace-nowrap px-5 py-3 text-right font-semibold text-slate-900">Rp {{ number_format((float) $item->line_total, 0, ',', '.') }}</td></tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <dl class="ml-auto max-w-sm space-y-2 border-t border-slate-100 p-5 text-sm">
                    <div class="flex justify-between gap-4"><dt class="text-slate-500">Subtotal</dt><dd class="font-semibold text-slate-800">Rp {{ number_format((float) $sale->subtotal, 0, ',', '.') }}</dd></div>
                    @if ((float) $sale->discount_percent > 0 || (float) $sale->discount_amount > 0)
                        <div class="flex justify-between gap-4"><dt class="text-slate-500">Diskon</dt><dd class="font-semibold text-slate-800">- Rp {{ number_format(((float) $sale->subtotal * (float) $sale->discount_percent / 100) + (float) $sale->discount_amount, 0, ',', '.') }}</dd></div>
                    @endif
                    @if ((float) $sale->tax > 0)
                        <div class="flex justify-between gap-4"><dt class="text-slate-500">Pajak / PPN</dt><dd class="font-semibold text-slate-800">Rp {{ number_format((float) $sale->tax, 0, ',', '.') }}</dd></div>
                    @endif
                    @if ((float) $sale->other_fee > 0)
                        <div class="flex justify-between gap-4"><dt class="text-slate-500">Biaya lain</dt><dd class="font-semibold text-slate-800">Rp {{ number_format((float) $sale->other_fee, 0, ',', '.') }}</dd></div>
                    @endif
                    <div class="flex justify-between gap-4 border-t border-slate-200 pt-3 text-base"><dt class="font-black text-slate-900">Total</dt><dd class="font-black text-amber-900">Rp {{ number_format((float) $sale->total, 0, ',', '.') }}</dd></div>
                    <div class="flex justify-between gap-4"><dt class="text-slate-500">Dibayar</dt><dd class="font-semibold text-slate-800">Rp {{ number_format((float) $sale->amount_paid, 0, ',', '.') }}</dd></div>
                    <div class="flex justify-between gap-4"><dt class="text-slate-500">Kembalian</dt><dd class="font-semibold text-slate-800">Rp {{ number_format((float) $sale->change_due, 0, ',', '.') }}</dd></div>
                </dl>
            </section>
        </div>
    </div>

    @push('scripts')
        <script>
            document.getElementById('reprintReceiptButton')?.addEventListener('click', async () => {
                const button = document.getElementById('reprintReceiptButton');
                const receiptWindow = window.open('about:blank', '_blank');
                button.disabled = true;
                button.textContent = 'Mencetak...';

                try {
                    const previewUrl = @json(route('cashier.receipt.preview', $sale));
                    const response = await fetch('{{ route('cashier.receipt.print', $sale) }}', {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                        },
                    });

                    const result = await response.json().catch(() => ({}));
                    if (result.status !== 'success' && receiptWindow) {
                        receiptWindow.location.href = previewUrl;
                    } else if (receiptWindow) {
                        receiptWindow.close();
                    }

                    if (!receiptWindow) {
                        window.alert('Izinkan pop-up browser untuk mencetak struk.');
                    } else if (result.status !== 'success') {
                        window.alert('Dialog cetak struk dibuka di tab baru.');
                    } else {
                        window.alert(result.message || 'Struk berhasil dicetak.');
                    }
                } catch (error) {
                    if (receiptWindow) {
                        receiptWindow.location.href = @json(route('cashier.receipt.preview', $sale));
                    }
                    window.alert('Gagal mengirim permintaan cetak struk.');
                } finally {
                    button.disabled = false;
                    button.textContent = 'Cetak Ulang Struk';
                }
            });
        </script>
    @endpush
</x-app-layout>
