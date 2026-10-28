<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-amber-700">Inventaris</p>
                <h2 class="mt-1 text-2xl font-black text-slate-900">Restock Barang</h2>
                <p class="mt-1 text-sm text-slate-500">Catat barang masuk dan pantau perubahan stok.</p>
            </div>
        </div>
    </x-slot>

    <div class="py-8 sm:py-10">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800" role="status">
                    {{ session('success') }}
                </div>
            @endif

            <section class="grid gap-6 lg:grid-cols-[minmax(0,0.8fr)_minmax(0,1.2fr)]">
                <form method="POST" action="{{ route('stock-restocks.store') }}" class="space-y-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
                    @csrf
                    <div>
                        <h3 class="text-lg font-black text-slate-900">Catat barang masuk</h3>
                        <p class="mt-1 text-sm text-slate-500">Stok produk diperbarui bersama riwayatnya.</p>
                    </div>

                    @if ($products->isEmpty())
                        <p class="rounded-xl bg-amber-50 p-4 text-sm text-amber-900">Tambahkan produk terlebih dahulu sebelum mencatat restock.</p>
                    @endif

                    <div>
                        <label for="product_id" class="mb-1.5 block text-sm font-semibold text-slate-700">Produk</label>
                        <select id="product_id" name="product_id" required @disabled($products->isEmpty()) class="w-full rounded-xl border-slate-300 bg-white text-sm focus:border-amber-500 focus:ring-amber-200">
                            <option value="">Pilih produk</option>
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}" @selected((string) old('product_id') === (string) $product->id)>
                                    {{ $product->name }} · stok {{ $product->stock }}{{ $product->is_active ? '' : ' · nonaktif' }}
                                </option>
                            @endforeach
                        </select>
                        @error('product_id') <p class="mt-1 text-sm text-rose-700">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label for="quantity" class="mb-1.5 block text-sm font-semibold text-slate-700">Jumlah masuk</label>
                            <input id="quantity" name="quantity" type="number" min="1" max="2147483647" value="{{ old('quantity') }}" required inputmode="numeric" class="w-full rounded-xl border-slate-300 bg-white text-sm focus:border-amber-500 focus:ring-amber-200">
                            @error('quantity') <p class="mt-1 text-sm text-rose-700">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="received_at" class="mb-1.5 block text-sm font-semibold text-slate-700">Tanggal diterima</label>
                            <input id="received_at" name="received_at" type="datetime-local" value="{{ old('received_at', now()->format('Y-m-d\TH:i')) }}" class="w-full rounded-xl border-slate-300 bg-white text-sm focus:border-amber-500 focus:ring-amber-200">
                            @error('received_at') <p class="mt-1 text-sm text-rose-700">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label for="supplier" class="mb-1.5 block text-sm font-semibold text-slate-700">Pemasok <span class="font-normal text-slate-400">(opsional)</span></label>
                        <input id="supplier" name="supplier" type="text" maxlength="255" value="{{ old('supplier') }}" class="w-full rounded-xl border-slate-300 bg-white text-sm focus:border-amber-500 focus:ring-amber-200">
                        @error('supplier') <p class="mt-1 text-sm text-rose-700">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="notes" class="mb-1.5 block text-sm font-semibold text-slate-700">Catatan <span class="font-normal text-slate-400">(opsional)</span></label>
                        <textarea id="notes" name="notes" rows="3" maxlength="2000" class="w-full rounded-xl border-slate-300 bg-white text-sm focus:border-amber-500 focus:ring-amber-200">{{ old('notes') }}</textarea>
                        @error('notes') <p class="mt-1 text-sm text-rose-700">{{ $message }}</p> @enderror
                    </div>

                    <button type="submit" @disabled($products->isEmpty()) class="btn-primary w-full justify-center disabled:cursor-not-allowed disabled:opacity-50">
                        Simpan restock
                    </button>
                </form>

                <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <div class="border-b border-slate-100 px-5 py-4 sm:px-6">
                        <h3 class="font-black text-slate-900">Riwayat barang masuk</h3>
                        <p class="mt-1 text-sm text-slate-500">Catatan terbaru tampil lebih dahulu.</p>
                    </div>

                    <div class="divide-y divide-slate-100">
                        @forelse ($restocks as $restock)
                            <article class="p-4 sm:p-5">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <h4 class="truncate font-bold text-slate-900">{{ $restock->product_name }}</h4>
                                        <p class="mt-1 text-xs text-slate-500">{{ $restock->received_at->locale('id')->translatedFormat('d M Y, H:i') }}</p>
                                    </div>
                                    <span class="shrink-0 rounded-full bg-emerald-50 px-3 py-1 text-sm font-black text-emerald-800">+{{ number_format($restock->quantity) }}</span>
                                </div>
                                <dl class="mt-3 grid grid-cols-2 gap-3 text-xs">
                                    <div><dt class="text-slate-500">Pemasok</dt><dd class="mt-0.5 font-semibold text-slate-800">{{ $restock->supplier ?: '—' }}</dd></div>
                                    <div><dt class="text-slate-500">Dicatat oleh</dt><dd class="mt-0.5 font-semibold text-slate-800">{{ $restock->user?->name ?? 'Akun dihapus' }}</dd></div>
                                </dl>
                                @if ($restock->notes)
                                    <p class="mt-3 break-words text-sm text-slate-600">{{ $restock->notes }}</p>
                                @endif
                            </article>
                        @empty
                            <p class="px-5 py-12 text-center text-sm text-slate-500">Belum ada riwayat restock.</p>
                        @endforelse
                    </div>

                    @if ($restocks->hasPages())
                        <div class="border-t border-slate-100 p-4">{{ $restocks->links() }}</div>
                    @endif
                </section>
            </section>
        </div>
    </div>
</x-app-layout>
