@csrf

<div>
    <label class="block text-sm font-semibold text-slate-700" for="name">
        {{ __('Name') }}
    </label>
    <input id="name" name="name" type="text" value="{{ old('name', $product->name ?? '') }}" required
           >
    @error('name')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>

<div>
    <label class="block text-sm font-semibold text-slate-700" for="category">
        {{ __('Category') }}
    </label>
    <input id="category" name="category" type="text" value="{{ old('category', $product->category ?? '') }}"
           >
    @error('category')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>

<div>
    <label class="block text-sm font-semibold text-slate-700" for="description">
        {{ __('Description') }}
    </label>
    <textarea id="description" name="description" rows="4"
              >{{ old('description', $product->description ?? '') }}</textarea>
    @error('description')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>

<div class="grid gap-6 sm:grid-cols-2">
    <div>
        <label class="block text-sm font-semibold text-slate-700" for="price">
            {{ __('Price') }}
        </label>
        <input id="price" name="price" type="number" min="0" step="0.01" value="{{ old('price', $product->price ?? '') }}" required
               >
        @error('price')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-slate-700" for="stock">
            {{ __('Stock') }}
        </label>
        <input id="stock" name="stock" type="number" min="0" value="{{ old('stock', $product->stock ?? 0) }}" required
               >
        @error('stock')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>
</div>

<div>
    <label class="inline-flex items-center">
        <input name="is_active" type="checkbox" value="1" @checked(old('is_active', $product->is_active ?? true))
               class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
        <span class="ml-2 text-sm font-semibold text-slate-700">{{ __('Active') }}</span>
    </label>
</div>

<div>
    <label class="block text-sm font-semibold text-slate-700" for="image">
        {{ __('Image') }}
    </label>
    <input id="image" name="image" type="file" accept="image/jpeg,image/png,image/jpg,image/gif,image/webp"
           >

    @if (isset($product) && $product->image)
        <img class="mt-3 h-24 w-24 rounded-md object-cover shadow-sm" src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}">
    @endif

    @error('image')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>

<div class="mt-8 flex flex-col-reverse gap-3 border-t border-slate-200 pt-6 sm:flex-row sm:items-center sm:justify-between">
    <a href="{{ route('products.index') }}" class="btn-secondary px-5 py-3 text-sm">
        {{ __('Cancel') }}
    </a>

    <button type="submit" class="btn-primary w-full gap-2 px-6 py-4 text-base uppercase tracking-[0.12em] sm:w-auto">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 3" />
        </svg>
        <span class="whitespace-nowrap">{{ $submitLabel === 'Save' ? __('Save Product') : $submitLabel }}</span>
    </button>
</div>
