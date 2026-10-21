<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ \App\Support\StoreProfile::get('store_name', config('app.name')) }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased text-slate-800">
        <div class="min-h-screen bg-shell">
            @include('layouts.navigation')

            @isset($header)
                <header class="border-b border-slate-200/70 bg-white/70 shadow-[0_8px_24px_rgba(15,23,42,0.035)] backdrop-blur-sm">
                    <div class="mx-auto max-w-7xl px-4 py-5 sm:px-6 sm:py-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <main class="pb-10">
                @if (session('success') || session('error'))
                    <div class="mx-auto max-w-7xl px-4 pt-4 sm:px-6 lg:px-8">
                        @if (session('success'))
                            <div role="status" class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800 shadow-sm">
                                <span class="mr-2" aria-hidden="true">✓</span>{{ session('success') }}
                            </div>
                        @endif
                        @if (session('error'))
                            <div role="alert" class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-800 shadow-sm">
                                <span class="mr-2" aria-hidden="true">!</span>{{ session('error') }}
                            </div>
                        @endif
                    </div>
                @endif
                {{ $slot }}
            </main>

            @stack('scripts')
        </div>
    </body>
</html>
