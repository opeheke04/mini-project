<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.22em] text-indigo-600">
                    Product Detail
                </p>
                <h2 class="mt-1 text-2xl font-bold leading-tight text-gray-800 dark:text-gray-200">
                    {{ $product->name }}
                </h2>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-[0_10px_30px_rgba(15,23,42,0.08)] dark:border-slate-700 dark:bg-gray-800">
                <div class="p-5 sm:p-8 text-gray-900 dark:text-gray-100">
                    <div class="grid gap-8 lg:grid-cols-[1.08fr,1.5fr]">
                        <div class="rounded-2xl bg-slate-50 p-3 ring-1 ring-slate-200 dark:bg-slate-900 dark:ring-slate-700">
                            @if ($product->image)
                                <img class="h-[360px] w-full rounded-xl object-cover shadow-sm" src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}">
                            @else
                                <div class="flex h-[360px] w-full items-center justify-center rounded-xl border border-dashed border-slate-300 bg-slate-100 text-slate-500 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-300">
                                    {{ __('No image') }}
                                </div>
                            @endif
                        </div>

                        <div class="space-y-6">
                            <div class="flex items-center justify-between gap-4">
                                <span class="inline-flex rounded-full px-3 py-1.5 text-xs font-bold {{ $product->is_active ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300' : 'bg-slate-200 text-slate-700 dark:bg-slate-700 dark:text-slate-200' }}">
                                    {{ $product->is_active ? __('Active') : __('Inactive') }}
                                </span>
                                <span class="text-lg font-bold text-blue-600 dark:text-blue-400">
                                    Rp {{ number_format((float) $product->price, 0, ',', '.') }}
                                </span>
                            </div>

                            <div class="rounded-2xl bg-slate-50 p-5 ring-1 ring-slate-200 dark:bg-slate-900 dark:ring-slate-700">
                                <dl class="space-y-5">
                                    <div>
                                        <dt class="text-xs font-bold uppercase tracking-[0.18em] text-slate-500 dark:text-slate-400">{{ __('Category') }}</dt>
                                        <dd class="mt-1 text-base font-medium text-slate-800 dark:text-slate-100">{{ $product->category ?: '-' }}</dd>
                                    </div>

                                    <div>
                                        <dt class="text-xs font-bold uppercase tracking-[0.18em] text-slate-500 dark:text-slate-400">{{ __('Stock') }}</dt>
                                        <dd class="mt-1 text-base font-medium text-slate-800 dark:text-slate-100">{{ $product->stock }}</dd>
                                    </div>

                                    <div>
                                        <dt class="text-xs font-bold uppercase tracking-[0.18em] text-slate-500 dark:text-slate-400">{{ __('Description') }}</dt>
                                        <dd class="mt-2 whitespace-pre-line text-base leading-7 text-slate-700 dark:text-slate-200">{{ $product->description ?: '-' }}</dd>
                                    </div>
                                </dl>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 flex flex-wrap gap-3 border-t border-slate-200 pt-6 dark:border-slate-700">
                        <a href="{{ route('products.edit', $product) }}" class="btn-primary px-5 py-2.5 text-sm">
                            {{ __('Edit') }}
                        </a>
                        <a href="{{ route('products.index') }}" class="btn-secondary px-5 py-2.5 text-sm">
                            {{ __('Back') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

