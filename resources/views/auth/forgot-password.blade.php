<x-guest-layout>
    <div class="mb-4 text-sm leading-6 text-slate-600">
        Lupa kata sandi? Masukkan alamat email Anda, lalu kami akan mengirimkan tautan untuk membuat kata sandi baru.
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" value="Email" />
            <x-text-input id="email" class="mt-1 block w-full rounded-xl border-slate-300 bg-slate-50 px-4 py-3 focus:border-amber-500 focus:ring-amber-200" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>
                Kirim tautan reset kata sandi
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
