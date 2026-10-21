<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ \App\Support\StoreProfile::get('store_name', config('app.name')) }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="login-wallpaper font-sans text-slate-900 antialiased">
        <main class="relative flex min-h-screen items-center justify-center px-4 py-6 sm:px-6 lg:px-8">
            <div @class([
                'grid w-full max-w-6xl overflow-hidden rounded-[2rem] border border-white/70 bg-white shadow-[0_32px_100px_rgba(15,23,42,0.3)] lg:min-h-[700px]',
                'lg:grid-cols-[1.08fr,0.92fr]' => request()->routeIs('login'),
                'lg:grid-cols-1' => ! request()->routeIs('login'),
            ])>
                @if (request()->routeIs('login'))
                    <section class="login-hero relative flex min-h-[340px] flex-col justify-between overflow-hidden p-7 text-white sm:p-10 lg:min-h-full lg:p-14">
                        <div class="relative z-10">
                            <a href="/" class="inline-flex items-center gap-3">
                                @if (\App\Support\StoreProfile::get('logo') !== '')
                                    <img src="{{ asset('storage/'.\App\Support\StoreProfile::get('logo')) }}" alt="" class="h-11 w-11 rounded-2xl border border-white/20 bg-white object-contain p-1">
                                @else
                                    <span class="flex h-11 w-11 items-center justify-center rounded-2xl border border-white/20 bg-white/10 shadow-lg backdrop-blur">
                                        <x-application-logo class="h-7 w-7 text-amber-300" />
                                    </span>
                                @endif
                                <span class="text-sm font-bold uppercase tracking-[0.14em] text-white/90">{{ \App\Support\StoreProfile::get('store_name', config('app.name')) }}</span>
                            </a>

                            <p class="mt-12 inline-flex items-center gap-2 rounded-full border border-amber-200/25 bg-amber-100/10 px-3 py-1.5 text-[10px] font-bold uppercase tracking-[0.2em] text-amber-200 sm:mt-16">
                                <span class="h-1.5 w-1.5 rounded-full bg-amber-300"></span>
                                Pengelolaan toko yang cerdas
                            </p>
                            <h1 class="mt-5 max-w-lg text-3xl font-black leading-tight tracking-tight sm:text-4xl lg:text-[2.75rem]">
                                Operasional toko, <span class="text-amber-300">lebih terkontrol.</span>
                            </h1>
                            <p class="mt-4 max-w-md text-sm leading-7 text-slate-200 sm:text-base">
                                Satu tempat untuk mengelola produk, memantau stok, dan melayani transaksi dengan lebih praktis.
                            </p>
                        </div>

                        <div class="relative z-10 mt-8 lg:mt-0">
                            <img
                                src="{{ asset('images/login-grocery-hero.svg') }}"
                                alt="Ilustrasi produk kebutuhan toko"
                                class="mx-auto w-full max-w-[430px] drop-shadow-[0_28px_35px_rgba(0,0,0,0.25)]"
                            >
                            <div class="mt-5 flex flex-wrap gap-x-6 gap-y-2 text-xs font-medium text-white/75">
                                <span class="inline-flex items-center gap-2"><span class="text-amber-300">✦</span> Stok lebih tertata</span>
                                <span class="inline-flex items-center gap-2"><span class="text-amber-300">✦</span> Transaksi lebih cepat</span>
                            </div>
                        </div>
                        <div class="pointer-events-none absolute -right-24 -top-28 h-80 w-80 rounded-full border border-white/10"></div>
                        <div class="pointer-events-none absolute -right-12 -top-16 h-56 w-56 rounded-full border border-white/10"></div>
                    </section>
                @endif

                <section class="flex items-center justify-center bg-[#fcfcfb] px-5 py-10 sm:px-10 lg:px-12">
                    <div class="w-full max-w-md">
                        @unless (request()->routeIs('login'))
                            <div class="mb-8 flex items-center justify-center">
                                <a href="/" class="inline-flex items-center gap-3 rounded-full border border-slate-200 bg-white px-4 py-2 shadow-sm">
                                    @if (\App\Support\StoreProfile::get('logo') !== '')
                                        <img src="{{ asset('storage/'.\App\Support\StoreProfile::get('logo')) }}" alt="" class="h-10 w-10 rounded-lg object-contain">
                                    @else
                                        <x-application-logo class="h-10 w-10 text-amber-700" />
                                    @endif
                                    <span class="text-sm font-bold uppercase tracking-[0.14em] text-slate-600">{{ \App\Support\StoreProfile::get('store_name', config('app.name')) }}</span>
                                </a>
                            </div>
                        @endunless

                        <div @class([
                            'rounded-2xl border border-slate-200/80 bg-white p-5 shadow-[0_18px_55px_rgba(15,23,42,0.07)] sm:p-8',
                            'login-form-card' => request()->routeIs('login'),
                        ])>
                            {{ $slot }}
                        </div>
                    </div>
                </section>
            </div>
        </main>
    </body>
</html>