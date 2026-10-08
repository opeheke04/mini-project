<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use InvalidArgumentException;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $initialUsers = config('app.initial_users');
        $admin = $initialUsers['admin'];
        $cashier = $initialUsers['cashier'];

        if (blank($admin['email']) || blank($admin['password']) || blank($cashier['email']) || blank($cashier['password'])) {
            $this->command?->warn('Initial admin/cashier accounts were skipped. Configure unique INITIAL_*_EMAIL and INITIAL_*_PASSWORD values before seeding accounts.');
        } else {
            if (strlen($admin['password']) < 16 || strlen($cashier['password']) < 16) {
                throw new InvalidArgumentException('Initial account passwords must each be at least 16 characters long.');
            }

            if (hash_equals($admin['password'], $cashier['password'])) {
                throw new InvalidArgumentException('Initial admin and cashier passwords must be different.');
            }

            if (! filter_var($admin['email'], FILTER_VALIDATE_EMAIL)
                || ! filter_var($cashier['email'], FILTER_VALIDATE_EMAIL)
                || strcasecmp($admin['email'], $cashier['email']) === 0) {
                throw new InvalidArgumentException('Initial admin and cashier accounts must use different valid email addresses.');
            }

            foreach ([
                ['config' => $admin, 'role' => 'admin'],
                ['config' => $cashier, 'role' => 'kasir'],
            ] as $account) {
                User::updateOrCreate(
                    ['email' => $account['config']['email']],
                    [
                        'name' => $account['config']['name'],
                        'role' => $account['role'],
                        'password' => Hash::make($account['config']['password']),
                    ]
                );
            }
        }

        Product::factory()->count(5)->create();
    }
}
