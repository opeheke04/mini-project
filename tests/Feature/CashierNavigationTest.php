<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CashierNavigationTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_open_cashier_page(): void
    {
        $user = User::factory()->create();
        Product::factory()->create([
            'name' => 'Indomie Goreng',
            'price' => 3500,
            'stock' => 10,
            'is_active' => true,
        ]);

        $response = $this
            ->actingAs($user)
            ->get('/cashier');

        $response
            ->assertOk()
            ->assertSeeText('Transaksi Kasir')
            ->assertSeeText('Indomie Goreng')
            ->assertSeeText('Bayar');
    }

    public function test_guest_is_redirected_from_cashier_page(): void
    {
        $this->get('/cashier')->assertRedirect('/login');
    }
}
