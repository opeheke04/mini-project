<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dashboard_shows_sales_metrics_recent_transactions_and_top_products(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $cashier = User::factory()->create(['role' => 'kasir']);
        $todaySale = $this->createSale($cashier, 'TRX-DASH-TODAY', '15000.00', now());
        $this->createSale($cashier, 'TRX-DASH-YESTERDAY', '7000.00', now()->subDay());
        $this->createSale($cashier, 'TRX-DASH-LAST-WEEK', '4000.00', now()->subDays(7));

        $product = Product::factory()->create(['name' => 'Dashboard Kopi']);
        SaleItem::create([
            'sale_id' => $todaySale->id,
            'product_id' => $product->id,
            'product_name' => 'Dashboard Kopi',
            'unit_price' => '5000.00',
            'quantity' => 3,
            'line_total' => '15000.00',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSeeText('Omzet hari ini')
            ->assertSeeText('15.000')
            ->assertSeeText('Omzet bulan ini')
            ->assertSeeText('26.000')
            ->assertSeeText('Omzet 7 hari terakhir')
            ->assertSeeText('+450,0%')
            ->assertSeeText('Produk terlaris bulan ini')
            ->assertSeeText('Dashboard Kopi')
            ->assertSeeText('Transaksi terbaru')
            ->assertSeeText('TRX-DASH-TODAY');
    }

    private function createSale(User $cashier, string $number, string $total, \DateTimeInterface $createdAt): Sale
    {
        $sale = Sale::create([
            'transaction_number' => $number,
            'user_id' => $cashier->id,
            'customer_type' => 'Umum',
            'subtotal' => $total,
            'discount_percent' => 0,
            'discount_amount' => 0,
            'tax' => 0,
            'other_fee' => 0,
            'total' => $total,
            'amount_paid' => $total,
            'change_due' => 0,
            'payment_method' => 'Tunai',
        ]);
        $sale->forceFill([
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
        ])->save();

        return $sale;
    }
}
