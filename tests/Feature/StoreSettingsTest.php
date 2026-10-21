<?php

namespace Tests\Feature;

use App\Models\StoreSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class StoreSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_update_store_brand_and_upload_a_logo(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->post(route('admin.store-settings.update'), [
                'store_name' => 'Toko Ceria',
                'address' => 'Jl. Merdeka 10',
                'phone' => '081234567890',
                'footer' => 'Terima kasih!',
                'printer_driver' => 'windows',
                'printer_name' => 'POS-58',
                'printer_host' => '192.168.1.100',
                'printer_port' => 9100,
                'logo' => UploadedFile::fake()->createWithContent(
                    'logo.png',
                    base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+/a8sAAAAASUVORK5CYII=')
                ),
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $logoPath = StoreSetting::query()->where('key', 'logo')->value('value');
        $this->assertDatabaseHas('store_settings', ['key' => 'store_name', 'value' => 'Toko Ceria']);
        $this->assertNotEmpty($logoPath);
        Storage::disk('public')->assertExists($logoPath);

        $this->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSeeText('Toko Ceria')
            ->assertSee($logoPath, false);
    }

    public function test_cashier_cannot_open_store_settings(): void
    {
        $cashier = User::factory()->create(['role' => 'kasir']);

        $this->actingAs($cashier)
            ->get(route('admin.store-settings.index'))
            ->assertForbidden();
    }
}
