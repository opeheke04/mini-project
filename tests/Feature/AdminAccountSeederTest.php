<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminAccountSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_account_is_seeded_with_easy_credentials(): void
    {
        $this->seed();

        $user = User::where('email', 'admin@prodi.test')->first();

        $this->assertNotNull($user);
        $this->assertEquals('Admin', $user->name);
        $this->assertTrue(Hash::check('admin123', $user->password));
    }
}
