<x-app-layout>
    <x-slot name="header">
        <div class="cashier-header flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="soft-label">Point of Sale</p>
                <h2 class="mt-3 text-3xl font-black tracking-tight text-slate-950">Transaksi Kasir</h2>
                <p class="mt-1 text-sm text-slate-500">Cari barang, susun keranjang, dan selesaikan pembayaran.</p>
            </div>
            <div class="transaction-ticket text-left sm:text-right">
                <p class="text-xs font-bold uppercase tracking-[0.16em] text-slate-400">No. Transaksi</p>
                <p id="transactionNumber" class="mt-1 font-mono text-sm font-bold text-slate-800">TRX-{{ now()->format('Ymd-His') }}</p>
                <p id="currentDate" class="mt-1 text-xs text-slate-500">{{ now()->locale('id')->translatedFormat('d F Y, H:i') }}</p>
            </div>
        </div>
    </x-slot>

    <div class="cashier-page mx-auto max-w-[1600px] px-4 py-6 sm:px-6 lg:px-8">
        <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_390px]">
            <section class="space-y-6">
                <div class="panel-surface overflow-visible">
                    <div class="border-b border-slate-100 px-5 py-4">
                        <div class="flex items-center justify-between gap-4">
                            <div>
                                <h3 class="font-black text-slate-900">Tambah Barang</h3>
                                <p class="mt-1 text-xs text-slate-500">Gunakan nama barang, kode, atau barcode.</p>
                            </div>
                            <span class="rounded-lg bg-blue-50 px-2.5 py-1 text-xs font-bold text-blue-700">F2 Cari</span>
                        </div>
                    </div>
                    <div class="space-y-5 p-5">
                        <div class="relative flex gap-3">
                            <div class="relative min-w-0 flex-1">
                                <label for="searchProduct" class="sr-only">Cari produk</label>
                                <input id="searchProduct" type="search" autocomplete="off" class="w-full rounded-xl border-slate-200 bg-slate-50 py-3 pl-4 pr-4 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Cari nama barang / kode / barcode...">
                                <div id="productResults" class="absolute inset-x-0 top-full z-20 mt-2 hidden max-h-72 overflow-y-auto rounded-xl border border-slate-200 bg-white p-2 shadow-xl"></div>
                            </div>
                            <button type="button" id="searchButton" class="btn-primary shrink-0 px-5">Cari</button>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label for="customerType" class="mb-2 block text-xs font-bold uppercase tracking-[0.14em] text-slate-500">Pelanggan</label>
                                <select id="customerType" class="w-full rounded-xl border-slate-200 bg-slate-50 py-3 text-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option>Umum</option>
                                    <option>Pelanggan Member</option>
                                    <option>Member VIP</option>
                                </select>
                            </div>
                            <div>
                                <label for="customerContact" class="mb-2 block text-xs font-bold uppercase tracking-[0.14em] text-slate-500">No. Member / HP</label>
                                <input id="customerContact" type="text" class="w-full rounded-xl border-slate-200 bg-slate-50 py-3 text-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Opsional">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="panel-surface overflow-hidden">
                    <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
                        <div>
                            <h3 class="font-black text-slate-900">Keranjang Belanja</h3>
                            <p class="mt-1 text-xs text-slate-500">Atur jumlah barang sebelum pembayaran.</p>
                        </div>
                        <span id="itemCount" class="rounded-lg bg-slate-100 px-3 py-1 text-xs font-bold text-slate-600">0 Item</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-left text-sm">
                            <thead class="bg-slate-50 text-[11px] uppercase tracking-[0.14em] text-slate-500">
                                <tr>
                                    <th class="px-5 py-3">No</th>
                                    <th class="px-3 py-3">Barang</th>
                                    <th class="px-3 py-3 text-right">Harga</th>
                                    <th class="px-3 py-3 text-center">Qty</th>
                                    <th class="px-3 py-3 text-right">Subtotal</th>
                                    <th class="px-5 py-3"></th>
                                </tr>
                            </thead>
                            <tbody id="cartBody" class="divide-y divide-slate-100"></tbody>
                        </table>
                    </div>
                </div>
            </section>

            <aside class="space-y-6 xl:sticky xl:top-6 xl:self-start">
                <div class="panel-surface payment-summary-card overflow-hidden">
                    <div class="border-b border-slate-100 bg-gradient-to-r from-slate-50 to-blue-50/70 px-5 py-4">
                        <h3 class="font-black text-slate-900">Ringkasan Pembayaran</h3>
                    </div>
                    <div class="space-y-4 p-5">
                        <div class="summary-row"><span>Total Item</span><strong id="totalQty">0</strong></div>
                        <div class="summary-row"><span>Subtotal</span><strong id="subtotal">Rp 0</strong></div>
                        <label class="summary-row" for="discountPercent"><span>Diskon (%)</span><input id="discountPercent" type="number" min="0" max="100" value="0" class="summary-input"></label>
                        <label class="summary-row" for="discountAmount"><span>Diskon (Rp)</span><input id="discountAmount" type="number" min="0" value="0" class="summary-input"></label>
                        <label class="summary-row" for="tax"><span>Pajak / PPN</span><input id="tax" type="number" min="0" value="0" class="summary-input"></label>
                        <label class="summary-row" for="otherFee"><span>Biaya Lain</span><input id="otherFee" type="number" min="0" value="0" class="summary-input"></label>

                        <div class="rounded-2xl bg-slate-900 p-5 text-white">
                            <p class="text-[11px] font-bold uppercase tracking-[0.18em] text-slate-400">Total Akhir</p>
                            <p id="grandTotal" class="mt-2 text-3xl font-black">Rp 0</p>
                        </div>
                    </div>
                </div>

                <div class="panel-surface payment-card overflow-hidden">
                    <div class="border-b border-slate-100 bg-gradient-to-r from-blue-50 to-indigo-50 px-5 py-4">
                        <h3 class="font-black text-slate-900">Pembayaran</h3>
                    </div>
                    <div class="space-y-5 p-5">
                        <div>
                            <div class="mb-2 flex items-center justify-between">
                                <label for="payment" class="text-xs font-bold uppercase tracking-[0.14em] text-slate-500">Uang Dibayar</label>
                                <span class="rounded-lg bg-blue-50 px-2 py-1 text-[10px] font-bold text-blue-700">F4 Bayar</span>
                            </div>
                            <div class="payment-field">
                                <span>Rp</span>
                                <input id="payment" type="number" min="0" placeholder="0" class="w-full border-0 bg-transparent py-4 pl-2 pr-4 text-right text-2xl font-black text-slate-900 outline-none focus:ring-0">
                            </div>
                        </div>

                        <div>
                            <p class="mb-2 text-xs font-bold uppercase tracking-[0.14em] text-slate-500">Metode Pembayaran</p>
                            <div id="paymentMethods" class="grid grid-cols-3 gap-2">
                                @foreach (['Tunai', 'QRIS', 'Debit', 'Kredit', 'E-Wallet', 'Transfer'] as $method)
                                    <button type="button" data-method="{{ $method }}" class="payment-method {{ $loop->first ? 'active' : '' }}">{{ $method }}</button>
                                @endforeach
                            </div>
                        </div>

                        <div id="changeBox" class="rounded-2xl bg-emerald-50 p-4 text-emerald-800">
                            <p id="changeLabel" class="text-[11px] font-bold uppercase tracking-[0.18em]">Kembalian</p>
                            <p id="change" class="mt-1 text-2xl font-black">Rp 0</p>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <button type="button" id="holdButton" class="btn-warning">Tahan</button>
                            <button type="button" id="cancelButton" class="btn-danger">Batal</button>
                        </div>
                        <button type="button" id="payButton" class="btn-success w-full">Bayar & Cetak</button>
                    </div>
                </div>
            </aside>
        </div>

        <div class="mt-6 flex flex-wrap items-center justify-between gap-2 text-xs text-slate-500">
            <span>Kasir: <strong class="text-slate-700">{{ auth()->user()->name }}</strong></span>
            <span>F2 Cari Barang · F4 Bayar · ESC Batal</span>
        </div>
    </div>

    @push('scripts')
        <script>
            window.cashierProducts = @json($products);
        </script>
        @vite('resources/js/cashier.js')
    @endpush
</x-app-layout>
