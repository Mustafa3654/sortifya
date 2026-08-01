@php
    $locale = app()->getLocale();
    $locales = config('sortifya.locales');
    $onHome = request()->routeIs('home');
@endphp

{{--
    Sticky, translucent, and thin. It sits over a page whose hero is the point,
    so it stays out of the way: hairline bottom rule, no shadow, no fill until
    the page scrolls under it.
--}}
<header
    x-data="{ scrolled: false, mobile: false }"
    x-on:scroll.window="scrolled = window.scrollY > 12"
    :class="scrolled
        ? 'border-slate-200/80 bg-mist-50/85 dark:border-slate-800/80 dark:bg-ink-950/85'
        : 'border-transparent bg-transparent'"
    class="fixed inset-x-0 top-0 z-50 border-b backdrop-blur-xl transition-colors duration-300"
>
    <nav class="mx-auto flex h-16 max-w-7xl items-center gap-3 px-4 sm:px-6 lg:px-8" aria-label="{{ __('sortifya.nav.menu') }}">

        {{-- Mark --}}
        <a href="{{ route('home') }}" class="group flex shrink-0 items-center gap-2.5 rounded-lg">
            <x-logo :size="30" class="transition-transform duration-300 group-hover:rotate-[-6deg]" />
            <span class="font-display text-[17px] font-bold tracking-tightest">
                <span class="gradient-text">Sortifya</span>
            </span>
        </a>

        {{-- Public links. Anchors only resolve on the landing page, so off it
             they are rewritten to point back home first. --}}
        <div class="mx-2 hidden items-center gap-1 md:flex">
            @foreach ([
                ['label' => __('sortifya.nav.home'), 'href' => route('home'), 'anchor' => false],
                ['label' => __('sortifya.nav.how'), 'href' => $onHome ? '#how-it-works' : route('home').'#how-it-works', 'anchor' => true],
                ['label' => __('sortifya.nav.tasks'), 'href' => $onHome ? '#tasks' : route('home').'#tasks', 'anchor' => true],
                ['label' => __('sortifya.nav.faq'), 'href' => route('faq'), 'anchor' => false],
                ['label' => __('sortifya.nav.contact'), 'href' => route('contact'), 'anchor' => false],
            ] as $link)
                <a href="{{ $link['href'] }}"
                   class="rounded-lg px-3 py-2 text-sm font-medium text-slate-600 transition-colors
                          hover:bg-slate-900/5 hover:text-slate-900
                          dark:text-slate-400 dark:hover:bg-white/5 dark:hover:text-white">
                    {{ $link['label'] }}
                </a>
            @endforeach
        </div>

        <div class="flex-1"></div>

        {{-- Utilities --}}
        <div class="flex items-center gap-1.5">

            {{-- Language. Below `sm` there is no room for it beside the CTA,
                 so it moves into the mobile sheet instead of being squeezed. --}}
            <div x-data="{ open: false }" x-on:keydown.escape="open = false" class="relative hidden sm:block">
                <button
                    type="button"
                    x-on:click="open = !open"
                    :aria-expanded="open.toString()"
                    aria-haspopup="true"
                    class="flex h-9 items-center gap-1.5 rounded-lg px-2.5 text-slate-500 transition-colors
                           hover:bg-slate-900/5 hover:text-slate-900 dark:text-slate-400 dark:hover:bg-white/5 dark:hover:text-white"
                >
                    <x-lucide name="languages" :size="17" />
                    <span class="font-mono text-xs font-semibold uppercase">{{ $locale }}</span>
                </button>

                <div
                    x-show="open"
                    x-cloak
                    x-on:click.outside="open = false"
                    x-transition:enter="transition ease-out duration-150"
                    x-transition:enter-start="opacity-0 -translate-y-1 scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                    x-transition:leave="transition ease-in duration-100"
                    x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95"
                    class="panel absolute end-0 mt-2 w-44 origin-top overflow-hidden p-1 shadow-panel"
                >
                    <p class="px-2.5 pb-1 pt-1.5 text-[10px] font-semibold uppercase tracking-widest text-slate-400">
                        {{ __('sortifya.nav.language') }}
                    </p>
                    @foreach ($locales as $code => $meta)
                        <a href="{{ route('locale.switch', $code) }}"
                           class="flex items-center justify-between rounded-lg px-2.5 py-2 text-sm transition-colors
                                  {{ $code === $locale
                                      ? 'bg-emerald-500/10 font-semibold text-emerald-600 dark:text-emerald-400'
                                      : 'text-slate-600 hover:bg-slate-900/5 dark:text-slate-300 dark:hover:bg-white/5' }}">
                            <span>{{ $meta['native'] }}</span>
                            @if ($code === $locale)
                                <x-lucide name="check" :size="15" />
                            @else
                                <span class="font-mono text-[10px] uppercase text-slate-400">{{ $code }}</span>
                            @endif
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- Theme --}}
            <button
                type="button"
                x-on:click="$store.theme.toggle()"
                :aria-label="$store.theme.dark ? @js(__('sortifya.nav.theme_light')) : @js(__('sortifya.nav.theme_dark'))"
                class="flex h-9 w-9 items-center justify-center rounded-lg text-slate-500 transition-colors
                       hover:bg-slate-900/5 hover:text-slate-900 dark:text-slate-400 dark:hover:bg-white/5 dark:hover:text-white"
            >
                <span x-show="!$store.theme.dark" x-cloak><x-lucide name="moon" :size="17" /></span>
                <span x-show="$store.theme.dark" x-cloak><x-lucide name="sun" :size="17" /></span>
            </button>

            <div class="mx-1 hidden h-5 w-px bg-slate-200 dark:bg-slate-800 sm:block"></div>

            @guest
                <a href="{{ route('login') }}"
                   class="hidden rounded-lg px-3 py-2 text-sm font-semibold text-slate-600 transition-colors
                          hover:text-slate-900 dark:text-slate-300 dark:hover:text-white sm:block">
                    {{ __('sortifya.nav.sign_in') }}
                </a>
                <a href="{{ route('register') }}" class="btn-primary !px-4 !py-2 text-[13px]">
                    {{ __('sortifya.nav.start') }}
                    <x-lucide name="arrow-right" :size="15" class="flip-rtl" />
                </a>
            @endguest

            @auth
                @php($balance = auth()->user()->balance())

                {{-- Balance badge. Mono, tabular, always two decimals — it is
                     the number people come back to check. --}}
                <a href="{{ route('wallet') }}"
                   class="hidden items-center gap-1.5 rounded-lg border border-emerald-500/25 bg-emerald-500/10 px-2.5 py-1.5
                          transition-colors hover:border-emerald-500/50 sm:flex"
                   title="{{ __('sortifya.nav.balance') }}">
                    <x-lucide name="wallet" :size="15" class="text-emerald-600 dark:text-emerald-400" />
                    <span class="numeric font-mono text-[13px] font-semibold text-emerald-700 dark:text-emerald-300">
                        ${{ number_format($balance, 2) }}
                    </span>
                </a>

                <a href="{{ route('dashboard') }}"
                   class="hidden rounded-lg px-3 py-2 text-sm font-semibold text-slate-600 transition-colors
                          hover:text-slate-900 dark:text-slate-300 dark:hover:text-white md:block">
                    {{ __('sortifya.nav.dashboard') }}
                </a>

                {{-- Account --}}
                <div x-data="{ open: false }" x-on:keydown.escape="open = false" class="relative">
                    <button
                        type="button"
                        x-on:click="open = !open"
                        :aria-expanded="open.toString()"
                        aria-haspopup="true"
                        class="flex h-9 items-center gap-1.5 rounded-lg ps-1 pe-2 transition-colors hover:bg-slate-900/5 dark:hover:bg-white/5"
                    >
                        <span class="flex h-7 w-7 items-center justify-center rounded-md bg-gradient-to-br from-emerald-500 to-teal-500
                                     font-display text-xs font-bold text-white">
                            {{ Str::upper(Str::substr(auth()->user()->name, 0, 1)) }}
                        </span>
                        <x-lucide name="chevron-down" :size="14" class="text-slate-400" />
                    </button>

                    <div
                        x-show="open"
                        x-cloak
                        x-on:click.outside="open = false"
                        x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 -translate-y-1 scale-95"
                        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                        x-transition:leave="transition ease-in duration-100"
                        x-transition:leave-start="opacity-100 scale-100"
                        x-transition:leave-end="opacity-0 scale-95"
                        class="panel absolute end-0 mt-2 w-60 origin-top overflow-hidden p-1 shadow-panel"
                    >
                        <div class="border-b border-slate-200 px-3 py-2.5 dark:border-slate-800">
                            <p class="truncate text-sm font-semibold text-slate-900 dark:text-white">{{ auth()->user()->name }}</p>
                            <p class="truncate font-mono text-[11px] text-slate-400">{{ auth()->user()->email }}</p>
                        </div>

                        <div class="p-1">
                            @foreach ([
                                ['icon' => 'layout-grid', 'label' => __('sortifya.nav.dashboard'), 'href' => route('dashboard')],
                                ['icon' => 'wallet', 'label' => __('sortifya.nav.wallet'), 'href' => route('wallet')],
                                ['icon' => 'user', 'label' => __('sortifya.nav.profile'), 'href' => route('profile.edit')],
                            ] as $item)
                                <a href="{{ $item['href'] }}"
                                   class="flex items-center gap-2.5 rounded-lg px-2.5 py-2 text-sm text-slate-600 transition-colors
                                          hover:bg-slate-900/5 dark:text-slate-300 dark:hover:bg-white/5">
                                    <x-lucide :name="$item['icon']" :size="16" class="text-slate-400" />
                                    {{ $item['label'] }}
                                </a>
                            @endforeach

                            @if (auth()->user()->isAdmin())
                                {{-- Named route, not a literal "/admin": a root-relative
                                     path 404s whenever the app is served from a
                                     subdirectory, which is the norm under XAMPP. --}}
                                <a href="{{ route('filament.admin.pages.dashboard') }}"
                                   class="flex items-center gap-2.5 rounded-lg px-2.5 py-2 text-sm text-amber-600 transition-colors
                                          hover:bg-amber-500/10 dark:text-amber-400">
                                    <x-lucide name="shield-check" :size="16" />
                                    {{ __('sortifya.nav.admin') }}
                                </a>
                            @endif
                        </div>

                        <div class="border-t border-slate-200 p-1 dark:border-slate-800">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                        class="flex w-full items-center gap-2.5 rounded-lg px-2.5 py-2 text-sm text-slate-600 transition-colors
                                               hover:bg-rose-500/10 hover:text-rose-600 dark:text-slate-300 dark:hover:text-rose-400">
                                    <x-lucide name="log-out" :size="16" class="text-slate-400 flip-rtl" />
                                    {{ __('sortifya.nav.log_out') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endauth

            {{-- Mobile menu toggle --}}
            <button
                type="button"
                x-on:click="mobile = !mobile"
                :aria-expanded="mobile.toString()"
                aria-label="{{ __('sortifya.nav.open_menu') }}"
                class="flex h-9 w-9 items-center justify-center rounded-lg text-slate-500 transition-colors
                       hover:bg-slate-900/5 dark:text-slate-400 dark:hover:bg-white/5 md:hidden"
            >
                <span x-show="!mobile"><x-lucide name="menu" :size="18" /></span>
                <span x-show="mobile" x-cloak><x-lucide name="x" :size="18" /></span>
            </button>
        </div>
    </nav>

    {{-- Mobile sheet --}}
    <div
        x-show="mobile"
        x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="border-t border-slate-200 bg-mist-50/95 px-4 py-3 backdrop-blur-xl dark:border-slate-800 dark:bg-ink-950/95 md:hidden"
    >
        <div class="flex flex-col gap-0.5">
            <a href="{{ route('home') }}" class="rounded-lg px-3 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-900/5 dark:text-slate-300 dark:hover:bg-white/5">{{ __('sortifya.nav.home') }}</a>
            <a href="{{ $onHome ? '#how-it-works' : route('home').'#how-it-works' }}" x-on:click="mobile = false" class="rounded-lg px-3 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-900/5 dark:text-slate-300 dark:hover:bg-white/5">{{ __('sortifya.nav.how') }}</a>
            <a href="{{ $onHome ? '#tasks' : route('home').'#tasks' }}" x-on:click="mobile = false" class="rounded-lg px-3 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-900/5 dark:text-slate-300 dark:hover:bg-white/5">{{ __('sortifya.nav.tasks') }}</a>
            <a href="{{ route('faq') }}" class="rounded-lg px-3 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-900/5 dark:text-slate-300 dark:hover:bg-white/5">{{ __('sortifya.nav.faq') }}</a>
            <a href="{{ route('contact') }}" class="rounded-lg px-3 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-900/5 dark:text-slate-300 dark:hover:bg-white/5">{{ __('sortifya.nav.contact') }}</a>

            @auth
                <div class="my-1.5 h-px bg-slate-200 dark:bg-slate-800"></div>
                <a href="{{ route('dashboard') }}" class="rounded-lg px-3 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-900/5 dark:text-slate-300 dark:hover:bg-white/5">{{ __('sortifya.nav.dashboard') }}</a>
                <a href="{{ route('wallet') }}" class="flex items-center justify-between rounded-lg px-3 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-900/5 dark:text-slate-300 dark:hover:bg-white/5">
                    {{ __('sortifya.nav.wallet') }}
                    <span class="numeric font-mono text-[13px] font-semibold text-emerald-600 dark:text-emerald-400">${{ number_format(auth()->user()->balance(), 2) }}</span>
                </a>
            @endauth

            @guest
                <div class="my-1.5 h-px bg-slate-200 dark:bg-slate-800"></div>
                <a href="{{ route('login') }}" class="rounded-lg px-3 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-900/5 dark:text-slate-300 dark:hover:bg-white/5">{{ __('sortifya.nav.sign_in') }}</a>
            @endguest

            {{-- Language lives here on mobile; the header row has no room. --}}
            <div class="my-1.5 h-px bg-slate-200 dark:bg-slate-800 sm:hidden"></div>
            <div class="flex items-center gap-2 px-3 py-2 sm:hidden">
                <span class="text-[11px] font-semibold uppercase tracking-widest text-slate-400">
                    {{ __('sortifya.nav.language') }}
                </span>
                <div class="ms-auto inline-flex rounded-lg border border-slate-200 p-0.5 dark:border-slate-800">
                    @foreach ($locales as $code => $meta)
                        <a href="{{ route('locale.switch', $code) }}"
                           class="rounded-md px-2.5 py-1 text-xs font-semibold transition-colors
                                  {{ $code === $locale
                                      ? 'bg-gradient-to-r from-emerald-500 to-teal-500 text-white'
                                      : 'text-slate-500 hover:text-slate-900 dark:hover:text-white' }}">
                            {{ $meta['native'] }}
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</header>
