<x-guest-layout>
    <div class="mb-4 text-sm leading-6 text-slate-600">
        Terima kasih telah mendaftar. Silakan verifikasi alamat email melalui tautan yang kami kirim. Jika email belum diterima, Anda dapat meminta tautan baru.
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 text-sm font-medium text-emerald-700">
            Tautan verifikasi baru telah dikirim ke alamat email Anda.
        </div>
    @endif

    <div class="mt-4 flex items-center justify-between">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf

            <div>
                <x-primary-button>
                    Kirim ulang email verifikasi
                </x-primary-button>
            </div>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button type="submit" class="rounded-md text-sm font-semibold text-amber-800 underline hover:text-amber-950 focus:outline-none focus:ring-2 focus:ring-amber-500">
                Keluar
            </button>
        </form>
    </div>
</x-guest-layout>
