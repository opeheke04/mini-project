<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductCreateTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_a_product(): void
    {
        $user = User::factory()->create(['role' => 'admin']);

        $response = $this
            ->actingAs($user)
            ->post('/products', [
                'name' => 'Laptop Gaming',
                'barcode' => '8991234567890',
                'category' => 'Elektronik',
                'description' => 'Laptop untuk kebutuhan gaming dan kerja.',
                'price' => 12500000,
                'stock' => 10,
                'is_active' => true,
            ]);

        $response
            ->assertRedirect('/products')
            ->assertSessionHas('success', 'Produk berhasil ditambahkan.');

        $this->assertDatabaseHas('products', [
            'name' => 'Laptop Gaming',
            'barcode' => '8991234567890',
            'category' => 'Elektronik',
            'price' => 12500000.00,
            'stock' => 10,
            'is_active' => true,
        ]);
    }

    public function test_product_barcode_must_be_unique(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        \App\Models\Product::factory()->create(['barcode' => '8991234567890']);

        $this->actingAs($admin)
            ->from('/products/create')
            ->post('/products', [
                'name' => 'Produk Duplikat',
                'barcode' => '8991234567890',
                'price' => 1000,
                'stock' => 1,
            ])
            ->assertRedirect('/products/create')
            ->assertSessionHasErrors('barcode');
    }
}
