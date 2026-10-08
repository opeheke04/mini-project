<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminAccountSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_and_cashier_accounts_are_seeded_from_configured_credentials(): void
    {
        config([
            'app.initial_users.admin.email' => 'admin@example.test',
            'app.initial_users.admin.password' => 'admin-unique-password-123',
            'app.initial_users.cashier.email' => 'cashier@example.test',
            'app.initial_users.cashier.password' => 'cashier-unique-password-456',
        ]);

        $this->seed();

        $admin = User::where('email', 'admin@example.test')->first();

        $this->assertNotNull($admin);
        $this->assertSame('Administrator', $admin->name);
        $this->assertSame('admin', $admin->role);
        $this->assertTrue(Hash::check('admin-unique-password-123', $admin->password));

        $cashier = User::where('email', 'cashier@example.test')->first();

        $this->assertNotNull($cashier);
        $this->assertSame('kasir', $cashier->role);
        $this->assertTrue(Hash::check('cashier-unique-password-456', $cashier->password));
    }

    public function test_accounts_are_not_created_without_configured_credentials(): void
    {
        config([
            'app.initial_users.admin.email' => null,
            'app.initial_users.admin.password' => null,
            'app.initial_users.cashier.email' => null,
            'app.initial_users.cashier.password' => null,
        ]);

        $this->seed();

        $this->assertDatabaseCount('users', 0);
    }
}
