<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SalesReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_filter_sales_and_view_daily_and_monthly_summaries(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->createSale('TRX-JAN-001', '12000.00', '2026-01-08 10:00:00');
        $this->createSale('TRX-JAN-002', '8000.00', '2026-01-08 14:00:00');
        $this->createSale('TRX-FEB-001', '5000.00', '2026-02-03 09:00:00');

        $daily = $this->actingAs($admin)->get(route('sales.index', [
            'from' => '2026-01-01',
            'to' => '2026-01-31',
            'period' => 'day',
        ]));

        $daily->assertOk()
            ->assertSeeText('Jumlah Transaksi')
            ->assertSeeText('20.000')
            ->assertSeeText('TRX-JAN-001')
            ->assertDontSeeText('TRX-FEB-001');

        $monthly = $this->actingAs($admin)->get(route('sales.index', [
            'from' => '2026-01-01',
            'to' => '2026-02-28',
            'period' => 'month',
        ]));

        $monthly->assertOk()
            ->assertSeeText('Januari 2026')
            ->assertSeeText('Februari 2026')
            ->assertSeeText('25.000');
    }

    public function test_admin_can_search_transactions_by_product_and_open_details(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $product = Product::factory()->create(['name' => 'Kopi Latte']);
        $sale = $this->createSale('TRX-SEARCH-001', '15000.00', now()->toDateTimeString(), [
            'customer_type' => 'Member VIP',
            'customer_contact' => '081234567890',
        ]);
        SaleItem::create([
            'sale_id' => $sale->id,
            'product_id' => $product->id,
            'product_name' => 'Kopi Latte',
            'unit_price' => 15000,
            'quantity' => 1,
            'line_total' => 15000,
        ]);

        $this->actingAs($admin)
            ->get(route('sales.index', ['search' => 'Kopi Latte']))
            ->assertOk()
            ->assertSeeText('TRX-SEARCH-001');

        $this->get(route('sales.show', $sale))
            ->assertOk()
            ->assertSeeText('Kopi Latte')
            ->assertSeeText('Member VIP')
            ->assertSeeText('081234567890');
    }

    public function test_admin_can_filter_reports_by_cashier_and_payment_method(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $cashier = User::factory()->create(['role' => 'kasir', 'name' => 'Kasir Filter']);
        $matching = $this->createSale('TRX-FILTER-001', '12000.00', now()->toDateTimeString(), [
            'user_id' => $cashier->id,
            'payment_method' => 'QRIS',
        ]);
        $this->createSale('TRX-FILTER-002', '8000.00', now()->toDateTimeString(), [
            'payment_method' => 'Tunai',
        ]);

        $this->actingAs($admin)
            ->get(route('sales.index', [
                'from' => now()->toDateString(),
                'to' => now()->toDateString(),
                'cashier_id' => $cashier->id,
                'payment_method' => 'QRIS',
            ]))
            ->assertOk()
            ->assertSeeText($matching->transaction_number)
            ->assertDontSeeText('TRX-FILTER-002')
            ->assertSeeText('12.000');
    }

    public function test_guests_and_cashiers_cannot_view_sales_reports(): void
    {
        $sale = $this->createSale('TRX-RESTRICTED-001', '1000.00', now()->toDateTimeString());

        $this->get(route('sales.index'))->assertRedirect(route('login'));

        $cashier = User::factory()->create(['role' => 'kasir']);
        $this->actingAs($cashier)->get(route('sales.index'))->assertForbidden();
        $this->get(route('sales.show', $sale))->assertForbidden();
    }

    public function test_authorized_user_can_open_thermal_receipt_preview(): void
    {
        $cashier = User::factory()->create(['role' => 'kasir']);
        $sale = $this->createSale('TRX-PRINT-001', '15000.00', now()->toDateTimeString());

        $this->actingAs($cashier)
            ->get(route('cashier.receipt.preview', $sale))
            ->assertOk()
            ->assertSee('size: 58mm auto', false)
            ->assertSeeText('TRX-PRINT-001')
            ->assertSee('window.print()', false);
    }

    private function createSale(string $number, string $total, string $createdAt, array $overrides = []): Sale
    {
        $sale = Sale::create(array_merge([
            'transaction_number' => $number,
            'user_id' => User::factory()->create(['role' => 'kasir'])->id,
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
        ], $overrides));
        $sale->forceFill(['created_at' => $createdAt, 'updated_at' => $createdAt])->save();

        return $sale;
    }
}
