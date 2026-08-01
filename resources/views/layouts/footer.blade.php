@php
    $locale = app()->getLocale();
    $onHome = request()->routeIs('home');
@endphp

<footer class="relative mt-24 border-t border-slate-200 dark:border-slate-800">
    {{-- A single ambient source, low and centred, so the page fades out
         rather than stopping at a hard edge. --}}
    <div class="ambient inset-x-1/4 -top-24 h-48" aria-hidden="true"></div>

    <div class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8">
        {{-- Two-up from `sm`, and only at `lg` does the asymmetric 12-column
             split engage — at `md` the narrow columns are too tight for the
             language pills and push the page sideways. --}}
        <div class="grid gap-10 sm:grid-cols-2 lg:grid-cols-12">

            {{-- 4 + 2 + 2 + 2 + 2 = 12. Adding a column means re-checking this. --}}
            <div class="sm:col-span-2 lg:col-span-4">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-2.5">
                    <x-logo :size="28" />
                    <span class="font-display text-base font-bold tracking-tightest gradient-text">Sortifya</span>
                </a>
                <p class="mt-3.5 max-w-sm text-sm leading-relaxed text-slate-500 dark:text-slate-400">
                    {{ __('sortifya.footer.blurb') }}
                </p>
            </div>

            <div class="lg:col-span-2">
                <h2 class="mb-3 text-[11px] font-semibold uppercase tracking-widest text-slate-400">
                    {{ __('sortifya.footer.navigate') }}
                </h2>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('home') }}" class="text-slate-600 transition-colors hover:text-emerald-600 dark:text-slate-400 dark:hover:text-emerald-400">{{ __('sortifya.nav.home') }}</a></li>
                    <li><a href="{{ $onHome ? '#how-it-works' : route('home').'#how-it-works' }}" class="text-slate-600 transition-colors hover:text-emerald-600 dark:text-slate-400 dark:hover:text-emerald-400">{{ __('sortifya.nav.how') }}</a></li>
                    <li><a href="{{ $onHome ? '#tasks' : route('home').'#tasks' }}" class="text-slate-600 transition-colors hover:text-emerald-600 dark:text-slate-400 dark:hover:text-emerald-400">{{ __('sortifya.nav.tasks') }}</a></li>
                    <li><a href="{{ route('faq') }}" class="text-slate-600 transition-colors hover:text-emerald-600 dark:text-slate-400 dark:hover:text-emerald-400">{{ __('sortifya.nav.faq') }}</a></li>
                </ul>
            </div>

            <div class="lg:col-span-2">
                <h2 class="mb-3 text-[11px] font-semibold uppercase tracking-widest text-slate-400">
                    {{ __('sortifya.footer.support') }}
                </h2>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('contact') }}" class="text-slate-600 transition-colors hover:text-emerald-600 dark:text-slate-400 dark:hover:text-emerald-400">{{ __('sortifya.nav.contact') }}</a></li>
                    <li><a href="{{ route('terms') }}" class="text-slate-600 transition-colors hover:text-emerald-600 dark:text-slate-400 dark:hover:text-emerald-400">{{ __('sortifya.nav.terms') }}</a></li>
                    <li><a href="{{ route('privacy') }}" class="text-slate-600 transition-colors hover:text-emerald-600 dark:text-slate-400 dark:hover:text-emerald-400">{{ __('sortifya.nav.privacy') }}</a></li>
                </ul>
            </div>

            <div class="lg:col-span-2">
                <h2 class="mb-3 text-[11px] font-semibold uppercase tracking-widest text-slate-400">
                    {{ __('sortifya.footer.account') }}
                </h2>
                <ul class="space-y-2 text-sm">
                    @guest
                        <li><a href="{{ route('login') }}" class="text-slate-600 transition-colors hover:text-emerald-600 dark:text-slate-400 dark:hover:text-emerald-400">{{ __('sortifya.nav.sign_in') }}</a></li>
                        <li><a href="{{ route('register') }}" class="text-slate-600 transition-colors hover:text-emerald-600 dark:text-slate-400 dark:hover:text-emerald-400">{{ __('sortifya.nav.start') }}</a></li>
                    @endguest
                    @auth
                        <li><a href="{{ route('dashboard') }}" class="text-slate-600 transition-colors hover:text-emerald-600 dark:text-slate-400 dark:hover:text-emerald-400">{{ __('sortifya.nav.dashboard') }}</a></li>
                        <li><a href="{{ route('wallet') }}" class="text-slate-600 transition-colors hover:text-emerald-600 dark:text-slate-400 dark:hover:text-emerald-400">{{ __('sortifya.nav.wallet') }}</a></li>
                        <li><a href="{{ route('profile.edit') }}" class="text-slate-600 transition-colors hover:text-emerald-600 dark:text-slate-400 dark:hover:text-emerald-400">{{ __('sortifya.nav.profile') }}</a></li>
                    @endauth
                </ul>
            </div>

            <div class="lg:col-span-2">
                <h2 class="mb-3 text-[11px] font-semibold uppercase tracking-widest text-slate-400">
                    {{ __('sortifya.nav.language') }}
                </h2>
                <div class="inline-flex max-w-full flex-wrap rounded-xl border border-slate-200 p-0.5 dark:border-slate-800">
                    @foreach (config('sortifya.locales') as $code => $meta)
                        <a href="{{ route('locale.switch', $code) }}"
                           class="rounded-[10px] px-3 py-1.5 text-xs font-semibold transition-colors
                                  {{ $code === $locale
                                      ? 'bg-gradient-to-r from-emerald-500 to-teal-500 text-white'
                                      : 'text-slate-500 hover:text-slate-900 dark:hover:text-white' }}">
                            {{ $meta['native'] }}
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="mt-12 flex flex-col items-start justify-between gap-3 border-t border-slate-200 pt-6 dark:border-slate-800 sm:flex-row sm:items-center">
            <p class="font-mono text-[11px] text-slate-400">
                {{ __('sortifya.common.copyright', ['year' => now()->year]) }} {{ __('sortifya.footer.rights') }}
            </p>
            <p class="text-[11px] text-slate-400">{{ __('sortifya.footer.built') }}</p>
        </div>
    </div>
</footer>
