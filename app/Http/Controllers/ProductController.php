<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\Drivers\Imagick\Driver as ImagickDriver;
use Intervention\Image\ImageManager;
use RuntimeException;

class ProductController extends Controller
{
    /**
     * Menampilkan semua product.
     */
    public function index(Request $request)
    {
        $validated = $request->validate([
            'stock' => ['nullable', Rule::in(['low'])],
        ]);

        $lowStockThreshold = config('inventory.low_stock_threshold');
        $products = Product::query()
            ->when(($validated['stock'] ?? null) === 'low', fn ($query) => $query->where('stock', '<=', $lowStockThreshold))
            ->orderByRaw('CASE WHEN stock <= ? THEN 0 ELSE 1 END', [$lowStockThreshold])
            ->orderBy('stock')
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('products.index', [
            'products' => $products,
            'lowStockThreshold' => $lowStockThreshold,
            'showingLowStock' => ($validated['stock'] ?? null) === 'low',
        ]);
    }

    /**
     * Menampilkan form tambah product.
     */
    public function create()
    {
        return view('products.create');
    }

    /**
     * Menyimpan product baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'barcode'     => ['nullable', 'string', 'max:64', Rule::unique('products', 'barcode')],
            'category'    => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'price'       => 'required|numeric|min:0',
            'stock'       => 'required|integer|min:0',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'is_active'   => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        if ($request->hasFile('image')) {
            $validated['image'] = $this->storeProductImage($request->file('image'));
        }

        Product::create($validated);

        return redirect()
            ->route('products.index')
            ->with('success', 'Produk berhasil ditambahkan.');
    }

    /**
     * Menampilkan detail product.
     */
    public function show(Product $product)
    {
        return view('products.show', compact('product'));
    }

    /**
     * Menampilkan form edit.
     */
    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

    /**
     * Mengupdate product.
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'barcode'     => ['nullable', 'string', 'max:64', Rule::unique('products', 'barcode')->ignore($product->id)],
            'category'    => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'price'       => 'required|numeric|min:0',
            'stock'       => 'required|integer|min:0',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'is_active'   => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        if ($request->hasFile('image')) {
            $path = $this->storeProductImage($request->file('image'));

            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }

            $validated['image'] = $path;
        }

        $product->update($validated);

        return redirect()
            ->route('products.index')
            ->with('success', 'Produk berhasil diperbarui.');
    }

    /**
     * Menghapus product.
     */
    public function destroy(Product $product)
    {
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return redirect()
            ->route('products.index')
            ->with('success', 'Produk berhasil dihapus.');
    }

    private function storeProductImage(UploadedFile $imageFile): string
    {
        $disk = Storage::disk('public');
        $driver = match (true) {
            extension_loaded('gd') => new GdDriver(),
            extension_loaded('imagick') => new ImagickDriver(),
            default => null,
        };

        if ($driver !== null) {
            $image = (new ImageManager($driver))
                ->read($imageFile->getRealPath())
                ->cover(800, 800);

            $filename = time() . '_' . Str::random(10) . '.jpg';
            $path = 'products/' . $filename;

            $disk->makeDirectory('products');

            if (! $disk->put($path, (string) $image->toJpeg(90))) {
                throw new RuntimeException('Gagal menyimpan foto produk.');
            }

            return $path;
        }

        $extension = $imageFile->guessExtension() ?: $imageFile->getClientOriginalExtension();
        $filename = time() . '_' . Str::random(10) . '.' . $extension;
        $path = $disk->putFileAs('products', $imageFile, $filename);

        if (! is_string($path)) {
            throw new RuntimeException('Gagal menyimpan foto produk.');
        }

        return $path;
    }
}
