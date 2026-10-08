<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.2em] text-amber-700">Katalog Toko</p>
            <h2 class="mt-1 text-2xl font-black leading-tight text-slate-900">
                {{ __('Tambah Produk') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white p-6 shadow-[0_14px_36px_rgba(15,23,42,0.08)] sm:p-8">
                <form class="product-form space-y-6" method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data">
                    @include('products._form', ['submitLabel' => __('Save')])
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
