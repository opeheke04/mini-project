<x-guest-layout>
    <form method="POST" action="{{ route('password.store') }}">
        @csrf

        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <!-- Email Address -->
        <div>
            <x-input-label for="email" value="Email" />
            <x-text-input id="email" class="mt-1 block w-full rounded-xl border-slate-300 bg-slate-50 px-4 py-3 focus:border-amber-500 focus:ring-amber-200" type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" value="Kata sandi baru" />
            <x-text-input id="password" class="mt-1 block w-full rounded-xl border-slate-300 bg-slate-50 px-4 py-3 focus:border-amber-500 focus:ring-amber-200" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" value="Konfirmasi kata sandi baru" />

            <x-text-input id="password_confirmation" class="mt-1 block w-full rounded-xl border-slate-300 bg-slate-50 px-4 py-3 focus:border-amber-500 focus:ring-amber-200"
                                type="password"
                                name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>
                Atur ulang kata sandi
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
