<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.2em] text-amber-700">Pengaturan</p>
            <h2 class="mt-1 text-2xl font-black text-slate-900">Profil Toko</h2>
            <p class="mt-1 text-sm text-slate-500">Ubah nama toko, alamat, nomor telepon, dan teks footer yang muncul di struk.</p>
        </div>
    </x-slot>

    <div class="mx-auto max-w-4xl px-4 py-8 sm:px-6 lg:px-8">
        <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
            <form method="POST" action="{{ route('admin.store-settings.update') }}" enctype="multipart/form-data" class="product-form space-y-5">
                @csrf

                <div class="grid gap-5 md:grid-cols-2">
                    <div>
                        <label for="store_name" class="block text-sm font-semibold text-slate-700">Nama toko</label>
                        <input id="store_name" name="store_name" type="text" value="{{ old('store_name', \App\Support\StoreProfile::get('store_name', 'POLIJE MART')) }}" required>
                        @error('store_name')<p class="mt-1 text-sm text-rose-700">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="phone" class="block text-sm font-semibold text-slate-700">Nomor telepon</label>
                        <input id="phone" name="phone" type="text" value="{{ old('phone', \App\Support\StoreProfile::get('phone', '0812-3456-7890')) }}" required>
                        @error('phone')<p class="mt-1 text-sm text-rose-700">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div>
                    <label for="address" class="block text-sm font-semibold text-slate-700">Alamat</label>
                    <input id="address" name="address" type="text" value="{{ old('address', \App\Support\StoreProfile::get('address', 'Jl. Mastrip No. 164, Jember')) }}" required>
                    @error('address')<p class="mt-1 text-sm text-rose-700">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="footer" class="block text-sm font-semibold text-slate-700">Footer struk</label>
                    <textarea id="footer" name="footer" rows="3" required>{{ old('footer', \App\Support\StoreProfile::get('footer', 'Terima Kasih Atas Kunjungan Anda')) }}</textarea>
                    @error('footer')<p class="mt-1 text-sm text-rose-700">{{ $message }}</p>@enderror
                </div>

                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                    <h3 class="mb-3 text-base font-black text-slate-900">Logo toko</h3>
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center">
                        @if (\App\Support\StoreProfile::get('logo') !== '')
                            <img src="{{ asset('storage/'.\App\Support\StoreProfile::get('logo')) }}" alt="Logo toko saat ini" class="h-20 w-20 rounded-2xl border border-slate-200 bg-white object-contain p-2">
                        @else
                            <div class="flex h-20 w-20 items-center justify-center rounded-2xl bg-white text-xs font-bold text-slate-400 ring-1 ring-slate-200">Belum ada logo</div>
                        @endif
                        <div class="min-w-0 flex-1">
                            <label for="logo" class="block text-sm font-semibold text-slate-700">Unggah logo (JPG, PNG, atau WebP, maks. 2 MB)</label>
                            <input id="logo" name="logo" type="file" accept="image/jpeg,image/png,image/webp,image/svg+xml">
                            @error('logo')<p class="mt-1 text-sm text-rose-700">{{ $message }}</p>@enderror
                            @if (\App\Support\StoreProfile::get('logo') !== '')
                                <label class="mt-3 inline-flex items-center gap-2 text-sm text-slate-600">
                                    <input type="checkbox" name="remove_logo" value="1">
                                    Hapus logo saat ini
                                </label>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                    <h3 class="mb-4 text-base font-black text-slate-900">Pengaturan Printer</h3>
                    <div class="grid gap-4 md:grid-cols-2">
                        <div class="md:col-span-2">
                            <label class="inline-flex items-center gap-2 text-sm font-semibold text-slate-700">
                                <input type="checkbox" name="printer_enabled" value="1" @checked(old('printer_enabled', filter_var(\App\Support\StoreProfile::get('printer.enabled', 'false'), FILTER_VALIDATE_BOOLEAN)))>
                                Aktifkan cetak struk otomatis
                            </label>
                        </div>

                        <div>
                            <label for="printer_driver" class="block text-sm font-semibold text-slate-700">Driver</label>
                            <select id="printer_driver" name="printer_driver" class="w-full rounded-xl border-slate-200 bg-white text-sm focus:border-amber-500 focus:ring-amber-200">
                                <option value="windows" @selected(old('printer_driver', \App\Support\StoreProfile::get('printer.driver', 'windows')) === 'windows')>Windows shared printer</option>
                                <option value="network" @selected(old('printer_driver', \App\Support\StoreProfile::get('printer.driver', 'windows')) === 'network')>Network/IP printer</option>
                            </select>
                            @error('printer_driver')<p class="mt-1 text-sm text-rose-700">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label for="printer_name" class="block text-sm font-semibold text-slate-700">Nama share printer</label>
                            <input id="printer_name" name="printer_name" type="text" value="{{ old('printer_name', \App\Support\StoreProfile::get('printer.name', 'POS-58')) }}" placeholder="POS-58">
                        </div>

                        <div>
                            <label for="printer_host" class="block text-sm font-semibold text-slate-700">Host printer</label>
                            <input id="printer_host" name="printer_host" type="text" value="{{ old('printer_host', \App\Support\StoreProfile::get('printer.host', '192.168.1.100')) }}" placeholder="192.168.1.100">
                        </div>

                        <div>
                            <label for="printer_port" class="block text-sm font-semibold text-slate-700">Port</label>
                            <input id="printer_port" name="printer_port" type="number" min="1" max="65535" value="{{ old('printer_port', \App\Support\StoreProfile::get('printer.port', '9100')) }}" placeholder="9100">
                        </div>
                    </div>
                </div>

                <div class="flex flex-wrap gap-3 pt-2">
                    <button type="submit" class="btn-primary">Simpan pengaturan</button>
                    <button type="submit" formaction="{{ route('admin.store-settings.test-print') }}" class="btn-secondary">Tes cetak</button>
                    <a href="{{ route('admin.dashboard') }}" class="btn-secondary">Kembali</a>
                </div>
            </form>
        </section>
    </div>
</x-app-layout>
