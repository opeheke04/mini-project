<?php

namespace Tests\Feature;

use App\Events\OrderPlaced;
use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class CashierTransactionTest extends TestCase
{
    use RefreshDatabase;

    public function test_payment_is_saved_and_stock_is_decreased(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create([
            'name' => 'Kopi',
            'price' => 12500,
            'stock' => 5,
            'is_active' => true,
        ]);
        Event::fake();

        $response = $this->actingAs($user)->postJson('/cashier/transactions', [
            'items' => [['product_id' => $product->id, 'quantity' => 2]],
            'customer_type' => 'Umum',
            'amount_paid' => 30000,
            'payment_method' => 'Tunai',
        ]);

        $sale = Sale::firstOrFail();
        $response->assertCreated()
            ->assertJsonPath('transaction.total', '25000.00')
            ->assertJsonPath('transaction.change', '5000.00')
            ->assertJsonPath('transaction.print_url', route('cashier.receipt.preview', $sale));

        Event::assertDispatched(OrderPlaced::class, fn (OrderPlaced $event): bool => $event->order->is($sale)
            && $event->user->is($user));

        $this->assertDatabaseHas('sales', [
            'user_id' => $user->id,
            'subtotal' => 25000,
            'total' => 25000,
            'amount_paid' => 30000,
            'payment_method' => 'Tunai',
        ]);
        $this->assertDatabaseHas('sale_items', [
            'product_id' => $product->id,
            'product_name' => 'Kopi',
            'quantity' => 2,
            'line_total' => 25000,
        ]);
        $this->assertSame(3, $product->fresh()->stock);
    }

    public function test_payment_is_rejected_when_stock_is_insufficient(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create([
            'stock' => 1,
            'is_active' => true,
        ]);

        $this->actingAs($user)->postJson('/cashier/transactions', [
            'items' => [['product_id' => $product->id, 'quantity' => 2]],
            'amount_paid' => 100000,
            'payment_method' => 'Tunai',
        ])->assertUnprocessable();

        $this->assertSame(0, Sale::count());
        $this->assertSame(1, $product->fresh()->stock);
    }

    public function test_payment_is_rejected_when_amount_paid_is_too_low(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create([
            'price' => 10000,
            'stock' => 3,
            'is_active' => true,
        ]);

        $this->actingAs($user)->postJson('/cashier/transactions', [
            'items' => [['product_id' => $product->id, 'quantity' => 1]],
            'amount_paid' => 9999,
            'payment_method' => 'Tunai',
        ])->assertUnprocessable();

        $this->assertSame(0, Sale::count());
        $this->assertSame(3, $product->fresh()->stock);
    }
}