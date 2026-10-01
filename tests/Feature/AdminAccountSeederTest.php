<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminAccountSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_and_cashier_accounts_are_seeded_with_expected_credentials(): void
    {
        $this->seed();

        $admin = User::where('email', 'admin@minimarket.test')->first();

        $this->assertNotNull($admin);
        $this->assertSame('Administrator', $admin->name);
        $this->assertSame('admin', $admin->role);
        $this->assertTrue(Hash::check('password', $admin->password));

        $cashier = User::where('email', 'kasir@minimarket.test')->first();

        $this->assertNotNull($cashier);
        $this->assertSame('kasir', $cashier->role);
        $this->assertTrue(Hash::check('password', $cashier->password));
    }
}
