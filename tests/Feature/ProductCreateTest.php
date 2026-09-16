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
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post('/products', [
                'name' => 'Laptop Gaming',
                'category' => 'Elektronik',
                'description' => 'Laptop untuk kebutuhan gaming dan kerja.',
                'price' => 12500000,
                'stock' => 10,
                'is_active' => true,
            ]);

        $response
            ->assertRedirect('/products')
            ->assertSessionHas('success', 'Product berhasil ditambahkan.');

        $this->assertDatabaseHas('products', [
            'name' => 'Laptop Gaming',
            'category' => 'Elektronik',
            'price' => 12500000.00,
            'stock' => 10,
            'is_active' => true,
        ]);
    }
}
