<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-amber-700">
                    Minimarket POS
                </p>
                <h2 class="mt-1 text-2xl font-black leading-tight text-slate-900">
                    {{ __('Katalog Produk') }}
                </h2>
            </div>

            <a href="{{ route('products.create') }}" class="btn-primary">
                {{ __('Tambah Produk') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700 shadow-sm">
                    {{ session('success') }}
                </div>
            @endif
            @if ($showingLowStock)
                <div class="mb-6 flex flex-col gap-2 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900 sm:flex-row sm:items-center sm:justify-between">
                    <span>{{ __('Menampilkan produk dengan stok :count unit atau kurang.', ['count' => $lowStockThreshold]) }}</span>
                    <a href="{{ route('products.index') }}" class="font-bold underline">{{ __('Tampilkan semua produk') }}</a>
                </div>
            @endif
            @if (! $showingLowStock && $products->getCollection()->contains(fn ($product) => $product->stock <= $lowStockThreshold))
                <div class="mb-6 flex flex-col gap-2 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900 sm:flex-row sm:items-center sm:justify-between">
                    <span><strong>{{ __('Perhatian:') }}</strong> {{ __('Ada produk dengan stok :count unit atau kurang.', ['count' => $lowStockThreshold]) }}</span>
                    <a href="{{ route('products.index', ['stock' => 'low']) }}" class="font-bold underline">{{ __('Tampilkan stok menipis') }}</a>
                </div>
            @endif

            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-[0_14px_36px_rgba(15,23,42,0.08)]">
                <div class="overflow-x-auto p-4 text-slate-900 sm:p-6">
                    <table class="min-w-full border-separate border-spacing-y-2">
                        <thead>
                            <tr class="text-left text-xs font-bold uppercase tracking-[0.12em] text-slate-500">
                                <th class="px-4 py-3">Produk</th>
                                <th class="px-4 py-3">Kategori</th>
                                <th class="px-4 py-3">Harga</th>
                                <th class="px-4 py-3">Stok</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3">Gambar</th>
                                <th class="px-4 py-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($products as $product)
                                <tr class="overflow-hidden rounded-2xl bg-slate-50 text-sm text-slate-700 shadow-sm ring-1 ring-slate-200">
                                    <td class="rounded-l-xl px-4 py-4 font-semibold text-slate-800">
                                        {{ $product->name }}
                                    </td>
                                    <td class="px-4 py-4">{{ $product->category ?: '-' }}</td>
                                    <td class="px-4 py-4 font-semibold text-slate-800">
                                        Rp {{ number_format((float) $product->price, 0, ',', '.') }}
                                    </td>
                                    <td class="px-4 py-4">
                                        <span @class([
                                            'inline-flex rounded-full px-2.5 py-1 font-semibold',
                                            'bg-rose-100 text-rose-800' => $product->stock === 0,
                                            'bg-amber-100 text-amber-900' => $product->stock > 0 && $product->stock <= $lowStockThreshold,
                                            'bg-slate-200 text-slate-700' => $product->stock > $lowStockThreshold,
                                        ])>
                                            {{ $product->stock }}
                                        </span>
                                        @if ($product->stock <= $lowStockThreshold)
                                            <span class="mt-1 block text-[10px] font-bold uppercase tracking-wide text-amber-800">{{ $product->stock === 0 ? __('Habis') : __('Stok menipis') }}</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4">
                                        <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-bold {{ $product->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-200 text-slate-700' }}">
                                            {{ $product->is_active ? 'Aktif' : 'Nonaktif' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4">
                                        @if ($product->image)
                                            <img class="h-16 w-16 rounded-lg object-cover shadow-sm ring-1 ring-slate-200" src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}">
                                        @else
                                            <span class="inline-flex h-16 w-16 items-center justify-center rounded-lg bg-slate-100 text-xs font-semibold text-slate-400">Tanpa gambar</span>
                                        @endif
                                    </td>
                                    <td class="rounded-r-xl px-4 py-4">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('products.show', $product) }}" class="btn-info">
                                                Lihat
                                            </a>
                                            <a href="{{ route('products.edit', $product) }}" class="btn-warning">
                                                Edit
                                            </a>
                                            <form method="POST" action="{{ route('products.destroy', $product) }}" onsubmit="return confirm('Hapus produk ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn-danger">
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-16 text-center text-slate-500">
                                        <div class="rounded-2xl border border-dashed border-amber-300 bg-amber-50/40 p-8">
                                            Belum ada produk. Tambahkan produk pertama untuk mulai mengelola inventaris.
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="mt-6">
                        {{ $products->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
