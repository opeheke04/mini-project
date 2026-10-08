<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LowStockAlertTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config(['inventory.low_stock_threshold' => 5]);
    }

    public function test_admin_dashboard_and_product_filter_show_low_stock_items(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Product::factory()->create(['name' => 'Susu Menipis', 'stock' => 5, 'is_active' => true]);
        Product::factory()->create(['name' => 'Stok Aman', 'stock' => 6, 'is_active' => true]);
        Product::factory()->create(['name' => 'Susu Habis', 'stock' => 0, 'is_active' => true]);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSeeText('Perlu perhatian')
            ->assertSeeText('Susu Menipis')
            ->assertSeeText('Susu Habis');

        $this->get(route('products.index', ['stock' => 'low']))
            ->assertOk()
            ->assertSeeText('Susu Menipis')
            ->assertSeeText('Susu Habis')
            ->assertDontSeeText('Stok Aman');
    }

    public function test_cashier_page_warns_when_active_products_are_low_on_stock(): void
    {
        $cashier = User::factory()->create(['role' => 'kasir']);
        Product::factory()->create(['name' => 'Minyak Tinggal', 'stock' => 2, 'is_active' => true]);
        Product::factory()->create(['name' => 'Barang Nonaktif', 'stock' => 0, 'is_active' => false]);

        $this->actingAs($cashier)
            ->get(route('cashier.index'))
            ->assertOk()
            ->assertSeeText('Perhatian stok menipis')
            ->assertSeeText('Minyak Tinggal')
            ->assertDontSeeText('Barang Nonaktif');
    }
}
