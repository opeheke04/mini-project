<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\StockRestock;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockRestockTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_record_a_restock_and_see_its_history(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $product = Product::factory()->create(['name' => 'Kopi Susu', 'stock' => 3]);

        $response = $this->actingAs($admin)->post(route('stock-restocks.store'), [
            'product_id' => $product->id,
            'quantity' => 12,
            'supplier' => 'PT Pemasok',
            'received_at' => '2026-10-25T10:15',
            'notes' => 'Kardus lengkap',
        ]);

        $response->assertRedirect(route('stock-restocks.index'));
        $this->assertDatabaseHas('products', ['id' => $product->id, 'stock' => 15]);
        $this->assertDatabaseHas('stock_restocks', [
            'product_id' => $product->id,
            'user_id' => $admin->id,
            'product_name' => 'Kopi Susu',
            'quantity' => 12,
            'supplier' => 'PT Pemasok',
            'notes' => 'Kardus lengkap',
        ]);

        $this->get(route('stock-restocks.index'))
            ->assertOk()
            ->assertSeeText('Riwayat barang masuk')
            ->assertSeeText('Kopi Susu')
            ->assertSeeText('PT Pemasok')
            ->assertSeeText('Kardus lengkap');
    }

    public function test_invalid_or_overflowing_restocks_do_not_change_stock(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $product = Product::factory()->create(['stock' => 2147483645]);

        $this->actingAs($admin)
            ->from(route('stock-restocks.index'))
            ->post(route('stock-restocks.store'), [
                'product_id' => $product->id,
                'quantity' => 3,
            ])
            ->assertRedirect(route('stock-restocks.index'))
            ->assertSessionHasErrors('quantity');

        $this->assertDatabaseHas('products', ['id' => $product->id, 'stock' => 2147483645]);
        $this->assertSame(0, StockRestock::query()->count());
    }

    public function test_only_admins_can_open_the_web_restock_page(): void
    {
        $this->get(route('stock-restocks.index'))->assertRedirect(route('login'));

        $cashier = User::factory()->create(['role' => 'kasir']);
        $this->actingAs($cashier)->get(route('stock-restocks.index'))->assertForbidden();
    }

    public function test_mobile_api_authenticates_admins_and_records_restock(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'password' => 'admin-password',
        ]);
        $product = Product::factory()->create(['name' => 'Teh Botol', 'stock' => 4]);

        $this->getJson('/api/mobile/products')->assertUnauthorized();

        $login = $this->postJson('/api/mobile/login', [
            'email' => $admin->email,
            'password' => 'admin-password',
        ])->assertOk()
            ->assertJsonPath('user.id', $admin->id);

        $token = $login->json('token');
        $this->withToken($token)
            ->getJson('/api/mobile/products')
            ->assertOk()
            ->assertJsonPath('low_stock_threshold', config('inventory.low_stock_threshold'))
            ->assertJsonPath('data.0.name', 'Teh Botol');

        $this->postJson('/api/mobile/restocks', [
            'product_id' => $product->id,
            'quantity' => 6,
            'supplier' => 'Supplier Mobile',
        ])->assertCreated()
            ->assertJsonPath('data.quantity', 6)
            ->assertJsonPath('data.product_name', 'Teh Botol');

        $this->assertDatabaseHas('products', ['id' => $product->id, 'stock' => 10]);
    }

    public function test_mobile_api_rejects_cashiers_and_tokens_without_write_ability(): void
    {
        $cashier = User::factory()->create([
            'role' => 'kasir',
            'password' => 'cashier-password',
        ]);
        $this->postJson('/api/mobile/login', [
            'email' => $cashier->email,
            'password' => 'cashier-password',
        ])->assertForbidden();

        $admin = User::factory()->create(['role' => 'admin']);
        $product = Product::factory()->create(['stock' => 4]);
        $token = $admin->createToken('read-only-mobile', ['inventory:read'])->plainTextToken;

        $this->withToken($token)
            ->postJson('/api/mobile/restocks', [
                'product_id' => $product->id,
                'quantity' => 2,
            ])
            ->assertForbidden();

        $this->assertDatabaseHas('products', ['id' => $product->id, 'stock' => 4]);
        $this->assertSame(0, StockRestock::query()->count());
    }

    public function test_expired_mobile_tokens_cannot_access_inventory(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $token = $admin->createToken(
            'expired-mobile',
            ['inventory:read'],
            now()->subMinute(),
        )->plainTextToken;

        $this->withToken($token)->getJson('/api/mobile/products')->assertUnauthorized();
    }
}
