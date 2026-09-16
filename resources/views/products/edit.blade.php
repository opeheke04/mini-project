<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
            {{ __('Edit Product') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
            <div class="overflow-hidden rounded-lg bg-white p-6 shadow-sm dark:bg-gray-800">
                <form class="space-y-6" method="POST" action="{{ route('products.update', $product) }}" enctype="multipart/form-data">
                    @method('PUT')
                    @include('products._form', ['submitLabel' => __('Update')])
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

