<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_registration_routes_are_not_available(): void
    {
        $this->get('/register')->assertNotFound();

        $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'a-long-password-1234',
            'password_confirmation' => 'a-long-password-1234',
        ])->assertNotFound();

        $this->assertDatabaseMissing('users', ['email' => 'test@example.com']);
    }

    public function test_admin_can_create_cashier_account_with_a_strong_password(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->post(route('admin.cashier-accounts.store'), [
                'name' => 'Kasir Baru',
                'email' => 'cashier@example.com',
                'password' => 'a-strong-cashier-password-123',
                'password_confirmation' => 'a-strong-cashier-password-123',
            ])
            ->assertRedirect(route('admin.cashier-accounts.index'))
            ->assertSessionHas('success');

        $cashier = User::where('email', 'cashier@example.com')->firstOrFail();
        $this->assertSame('kasir', $cashier->role);
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('a-strong-cashier-password-123', $cashier->password));
    }

    public function test_non_admin_cannot_create_cashier_accounts(): void
    {
        $cashier = User::factory()->create(['role' => 'kasir']);

        $this->actingAs($cashier)
            ->post(route('admin.cashier-accounts.store'), [
                'name' => 'Another Cashier',
                'email' => 'another-cashier@example.com',
                'password' => 'a-strong-cashier-password-123',
                'password_confirmation' => 'a-strong-cashier-password-123',
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing('users', ['email' => 'another-cashier@example.com']);
    }

    public function test_cashier_password_must_be_at_least_sixteen_characters(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->from(route('admin.cashier-accounts.index'))
            ->post(route('admin.cashier-accounts.store'), [
                'name' => 'Weak Password',
                'email' => 'weak@example.com',
                'password' => 'short123',
                'password_confirmation' => 'short123',
            ])
            ->assertSessionHasErrors('password');

        $this->assertDatabaseMissing('users', ['email' => 'weak@example.com']);
    }
}
