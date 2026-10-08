<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.2em] text-amber-700">Administrasi</p>
            <h2 class="mt-1 text-2xl font-black text-slate-900">Akun Kasir</h2>
            <p class="mt-1 text-sm text-slate-500">Akun kasir hanya dapat dibuat oleh administrator.</p>
        </div>
    </x-slot>

    <div class="mx-auto grid max-w-7xl gap-6 px-4 py-8 sm:px-6 lg:grid-cols-[minmax(0,0.85fr)_minmax(0,1.15fr)] lg:px-8">
        <section class="h-fit rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
            <div class="mb-5">
                <h3 class="font-black text-slate-900">Buat akun kasir</h3>
                <p class="mt-1 text-sm text-slate-500">Gunakan password unik minimal 16 karakter, dengan huruf dan angka.</p>
            </div>

            @if (session('success'))
                <div role="status" class="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800">
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('admin.cashier-accounts.store') }}" class="product-form space-y-4">
                @csrf

                <div>
                    <label for="name" class="block text-sm font-semibold text-slate-700">Nama kasir</label>
                    <input id="name" name="name" type="text" value="{{ old('name') }}" autocomplete="name" required>
                    @error('name')<p class="mt-1 text-sm text-rose-700">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-semibold text-slate-700">Email</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required>
                    @error('email')<p class="mt-1 text-sm text-rose-700">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-semibold text-slate-700">Password sementara</label>
                    <input id="password" name="password" type="password" autocomplete="new-password" minlength="16" required>
                    @error('password')<p class="mt-1 text-sm text-rose-700">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-semibold text-slate-700">Ulangi password</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" minlength="16" required>
                </div>

                <button type="submit" class="btn-primary w-full">Buat akun kasir</button>
            </form>
        </section>

        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-100 px-5 py-4 sm:px-6">
                <h3 class="font-black text-slate-900">Kasir terdaftar</h3>
                <p class="mt-1 text-xs text-slate-500">{{ $cashiers->total() }} akun kasir</p>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse ($cashiers as $cashier)
                    <article class="flex items-center gap-3 px-5 py-4 sm:px-6">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-slate-900 text-sm font-black text-amber-300">
                            {{ mb_strtoupper(mb_substr($cashier->name, 0, 1)) }}
                        </span>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-bold text-slate-900">{{ $cashier->name }}</p>
                            <p class="truncate text-xs text-slate-500">{{ $cashier->email }}</p>
                        </div>
                        <span class="rounded-full bg-emerald-50 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide text-emerald-800">Kasir</span>
                    </article>
                @empty
                    <p class="px-5 py-10 text-center text-sm text-slate-500">Belum ada akun kasir.</p>
                @endforelse
            </div>
            @if ($cashiers->hasPages())
                <div class="border-t border-slate-100 p-4">{{ $cashiers->links() }}</div>
            @endif
        </section>
    </div>
</x-app-layout>
