<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductNavigationTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_has_product_navigation_link(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get('/dashboard');

        $response
            ->assertOk()
            ->assertSee('Products');
    }
}
