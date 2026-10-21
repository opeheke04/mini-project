<?php

namespace App\Http\Controllers;

use App\Services\ReceiptPrinter;
use App\Support\StoreProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class StoreSettingsController extends Controller
{
    public function index(): View
    {
        return view('admin.store-settings.index', [
            'settings' => StoreProfile::all(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'store_name' => ['required', 'string', 'max:120'],
            'address' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'footer' => ['required', 'string', 'max:120'],
            'logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'remove_logo' => ['nullable', 'boolean'],
            'printer_enabled' => ['nullable', 'boolean'],
            'printer_driver' => ['required', 'in:windows,network'],
            'printer_name' => ['nullable', 'string', 'max:80'],
            'printer_host' => ['nullable', 'string', 'max:80'],
            'printer_port' => ['nullable', 'integer', 'min:1', 'max:65535'],
        ]);

        StoreProfile::set('store_name', $validated['store_name']);
        StoreProfile::set('address', $validated['address']);
        StoreProfile::set('phone', $validated['phone']);
        StoreProfile::set('footer', $validated['footer']);
        $currentLogo = StoreProfile::get('logo');
        if ($request->boolean('remove_logo') && $currentLogo !== '') {
            Storage::disk('public')->delete($currentLogo);
            StoreProfile::set('logo', '');
            $currentLogo = '';
        }
        if ($request->hasFile('logo')) {
            $newLogo = $request->file('logo')->store('store', 'public');
            if ($currentLogo !== '') {
                Storage::disk('public')->delete($currentLogo);
            }
            StoreProfile::set('logo', $newLogo);
        }
        StoreProfile::set('printer.enabled', $request->boolean('printer_enabled') ? 'true' : 'false');
        StoreProfile::set('printer.driver', $validated['printer_driver']);
        StoreProfile::set('printer.name', $validated['printer_name'] ?? 'POS-58');
        StoreProfile::set('printer.host', $validated['printer_host'] ?? '192.168.1.100');
        StoreProfile::set('printer.port', (string) ($validated['printer_port'] ?? 9100));

        return back()->with('success', 'Pengaturan toko dan printer berhasil diperbarui.');
    }

    public function testPrint(ReceiptPrinter $printer): RedirectResponse
    {
        $result = $printer->testPrint();

        if ($result) {
            return back()->with('success', 'Tes cetak berhasil dikirim ke printer.');
        }

        return back()->with('error', 'Tes cetak gagal. Periksa konfigurasi printer dan koneksi jaringan.');
    }
}
