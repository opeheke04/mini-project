<?php

namespace App\Support;

use App\Models\StoreSetting;
use Illuminate\Support\Facades\Schema;

class StoreProfile
{
    /**
     * @return array<string, string>
     */
    public static function all(): array
    {
        return [
            'store_name' => self::get('store_name', 'POLIJE MART'),
            'address' => self::get('address', 'Jl. Mastrip No. 164, Jember'),
            'phone' => self::get('phone', '0812-3456-7890'),
            'footer' => self::get('footer', 'Terima Kasih Atas Kunjungan Anda'),
            'logo' => self::get('logo'),
            'printer.enabled' => self::get('printer.enabled', 'false'),
            'printer.driver' => self::get('printer.driver', 'windows'),
            'printer.name' => self::get('printer.name', 'POS-58'),
            'printer.host' => self::get('printer.host', '192.168.1.100'),
            'printer.port' => self::get('printer.port', '9100'),
        ];
    }

    public static function get(string $key, string|bool|int $default = ''): string
    {
        try {
            $value = Schema::hasTable('store_settings')
                ? StoreSetting::query()->where('key', $key)->value('value')
                : null;

            if (is_string($value) && $value !== '') {
                return $value;
            }
        } catch (\Throwable) {
            // Ignore missing or unavailable settings table and fall back to env values.
        }

        return (string) env(self::envKey($key), $default);
    }

    public static function set(string $key, string $value): void
    {
        try {
            if (! Schema::hasTable('store_settings')) {
                return;
            }

            StoreSetting::query()->updateOrCreate(
                ['key' => $key],
                ['value' => trim($value)]
            );
        } catch (\Throwable) {
            // Ignore missing or unavailable settings table.
        }
    }

    protected static function envKey(string $key): string
    {
        $normalized = str_replace('.', '_', $key);

        return match ($normalized) {
            'store_name' => 'STORE_NAME',
            'address' => 'STORE_ADDRESS',
            'phone' => 'STORE_PHONE',
            'footer' => 'STORE_FOOTER',
            'printer_enabled' => 'PRINTER_ENABLED',
            'printer_driver' => 'ESC_POS_DRIVER',
            'printer_name' => 'ESC_POS_PRINTER_NAME',
            'printer_host' => 'ESC_POS_PRINTER_HOST',
            'printer_port' => 'ESC_POS_PRINTER_PORT',
            default => strtoupper(str_replace('-', '_', $normalized)),
        };
    }
}
