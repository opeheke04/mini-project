<x-app-layout>
    <x-slot name="header">
        <div class="cashier-header flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <div class="flex items-center gap-3">
                    @if ($storeLogo !== '')
                        <img src="{{ asset('storage/'.$storeLogo) }}" alt="" class="h-12 w-12 rounded-2xl border border-slate-200 bg-white object-contain p-1 shadow-sm">
                    @endif
                    <div>
                        <p class="soft-label">{{ $storeName }}</p>
                        <h2 class="mt-2 text-2xl font-black tracking-tight text-slate-950 sm:text-3xl">Transaksi Kasir</h2>
                    </div>
                </div>
                <p class="mt-1 text-sm text-slate-500">Cari barang, susun keranjang, dan selesaikan pembayaran.</p>
            </div>
            <div class="transaction-ticket text-left sm:text-right">
                <p class="text-xs font-bold uppercase tracking-[0.16em] text-slate-400">No. Transaksi</p>
                <p id="transactionNumber" class="mt-1 font-mono text-sm font-bold text-slate-800">TRX-{{ now()->format('Ymd-His') }}</p>
                <p id="currentDate" class="mt-1 text-xs text-slate-500">{{ now()->locale('id')->translatedFormat('d F Y, H:i') }}</p>
            </div>
        </div>
    </x-slot>

    <div class="cashier-page mx-auto max-w-[1600px] px-4 py-7 sm:px-6 lg:px-8">
        <div id="cashierToast" class="cashier-toast hidden" role="status" aria-live="polite" aria-atomic="true"></div>
        @if ($lowStockProducts->isNotEmpty())
            <section class="mb-6 rounded-2xl border border-amber-200 bg-amber-50/80 p-4 shadow-sm sm:p-5" aria-label="Peringatan stok menipis">
                <div class="flex items-start gap-3">
                    <span class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-amber-100 font-black text-amber-800">!</span>
                    <div class="min-w-0 flex-1">
                        <h3 class="font-black text-amber-950">Perhatian stok menipis</h3>
                        <p class="mt-1 text-sm text-amber-900">Produk aktif dengan stok {{ $lowStockThreshold }} unit atau kurang:</p>
                        <ul class="mt-3 flex flex-wrap gap-2">
                            @foreach ($lowStockProducts as $product)
                                <li class="rounded-lg border border-amber-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-800">
                                    {{ $product->name }} <span class="text-amber-800">({{ $product->stock === 0 ? 'habis' : $product->stock.' tersisa' }})</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    @if (auth()->user()->role === 'admin')
                        <a href="{{ route('products.index', ['stock' => 'low']) }}" class="shrink-0 text-xs font-bold text-amber-900 underline">Lihat stok</a>
                    @endif
                </div>
            </section>
        @endif
        <div class="cashier-layout grid min-w-0 gap-6 xl:grid-cols-[minmax(0,1fr)_390px]">
            <section class="min-w-0 space-y-6">
                <div class="panel-surface search-panel overflow-visible">
                    <div class="panel-heading border-b border-slate-100 px-5 py-4">
                        <div class="flex items-center justify-between gap-4">
                            <div class="flex items-start gap-3">
                                <span class="heading-icon bg-amber-100 text-amber-800" aria-hidden="true">
                                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M10 4v12M4 10h12" /></svg>
                                </span>
                                <div>
                                <h3 class="font-black text-slate-900">Tambah Barang</h3>
                                <p class="mt-1 text-xs text-slate-500">Cari nama, kategori, atau scan barcode dengan scanner.</p>
                                </div>
                            </div>
                            <span class="rounded-lg bg-slate-100 px-2.5 py-1 text-xs font-bold text-slate-700">F2 Cari</span>
                        </div>
                    </div>
                    <div class="space-y-5 p-5">
                        <div class="relative flex gap-3">
                            <div class="relative min-w-0 flex-1">
                                <label for="searchProduct" class="sr-only">Cari produk</label>
                                <input id="searchProduct" type="search" autocomplete="off" class="w-full rounded-xl border-slate-200 bg-slate-50 py-3 pl-4 pr-4 text-sm shadow-sm focus:border-amber-500 focus:ring-amber-500" placeholder="Cari nama, kategori, atau scan barcode...">
                                <div id="productResults" class="absolute inset-x-0 top-full z-20 mt-2 hidden max-h-72 overflow-y-auto rounded-xl border border-slate-200 bg-white p-2 shadow-xl"></div>
                            </div>
                            <button type="button" id="searchButton" class="btn-primary shrink-0 px-5">Cari</button>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label for="customerType" class="mb-2 block text-xs font-bold uppercase tracking-[0.14em] text-slate-500">Pelanggan</label>
                                <select id="customerType" class="w-full rounded-xl border-slate-200 bg-slate-50 py-3 text-sm focus:border-amber-500 focus:ring-amber-500">
                                    <option>Umum</option>
                                    <option>Pelanggan Member</option>
                                    <option>Member VIP</option>
                                </select>
                            </div>
                            <div>
                                <label for="customerContact" class="mb-2 block text-xs font-bold uppercase tracking-[0.14em] text-slate-500">No. Member / HP</label>
                                <input id="customerContact" type="text" class="w-full rounded-xl border-slate-200 bg-slate-50 py-3 text-sm focus:border-amber-500 focus:ring-amber-500" placeholder="Opsional">
                            </div>
                        </div>
                    </div>
                </div>

                <section class="panel-surface overflow-hidden" aria-label="Pilih produk cepat">
                    <div class="flex flex-col gap-3 border-b border-slate-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h3 class="font-black text-slate-900">Pilih Produk</h3>
                            <p class="mt-1 text-xs text-slate-500">Tekan kartu produk untuk langsung memasukkan ke keranjang.</p>
                        </div>
                        <span id="productGridCount" class="w-fit rounded-full bg-amber-50 px-3 py-1 text-xs font-bold text-amber-900">{{ $products->count() }} produk tersedia</span>
                    </div>
                    <div id="productGrid" class="grid grid-cols-2 gap-3 p-4 sm:grid-cols-3 sm:p-5 lg:grid-cols-4" aria-live="polite"></div>
                </section>

                <div class="panel-surface cart-panel overflow-hidden">
                    <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
                        <div class="flex items-start gap-3">
                            <span class="heading-icon bg-amber-100 text-amber-700" aria-hidden="true">
                                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.7"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4h2l1.5 9h8.8l1.5-6H6M8 16a.75.75 0 1 0 0 .01M14 16a.75.75 0 1 0 0 .01" /></svg>
                            </span>
                            <div>
                                <h3 class="font-black text-slate-900">Keranjang Belanja</h3>
                            <p class="mt-1 text-xs text-slate-500">Atur jumlah barang sebelum pembayaran.</p>
                            </div>
                        </div>
                        <span id="itemCount" class="rounded-lg bg-slate-100 px-3 py-1 text-xs font-bold text-slate-600">0 Item</span>
                    </div>
                    <div class="overflow-x-auto" role="region" aria-label="Daftar barang di keranjang" tabindex="0">
                        <table class="min-w-[540px] text-left text-sm sm:min-w-[620px]">
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

            <aside class="min-w-0 space-y-6 xl:sticky xl:top-6 xl:self-start">
                <div id="paymentSummary" class="panel-surface payment-summary-card overflow-hidden">
                    <div class="payment-card-heading border-b border-slate-100 px-5 py-4">
                        <span class="soft-label mb-2">Ringkas & jelas</span>
                        <h3 class="font-black text-slate-900">Ringkasan Pembayaran</h3>
                    </div>
                    <div class="space-y-4 p-5">
                        <div class="summary-row"><span>Total Item</span><strong id="totalQty">0</strong></div>
                        <div class="summary-row"><span>Subtotal</span><strong id="subtotal">Rp 0</strong></div>
                        <label class="summary-row" for="discountPercent"><span>Diskon (%)</span><input id="discountPercent" type="number" min="0" max="100" value="0" class="summary-input"></label>
                        <label class="summary-row" for="discountAmount"><span>Diskon (Rp)</span><input id="discountAmount" type="number" min="0" value="0" class="summary-input"></label>
                        <label class="summary-row" for="tax"><span>Pajak / PPN</span><input id="tax" type="number" min="0" value="0" class="summary-input"></label>
                        <label class="summary-row" for="otherFee"><span>Biaya Lain</span><input id="otherFee" type="number" min="0" value="0" class="summary-input"></label>

                        <div class="rounded-2xl bg-gradient-to-br from-slate-950 via-slate-900 to-slate-800 p-5 text-white shadow-lg shadow-slate-900/15 ring-1 ring-white/10 sm:p-6">
                            <p class="text-[11px] font-bold uppercase tracking-[0.18em] text-amber-300">Total Akhir</p>
                            <p id="grandTotal" class="mt-2 text-3xl font-black tracking-tight sm:text-4xl">Rp 0</p>
                        </div>
                    </div>
                </div>

                <div id="paymentPanel" class="panel-surface payment-card overflow-hidden">
                    <div class="payment-card-heading border-b border-slate-100 px-5 py-4">
                        <span class="soft-label mb-2">Langkah terakhir</span>
                        <h3 class="font-black text-slate-900">Pembayaran</h3>
                    </div>
                    <div class="space-y-5 p-5">
                        <div>
                            <div class="mb-2 flex items-center justify-between">
                                <label for="payment" class="text-xs font-bold uppercase tracking-[0.14em] text-slate-500">Uang Dibayar</label>
                                <span class="rounded-lg bg-amber-50 px-2 py-1 text-[10px] font-bold text-amber-800">F4 Bayar</span>
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
                        <section class="rounded-xl border border-slate-200 p-4" aria-label="Transaksi ditahan">
                            <div class="flex items-center justify-between gap-3">
                                <div>
                                    <h4 class="text-sm font-bold text-slate-800">Transaksi Ditahan</h4>
                                    <p class="mt-1 text-xs text-slate-500">Tersimpan di browser kasir ini.</p>
                                </div>
                                <span id="heldTransactionCount" class="rounded-lg bg-slate-100 px-2.5 py-1 text-xs font-bold text-slate-600">0</span>
                            </div>
                            <div class="mt-3 flex gap-2">
                                <label for="heldTransactionsSelect" class="sr-only">Pilih transaksi ditahan</label>
                                <select id="heldTransactionsSelect" class="min-w-0 flex-1 rounded-lg border-slate-200 bg-white text-sm">
                                    <option value="">Tidak ada transaksi ditahan</option>
                                </select>
                                <button type="button" id="resumeHeldButton" class="btn-secondary shrink-0 px-3" disabled>Buka</button>
                                <button type="button" id="deleteHeldButton" class="btn-danger shrink-0 px-3" disabled aria-label="Hapus transaksi ditahan">Hapus</button>
                            </div>
                        </section>
                        <button type="button" id="payButton" class="btn-success min-h-14 w-full text-base">Bayar & Cetak</button>
                    </div>
                </div>
            </aside>
        </div>

        <div class="mt-6 flex flex-wrap items-center justify-between gap-2 text-xs text-slate-500">
            <span>Kasir: <strong class="text-slate-700">{{ auth()->user()->name }}</strong></span>
            <span>F2 Cari Barang · F4 Bayar · ESC Batal</span>
        </div>
    </div>
    <div class="cashier-mobile-bar xl:hidden">
        <div class="flex min-w-0 flex-col">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500">Total sementara</span>
            <strong id="mobileGrandTotal" class="truncate text-base font-black text-slate-950">Rp 0</strong>
        </div>
        <button type="button" id="mobileCheckoutButton" class="btn-primary min-h-11 shrink-0 px-4">Lanjut Bayar</button>
    </div>

    @push('scripts')
        <script>
            window.cashierProducts = @json($products);
            window.cashierTransactionUrl = @json(route('cashier.transactions.store'));
            window.cashierReceiptUrl = @json(route('cashier.receipt.print', ['sale' => 0]));
            window.cashierReceiptPreviewUrl = @json(route('cashier.receipt.preview', ['sale' => 0]));
            window.cashierHeldStorageKey = @json('cashier.held-transactions.user.'.auth()->id());
        </script>
        @vite('resources/js/cashier.js')
    @endpush
</x-app-layout>
