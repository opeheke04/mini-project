<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

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
                <header class="border-b border-slate-200/80 bg-white/75 shadow-[0_8px_24px_rgba(15,23,42,0.04)] backdrop-blur-sm dark:border-slate-700 dark:bg-slate-900/80">
                    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <main class="pb-10">
                {{ $slot }}
            </main>

            @stack('scripts')
        </div>
    </body>
</html>
