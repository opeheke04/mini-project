<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MiddlewareTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_from_admin_route(): void
    {
        $this->get('/admin')->assertRedirect('/login');
    }

    public function test_admin_can_access_admin_route(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->get('/admin')
            ->assertOk()
            ->assertSeeText('Selamat datang di halaman admin!');
    }

    public function test_non_admin_is_forbidden_from_admin_route(): void
    {
        $cashier = User::factory()->create(['role' => 'kasir']);

        $this->actingAs($cashier)->get('/admin')->assertForbidden();
    }

    public function test_role_parameter_allows_matching_user(): void
    {
        $cashier = User::factory()->create(['role' => 'kasir']);

        $this->actingAs($cashier)
            ->get('/role-check')
            ->assertOk()
            ->assertSeeText('Akses kasir diberikan.');
    }

    public function test_role_parameter_forbids_non_matching_user(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->get('/role-check')->assertForbidden();
    }
}