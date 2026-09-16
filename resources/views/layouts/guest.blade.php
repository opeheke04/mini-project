<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-slate-900 antialiased">
        <div class="min-h-screen bg-[radial-gradient(circle_at_top,_#eef2ff,_#f8fafc_35%,_#e2e8f0_100%)] px-4 py-8 sm:px-6 lg:px-8">
            <div class="mx-auto flex min-h-[calc(100vh-4rem)] w-full max-w-5xl items-center justify-center">
                <div class="grid w-full overflow-hidden rounded-[28px] border border-white/60 bg-white/80 shadow-[0_24px_80px_rgba(15,23,42,0.12)] backdrop-blur-sm lg:grid-cols-[1.1fr,1fr]">
                    <div class="hidden bg-gradient-to-br from-blue-700 via-indigo-700 to-violet-700 p-10 text-white lg:flex lg:flex-col lg:justify-between">
                        <div>
                            <div class="mb-8 inline-flex items-center rounded-full border border-white/25 bg-white/10 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-blue-100">
                                Inventory System
                            </div>
                            <h1 class="text-3xl font-black leading-tight">Kelola produk dengan lebih mudah.</h1>
                            <p class="mt-4 max-w-sm text-base text-blue-100/90">
                                Akses dashboard, kelola stok, dan pantau produk dengan tampilan yang lebih rapi serta profesional.
                            </p>
                        </div>

                        <div class="rounded-2xl border border-white/15 bg-white/10 p-4 backdrop-blur-sm">
                            <p class="text-xs uppercase tracking-[0.2em] text-blue-100">Status</p>
                            <p class="mt-2 text-2xl font-bold">Online</p>
                        </div>
                    </div>

                    <div class="flex items-center justify-center p-6 sm:p-8 lg:p-10">
                        <div class="w-full max-w-md">
                            <div class="mb-8 flex items-center justify-center">
                                <a href="/" class="inline-flex items-center gap-3 rounded-full border border-slate-200 bg-white px-4 py-2 shadow-sm">
                                    <x-application-logo class="h-10 w-10 text-blue-600" />
                                    <span class="text-sm font-bold uppercase tracking-[0.2em] text-slate-600">Profile Prodi</span>
                                </a>
                            </div>

                            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-7">
                                {{ $slot }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>