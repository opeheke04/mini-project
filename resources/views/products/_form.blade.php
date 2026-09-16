@csrf

<div>
    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300" for="name">
        {{ __('Name') }}
    </label>
    <input id="name" name="name" type="text" value="{{ old('name', $product->name ?? '') }}" required
           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
    @error('name')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>

<div>
    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300" for="category">
        {{ __('Category') }}
    </label>
    <input id="category" name="category" type="text" value="{{ old('category', $product->category ?? '') }}"
           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
    @error('category')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>

<div>
    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300" for="description">
        {{ __('Description') }}
    </label>
    <textarea id="description" name="description" rows="4"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">{{ old('description', $product->description ?? '') }}</textarea>
    @error('description')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>

<div class="grid gap-6 sm:grid-cols-2">
    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300" for="price">
            {{ __('Price') }}
        </label>
        <input id="price" name="price" type="number" min="0" step="0.01" value="{{ old('price', $product->price ?? '') }}" required
               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
        @error('price')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300" for="stock">
            {{ __('Stock') }}
        </label>
        <input id="stock" name="stock" type="number" min="0" value="{{ old('stock', $product->stock ?? 0) }}" required
               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
        @error('stock')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>
</div>

<div>
    <label class="inline-flex items-center">
        <input name="is_active" type="checkbox" value="1" @checked(old('is_active', $product->is_active ?? true))
               class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ __('Active') }}</span>
    </label>
</div>

<div>
    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300" for="image">
        {{ __('Image') }}
    </label>
    <input id="image" name="image" type="file" accept="image/jpeg,image/png,image/jpg,image/gif,image/webp"
           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">

    @if (isset($product) && $product->image)
        <img class="mt-3 h-24 w-24 rounded-md object-cover shadow-sm" src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}">
    @endif

    @error('image')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>

<div class="mt-8 flex flex-col-reverse gap-3 border-t border-gray-200 pt-6 sm:flex-row sm:items-center sm:justify-between dark:border-gray-700">
    <a href="{{ route('products.index') }}" class="btn-secondary px-5 py-3 text-sm">
        {{ __('Cancel') }}
    </a>

    <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-xl border-2 border-blue-700 bg-blue-600 px-6 py-4 text-base font-black uppercase tracking-[0.12em] text-white shadow-[0_12px_28px_rgba(37,99,235,0.45)] transition-all duration-200 ease-out hover:-translate-y-0.5 hover:bg-blue-700 hover:shadow-[0_18px_36px_rgba(37,99,235,0.55)] focus:outline-none focus:ring-4 focus:ring-blue-200 active:translate-y-0 dark:focus:ring-blue-900 sm:w-auto">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 3" />
        </svg>
        <span class="whitespace-nowrap">{{ $submitLabel === 'Save' ? __('Save Product') : $submitLabel }}</span>
    </button>
</div>

