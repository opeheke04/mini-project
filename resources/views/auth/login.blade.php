<x-guest-layout>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="mb-6">
        <p class="text-[11px] font-bold uppercase tracking-[0.22em] text-amber-700">Portal akses</p>
        <h2 class="mt-2 text-2xl font-black tracking-tight text-slate-900">Selamat datang kembali</h2>
        <p class="mt-2 text-sm leading-6 text-slate-500">Masuk untuk melanjutkan pengelolaan toko Anda.</p>
    </div>

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <div>
            <x-input-label for="email" :value="__('Email')" class="!text-sm !font-semibold !text-slate-700" />
            <x-text-input id="email" class="mt-1 block w-full rounded-xl border-slate-300 bg-slate-50 px-4 py-3 text-slate-800 shadow-sm transition focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-200" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password" :value="__('Password')" class="!text-sm !font-semibold !text-slate-700" />
            <x-text-input id="password" class="mt-1 block w-full rounded-xl border-slate-300 bg-slate-50 px-4 py-3 text-slate-800 shadow-sm transition focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-200"
                type="password"
                name="password"
                required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between gap-3">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="h-4 w-4 rounded border-slate-300 text-blue-600 shadow-sm focus:ring-blue-500" name="remember">
                <span class="ml-2 text-sm text-slate-600">{{ __('Remember me') }}</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-sm font-medium text-blue-600 transition hover:text-blue-500" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif
        </div>

        <div class="pt-2">
            <x-primary-button class="w-full justify-center px-5 py-3 text-sm font-bold uppercase tracking-[0.12em]">
                {{ __('Log in') }}
            </x-primary-button>
        </div>

    </form>
</x-guest-layout>
