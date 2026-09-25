<nav x-data="{ open: false }" class="border-b border-slate-200/80 bg-white/80 shadow-[0_8px_24px_rgba(15,23,42,0.04)] backdrop-blur-sm dark:border-slate-700 dark:bg-slate-900/80">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 justify-between">
            <div class="flex">
                <div class="flex shrink-0 items-center">
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center rounded-xl bg-gradient-to-br from-blue-600 to-indigo-600 p-2.5 text-white shadow-[0_10px_25px_rgba(37,99,235,0.35)]">
                        <x-application-logo class="block h-7 w-auto fill-current" />
                    </a>
                </div>

                <div class="hidden space-x-1 sm:-my-px sm:ms-8 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="rounded-lg px-3 py-2 text-sm font-semibold text-slate-600 hover:text-blue-700 dark:text-slate-300 dark:hover:text-white">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                    <x-nav-link :href="route('products.index')" :active="request()->routeIs('products.*')" class="rounded-lg px-3 py-2 text-sm font-semibold text-slate-600 hover:text-blue-700 dark:text-slate-300 dark:hover:text-white">
                        {{ __('Products') }}
                    </x-nav-link>
                    <x-nav-link :href="route('cashier.index')" :active="request()->routeIs('cashier.*')" class="rounded-lg px-3 py-2 text-sm font-semibold text-slate-600 hover:text-blue-700 dark:text-slate-300 dark:hover:text-white">
                        {{ __('Kasir') }}
                    </x-nav-link>
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:border-blue-200 hover:text-blue-700 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:hover:border-blue-500 dark:hover:text-blue-300">
                            <div>{{ Auth::user()->name }}</div>
                            <div class="ms-1">
                                <svg class="h-4 w-4 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault(); this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center rounded-lg p-2 text-slate-500 transition hover:bg-slate-100 hover:text-blue-700 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="space-y-1 pb-3 pt-2">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('products.index')" :active="request()->routeIs('products.*')">
                {{ __('Products') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('cashier.index')" :active="request()->routeIs('cashier.*')">
                {{ __('Kasir') }}
            </x-responsive-nav-link>
        </div>

        <div class="border-t border-slate-200 pb-1 pt-4 dark:border-slate-700">
            <div class="px-4">
                <div class="text-base font-medium text-slate-800 dark:text-slate-200">{{ Auth::user()->name }}</div>
                <div class="text-sm text-slate-500 dark:text-slate-400">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault(); this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
