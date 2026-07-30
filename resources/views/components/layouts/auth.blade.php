@props([
    'title' => null,
    'heading',
    'body' => null,
])

{{--
    Auth pages are a split: the form on one side, the product's own material
    on the other. The right panel shows a static fragment of the transformed
    sheet — the thing you are signing up to produce — rather than a stock
    photograph or a wall of testimonials.
--}}

<x-layouts.shell :title="$title">
    <div class="grid min-h-screen lg:grid-cols-2">

        {{-- ── Form ── --}}
        <div class="relative flex flex-col px-5 py-8 sm:px-10 lg:px-14">
            <div class="ambient -start-20 -top-24 h-72 w-72 lg:hidden" aria-hidden="true"></div>

            <header class="flex items-center justify-between">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-2.5">
                    <x-logo :size="30" />
                    <span class="font-display text-[17px] font-bold tracking-tightest gradient-text">Sortifya</span>
                </a>

                <div class="flex items-center gap-1">
                    @foreach (config('sortifya.locales') as $code => $meta)
                        <a href="{{ route('locale.switch', $code) }}"
                           class="rounded-lg px-2.5 py-1.5 font-mono text-[11px] font-semibold uppercase transition-colors
                                  {{ app()->getLocale() === $code
                                      ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400'
                                      : 'text-slate-400 hover:text-slate-700 dark:hover:text-slate-200' }}">
                            {{ $code }}
                        </a>
                    @endforeach

                    <button type="button" x-on:click="$store.theme.toggle()"
                            :aria-label="$store.theme.dark ? @js(__('sortifya.nav.theme_light')) : @js(__('sortifya.nav.theme_dark'))"
                            class="ms-1 flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 transition-colors
                                   hover:bg-slate-900/5 hover:text-slate-700 dark:hover:bg-white/5 dark:hover:text-slate-200">
                        <span x-show="!$store.theme.dark" x-cloak><x-lucide name="moon" :size="16" /></span>
                        <span x-show="$store.theme.dark" x-cloak><x-lucide name="sun" :size="16" /></span>
                    </button>
                </div>
            </header>

            <main id="main" class="flex flex-1 items-center justify-center py-10">
                <div class="w-full max-w-sm">
                    <h1 class="text-3xl font-bold">{{ $heading }}</h1>
                    @if ($body)
                        <p class="mt-2.5 text-sm leading-relaxed text-slate-500 dark:text-slate-400">{{ $body }}</p>
                    @endif

                    <x-flash class="mt-6" />

                    <div class="mt-7">
                        {{ $slot }}
                    </div>
                </div>
            </main>

            <footer class="text-center">
                <p class="font-mono text-[11px] text-slate-400">
                    {{ __('sortifya.common.copyright', ['year' => now()->year]) }}
                </p>
            </footer>
        </div>

        {{-- ── Product panel ── --}}
        <aside class="relative hidden overflow-hidden border-s border-slate-200 bg-white/40 dark:border-slate-800 dark:bg-ink-900/50 lg:flex lg:flex-col lg:justify-center">
            <div class="pointer-events-none absolute inset-0" aria-hidden="true">
                <div class="cell-grid absolute inset-0 opacity-60
                            [mask-image:radial-gradient(ellipse_70%_60%_at_50%_40%,#000_20%,transparent_100%)]"></div>
                <div class="ambient start-1/4 top-1/4 h-80 w-80 animate-drift"></div>
            </div>

            <div class="relative px-12 xl:px-16">
                <p class="mb-5 max-w-md font-display text-2xl font-semibold leading-snug tracking-tightest text-slate-900 dark:text-white">
                    {{ __('sortifya.home.hero.title_lead') }}
                    <span class="gradient-text">{{ __('sortifya.home.hero.title_accent') }}</span>
                </p>

                {{-- A finished sheet: what the work looks like when it is right. --}}
                <div class="panel max-w-md overflow-hidden shadow-glow" dir="ltr">
                    <div class="flex items-center gap-2 border-b border-slate-200 px-4 py-3 dark:border-slate-800">
                        <x-lucide name="file-spreadsheet" :size="15" class="text-emerald-500" />
                        <span class="font-mono text-[11px] text-slate-500">{{ __('sortifya.home.sheet.output') }}</span>
                        <span class="chip-ok ms-auto">100%</span>
                    </div>

                    <table class="w-full border-collapse">
                        <thead>
                            <tr>
                                <th class="w-8 border-b border-e border-slate-200 bg-slate-100/70 py-2 text-center font-mono text-[9px] text-slate-400 dark:border-slate-800 dark:bg-ink-800/70">1</th>
                                @foreach ([__('sortifya.home.sheet.col_date'), __('sortifya.home.sheet.col_vendor'), __('sortifya.home.sheet.col_amount')] as $heading)
                                    <th class="border-b border-e border-slate-200 px-3 py-2 text-start text-[10px] font-semibold uppercase tracking-wider text-slate-500 last:border-e-0 dark:border-slate-800 dark:text-slate-400">{{ $heading }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ([
                                ['2', '02-14', 'Nadeem Print Co.', '148.00'],
                                ['3', '02-16', 'Halabi Logistics', '92.40'],
                                ['4', '02-19', 'Cedar Supply Ltd.', '1,204.75'],
                                ['5', '02-23', 'Mouawad Hardware', '316.20'],
                            ] as $row)
                                <tr>
                                    <td class="w-8 border-b border-e border-slate-200 bg-slate-100/70 py-2 text-center font-mono text-[9px] text-slate-400 dark:border-slate-800 dark:bg-ink-800/70">{{ $row[0] }}</td>
                                    <td class="border-b border-e border-slate-200 px-3 py-2 font-mono text-[11px] text-slate-600 dark:border-slate-800 dark:text-slate-300">{{ $row[1] }}</td>
                                    <td class="border-b border-e border-slate-200 px-3 py-2 font-mono text-[11px] text-slate-600 dark:border-slate-800 dark:text-slate-300">{{ $row[2] }}</td>
                                    <td class="numeric border-b border-slate-200 px-3 py-2 text-end font-mono text-[11px] font-medium text-emerald-600 dark:border-slate-800 dark:text-emerald-400">${{ $row[3] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <dl class="mt-8 flex max-w-md gap-8">
                    @foreach ([
                        ['label' => __('sortifya.home.payout.min'), 'value' => '$'.number_format(config('sortifya.minimum_withdrawal'), 2)],
                        ['label' => __('sortifya.home.how.step_1_meta'), 'value' => config('sortifya.task_hold_minutes').' min'],
                    ] as $fact)
                        <div>
                            <dt class="mb-1 text-[10px] font-semibold uppercase tracking-widest text-slate-400">{{ $fact['label'] }}</dt>
                            <dd class="numeric font-mono text-xl font-semibold text-slate-900 dark:text-white">{{ $fact['value'] }}</dd>
                        </div>
                    @endforeach
                </dl>
            </div>
        </aside>
    </div>
</x-layouts.shell>
