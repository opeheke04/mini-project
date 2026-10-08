<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductNavigationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dashboard_has_product_navigation_link(): void
    {
        $user = User::factory()->create(['role' => 'admin']);

        $response = $this
            ->actingAs($user)
            ->get('/admin/dashboard');

        $response
            ->assertOk()
            ->assertSee('Produk');
    }

    public function test_cashier_cannot_open_product_pages(): void
    {
        $user = User::factory()->create(['role' => 'kasir']);

        $this->actingAs($user)->get('/products')->assertForbidden();
    }
}
