<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductImageUploadTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_replace_a_product_image_without_an_image_processing_extension(): void
    {
        Storage::fake('public');

        $admin = User::factory()->create(['role' => 'admin']);
        $product = Product::factory()->create(['image' => 'products/old.jpg']);
        Storage::disk('public')->put('products/old.jpg', 'old image');

        $response = $this
            ->actingAs($admin)
            ->put(route('products.update', $product), [
                'name' => $product->name,
                'barcode' => $product->barcode,
                'category' => $product->category,
                'description' => $product->description,
                'price' => $product->price,
                'stock' => $product->stock,
                'is_active' => true,
                'image' => UploadedFile::fake()->createWithContent(
                    'new-product.png',
                    base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+/l2cAAAAASUVORK5CYII=')
                ),
            ]);

        $response
            ->assertRedirect('/products')
            ->assertSessionHas('success', 'Produk berhasil diperbarui.');

        $product->refresh();

        $this->assertNotSame('products/old.jpg', $product->image);
        Storage::disk('public')->assertExists($product->image);
        Storage::disk('public')->assertMissing('products/old.jpg');
    }
}
