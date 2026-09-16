<x-guest-layout>
    <div class="mb-6">
        <p class="text-xs font-bold uppercase tracking-[0.2em] text-blue-600">Create account</p>
        <h2 class="mt-2 text-2xl font-black text-slate-800">Daftar sekarang</h2>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-5">
        @csrf

        <div>
            <x-input-label for="name" :value="__('Name')" class="!text-sm !font-semibold !text-slate-700" />
            <x-text-input id="name" class="mt-1 block w-full rounded-xl border-slate-300 bg-slate-50 px-4 py-3 text-slate-800 shadow-sm transition focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-200" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" class="!text-sm !font-semibold !text-slate-700" />
            <x-text-input id="email" class="mt-1 block w-full rounded-xl border-slate-300 bg-slate-50 px-4 py-3 text-slate-800 shadow-sm transition focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-200" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password" :value="__('Password')" class="!text-sm !font-semibold !text-slate-700" />
            <x-text-input id="password" class="mt-1 block w-full rounded-xl border-slate-300 bg-slate-50 px-4 py-3 text-slate-800 shadow-sm transition focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-200"
                type="password"
                name="password"
                required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" class="!text-sm !font-semibold !text-slate-700" />
            <x-text-input id="password_confirmation" class="mt-1 block w-full rounded-xl border-slate-300 bg-slate-50 px-4 py-3 text-slate-800 shadow-sm transition focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-200"
                type="password"
                name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="pt-2">
            <x-primary-button class="w-full justify-center px-5 py-3 text-sm font-bold uppercase tracking-[0.12em]">
                {{ __('Register') }}
            </x-primary-button>
        </div>

        <div class="pt-2 text-center text-sm text-slate-600">
            Sudah punya akun?
            <a href="{{ route('login') }}" class="font-semibold text-blue-600 hover:text-blue-500">Masuk</a>
        </div>
    </form>
</x-guest-layout>
