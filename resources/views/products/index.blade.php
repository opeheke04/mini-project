<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-indigo-600">
                    Inventory
                </p>
                <h2 class="mt-1 text-2xl font-bold leading-tight text-gray-800 dark:text-gray-200">
                    {{ __('Products') }}
                </h2>
            </div>

            <a href="{{ route('products.create') }}" class="btn-primary">
                {{ __('Add Product') }}
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

            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-[0_10px_30px_rgba(15,23,42,0.08)] dark:border-slate-700 dark:bg-gray-800">
                <div class="overflow-x-auto p-4 sm:p-6 text-gray-900 dark:text-gray-100">
                    <table class="min-w-full border-separate border-spacing-y-2">
                        <thead>
                            <tr class="text-left text-xs font-bold uppercase tracking-[0.12em] text-slate-500 dark:text-slate-400">
                                <th class="px-4 py-3">{{ __('Name') }}</th>
                                <th class="px-4 py-3">{{ __('Category') }}</th>
                                <th class="px-4 py-3">{{ __('Price') }}</th>
                                <th class="px-4 py-3">{{ __('Stock') }}</th>
                                <th class="px-4 py-3">{{ __('Status') }}</th>
                                <th class="px-4 py-3">{{ __('Image') }}</th>
                                <th class="px-4 py-3 text-right">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($products as $product)
                                <tr class="overflow-hidden rounded-2xl bg-slate-50 text-sm text-slate-700 shadow-sm ring-1 ring-slate-200 dark:bg-slate-900 dark:text-slate-200 dark:ring-slate-700">
                                    <td class="rounded-l-xl px-4 py-4 font-semibold text-slate-800 dark:text-slate-100">
                                        {{ $product->name }}
                                    </td>
                                    <td class="px-4 py-4">{{ $product->category ?: '-' }}</td>
                                    <td class="px-4 py-4 font-semibold text-slate-800 dark:text-slate-100">
                                        Rp {{ number_format((float) $product->price, 0, ',', '.') }}
                                    </td>
                                    <td class="px-4 py-4">
                                        <span class="inline-flex rounded-full bg-slate-200 px-2.5 py-1 font-semibold text-slate-700 dark:bg-slate-700 dark:text-slate-100">
                                            {{ $product->stock }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4">
                                        <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-bold {{ $product->is_active ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300' : 'bg-slate-200 text-slate-700 dark:bg-slate-700 dark:text-slate-200' }}">
                                            {{ $product->is_active ? __('Active') : __('Inactive') }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4">
                                        @if ($product->image)
                                            <img class="h-16 w-16 rounded-lg object-cover shadow-sm ring-1 ring-slate-200 dark:ring-slate-700" src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}">
                                        @else
                                            <span class="inline-flex h-16 w-16 items-center justify-center rounded-lg bg-slate-200 text-xs text-slate-400 dark:bg-slate-700 dark:text-slate-300">No img</span>
                                        @endif
                                    </td>
                                    <td class="rounded-r-xl px-4 py-4">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('products.show', $product) }}" class="btn-info">
                                                {{ __('View') }}
                                            </a>
                                            <a href="{{ route('products.edit', $product) }}" class="btn-warning">
                                                {{ __('Edit') }}
                                            </a>
                                            <form method="POST" action="{{ route('products.destroy', $product) }}" onsubmit="return confirm('{{ __('Delete this product?') }}')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn-danger">
                                                    {{ __('Delete') }}
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-16 text-center text-slate-500 dark:text-slate-300">
                                        <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-8 dark:border-slate-600 dark:bg-slate-900/40">
                                            {{ __('No products found.') }}
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

