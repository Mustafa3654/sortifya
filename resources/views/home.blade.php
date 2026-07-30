<x-layouts.app flush :description="__('sortifya.home.hero.body')">

    {{-- ══════════════════════════════════════════════════════════════════
         HERO
         The thesis, stated twice: once in words on the left, once as the
         actual transformation on the right. The right panel is the page's
         signature — ragged scan lines committing, row by row, into an
         aligned ledger.
         ══════════════════════════════════════════════════════════════ --}}
    <section class="relative overflow-hidden pt-28 pb-16 sm:pt-36 sm:pb-24">

        {{-- Ambient light, plus the cell grid the whole product is about,
             faded out toward the edges. --}}
        <div class="pointer-events-none absolute inset-0 -z-10" aria-hidden="true">
            <div class="cell-grid absolute inset-0 opacity-70
                        [mask-image:radial-gradient(ellipse_75%_55%_at_50%_0%,#000_25%,transparent_100%)]"></div>
            <div class="ambient -top-32 start-1/4 h-[26rem] w-[26rem] animate-drift"></div>
            <div class="ambient -top-16 end-0 h-[22rem] w-[22rem]"></div>
        </div>

        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <x-flash class="mb-8" />

            <div class="grid items-center gap-12 lg:grid-cols-12 lg:gap-10">

                {{-- ── Copy ── --}}
                <div class="lg:col-span-6 xl:col-span-5" data-aos="fade-up">

                    <span class="inline-flex items-center gap-2 rounded-full border border-emerald-500/25 bg-emerald-500/10
                                 px-3 py-1.5 font-mono text-[11px] font-medium uppercase tracking-widest text-emerald-600
                                 dark:text-emerald-400">
                        <span class="relative flex h-1.5 w-1.5">
                            <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-500 opacity-75"></span>
                            <span class="relative inline-flex h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                        </span>
                        {{ __('sortifya.home.eyebrow') }}
                    </span>

                    <h1 class="mt-6 text-[2.6rem] font-bold leading-[1.05] sm:text-6xl lg:text-[3.5rem] xl:text-6xl">
                        {{ __('sortifya.home.hero.title_lead') }}
                        <span class="gradient-text">{{ __('sortifya.home.hero.title_accent') }}</span>
                        {{ __('sortifya.home.hero.title_tail') }}
                    </h1>

                    <p class="mt-6 max-w-xl text-[15px] leading-relaxed text-slate-600 dark:text-slate-400 sm:text-base">
                        {{ __('sortifya.home.hero.body') }}
                    </p>

                    <div class="mt-9 flex flex-wrap items-center gap-3">
                        <a href="{{ auth()->check() ? route('dashboard') : route('register') }}"
                           class="btn-primary !px-6 !py-3">
                            {{ __('sortifya.home.hero.cta_primary') }}
                            <x-lucide name="arrow-right" :size="17" class="flip-rtl" />
                        </a>
                        <a href="#how-it-works" class="btn-ghost !px-6 !py-3">
                            {{ __('sortifya.home.hero.cta_secondary') }}
                        </a>
                    </div>

                    <p class="mt-5 text-[13px] text-slate-400">{{ __('sortifya.home.hero.note') }}</p>
                </div>

                {{-- ── Signature: the transformation ── --}}
                <div class="lg:col-span-6 xl:col-span-7" data-aos="fade-up" data-aos-delay="120">
                    <div x-data="sheetHero()" dir="ltr"
                         class="relative grid gap-3 sm:grid-cols-[minmax(0,0.78fr)_minmax(0,1fr)]">

                        {{-- Source: a scan. Skewed, soft, unaligned. --}}
                        <div class="panel relative overflow-hidden p-4 shadow-panel">
                            <div class="mb-3 flex items-center gap-2 border-b border-slate-200 pb-3 dark:border-slate-800">
                                <x-lucide name="file-text" :size="15" class="text-rose-500" />
                                <span class="truncate font-mono text-[11px] text-slate-500">{{ __('sortifya.home.sheet.source') }}</span>
                            </div>

                            <p class="mb-3 text-[10px] font-semibold uppercase tracking-widest text-slate-400">
                                {{ __('sortifya.home.sheet.source_note') }}
                            </p>

                            {{-- Ragged ink. Each line straightens and sharpens
                                 the moment its row is committed. --}}
                            <div class="space-y-2.5">
                                <template x-for="(row, i) in rows" :key="'src-' + i">
                                    <div class="flex items-center gap-2 transition-all duration-500"
                                         :class="isCommitted(i) ? 'opacity-30' : 'opacity-100'"
                                         :style="`transform: rotate(${isCommitted(i) ? 0 : (i % 2 ? -0.7 : 0.6)}deg)`">
                                        <div class="h-[7px] rounded-full bg-slate-300 transition-all duration-500 dark:bg-slate-700"
                                             :style="`width: ${34 + (i * 9) % 26}%`"></div>
                                        <div class="h-[7px] flex-1 rounded-full bg-slate-200 transition-all duration-500 dark:bg-slate-800"></div>
                                        <div class="h-[7px] w-8 rounded-full bg-slate-300 dark:bg-slate-700"></div>
                                    </div>
                                </template>
                            </div>

                            <div class="mt-5 space-y-2 opacity-40">
                                <div class="h-[7px] w-4/5 rounded-full bg-slate-200 dark:bg-slate-800"></div>
                                <div class="h-[7px] w-2/3 rounded-full bg-slate-200 dark:bg-slate-800"></div>
                                <div class="h-[7px] w-3/4 rounded-full bg-slate-200 dark:bg-slate-800"></div>
                            </div>
                        </div>

                        {{-- Output: the sheet. --}}
                        <div class="panel relative overflow-hidden shadow-glow">
                            <div class="flex items-center gap-2 border-b border-slate-200 px-4 py-3 dark:border-slate-800">
                                <x-lucide name="file-spreadsheet" :size="15" class="text-emerald-500" />
                                <span class="truncate font-mono text-[11px] text-slate-500">{{ __('sortifya.home.sheet.output') }}</span>
                                <span class="ms-auto numeric font-mono text-[11px] font-semibold text-emerald-500"
                                      x-text="progress + '%'">0%</span>
                            </div>

                            <div class="overflow-x-auto">
                                <table class="w-full border-collapse text-start">
                                    <thead>
                                        {{-- Real spreadsheet column letters. --}}
                                        <tr class="bg-slate-100/70 dark:bg-ink-800/70">
                                            <th class="w-8 border-b border-e border-slate-200 py-1 text-center font-mono text-[9px] font-medium text-slate-400 dark:border-slate-800"></th>
                                            @foreach (['A', 'B', 'C'] as $column)
                                                <th class="border-b border-e border-slate-200 py-1 text-center font-mono text-[9px] font-medium text-slate-400 last:border-e-0 dark:border-slate-800">{{ $column }}</th>
                                            @endforeach
                                        </tr>
                                        <tr>
                                            <th class="w-8 border-b border-e border-slate-200 bg-slate-100/70 py-2 text-center font-mono text-[9px] text-slate-400 dark:border-slate-800 dark:bg-ink-800/70">1</th>
                                            @foreach ([__('sortifya.home.sheet.col_date'), __('sortifya.home.sheet.col_vendor'), __('sortifya.home.sheet.col_amount')] as $heading)
                                                <th class="border-b border-e border-slate-200 px-3 py-2 text-start text-[10px] font-semibold uppercase tracking-wider text-slate-500 last:border-e-0 dark:border-slate-800 dark:text-slate-400">{{ $heading }}</th>
                                            @endforeach
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <template x-for="(row, i) in rows" :key="'out-' + i">
                                            <tr class="transition-colors duration-300"
                                                :class="i === committed ? 'bg-emerald-500/10' : ''">
                                                <td class="w-8 border-b border-e border-slate-200 bg-slate-100/70 py-2 text-center font-mono text-[9px] text-slate-400 dark:border-slate-800 dark:bg-ink-800/70"
                                                    x-text="row.ref"></td>

                                                <template x-for="(key, c) in ['date', 'vendor', 'amount']" :key="c">
                                                    <td class="border-b border-e border-slate-200 px-3 py-2 font-mono text-[11px] last:border-e-0 dark:border-slate-800"
                                                        :class="[
                                                            key === 'amount' ? 'text-end numeric' : 'text-start',
                                                            key === 'amount' && isCommitted(i) ? 'text-emerald-600 dark:text-emerald-400 font-medium' : 'text-slate-600 dark:text-slate-300',
                                                        ]">
                                                        <span x-show="isCommitted(i)" class="animate-cell-in inline-block"
                                                              :style="`animation-delay: ${c * 70}ms`"
                                                              x-text="key === 'amount' ? '$' + row[key] : row[key]"></span>

                                                        {{-- Empty cell: a caret waiting on the active row. --}}
                                                        <span x-show="!isCommitted(i)" class="inline-block">
                                                            <span x-show="i === committed"
                                                                  class="inline-block h-3 w-[1.5px] animate-caret bg-emerald-500 align-middle"></span>
                                                            <span x-show="i !== committed" class="text-transparent">—</span>
                                                        </span>
                                                    </td>
                                                </template>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>

                            <div class="flex items-center gap-3 border-t border-slate-200 px-4 py-2.5 dark:border-slate-800">
                                <span class="text-[10px] font-semibold uppercase tracking-widest text-slate-400">
                                    {{ __('sortifya.home.sheet.progress') }}
                                </span>
                                <div class="h-1 flex-1 overflow-hidden rounded-full bg-slate-200 dark:bg-ink-800">
                                    <div class="h-full rounded-full bg-gradient-to-r from-emerald-500 to-teal-400 transition-all duration-500"
                                         :style="`width: ${progress}%`"></div>
                                </div>
                                <span class="numeric font-mono text-[11px] text-slate-500"
                                      x-text="committed + '/' + rows.length">0/5</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ══════════════════════════════════════════════════════════════════
         STATS — rendered as a ledger strip, not four floating cards.
         ══════════════════════════════════════════════════════════════ --}}
    <section class="border-y border-slate-200 bg-white/50 dark:border-slate-800 dark:bg-ink-900/40" aria-label="{{ __('sortifya.home.stats.title') }}">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <dl class="grid divide-y divide-slate-200 dark:divide-slate-800 sm:grid-cols-2 sm:divide-y-0 lg:grid-cols-4
                       sm:[&>div]:border-e sm:[&>div]:border-slate-200 dark:sm:[&>div]:border-slate-800
                       sm:[&>div:nth-child(2)]:border-e-0 lg:[&>div:nth-child(2)]:border-e lg:[&>div:last-child]:border-e-0
                       sm:[&>div:nth-child(-n+2)]:border-b lg:[&>div]:border-b-0 sm:[&>div:nth-child(-n+2)]:border-slate-200
                       dark:sm:[&>div:nth-child(-n+2)]:border-slate-800">

                @foreach ([
                    ['icon' => 'table-2',    'value' => $stats['rows'],          'label' => __('sortifya.home.stats.rows'),     'decimals' => 0, 'prefix' => '',  'suffix' => ''],
                    ['icon' => 'hand-coins', 'value' => $stats['paid'],          'label' => __('sortifya.home.stats.paid'),     'decimals' => 2, 'prefix' => '$', 'suffix' => ''],
                    ['icon' => 'inbox',      'value' => $stats['open_tasks'],    'label' => __('sortifya.home.stats.tasks'),    'decimals' => 0, 'prefix' => '',  'suffix' => ''],
                    ['icon' => 'circle-check-big', 'value' => $stats['approval_rate'], 'label' => __('sortifya.home.stats.approval'), 'decimals' => 0, 'prefix' => '',  'suffix' => '%'],
                ] as $index => $stat)
                    <div class="px-5 py-7 sm:px-7" data-aos="fade-up" data-aos-delay="{{ $index * 70 }}">
                        <dt class="mb-2.5 flex items-center gap-2 text-[11px] font-semibold uppercase tracking-widest text-slate-400">
                            <x-lucide :name="$stat['icon']" :size="14" class="text-emerald-500" />
                            {{ $stat['label'] }}
                        </dt>
                        <dd x-data="counter({{ $stat['value'] }}, { decimals: {{ $stat['decimals'] }}, prefix: @js($stat['prefix']), suffix: @js($stat['suffix']) })"
                            class="numeric font-mono text-3xl font-semibold tracking-tight text-slate-900 dark:text-white"
                            x-text="display">{{ $stat['prefix'] }}{{ number_format($stat['value'], $stat['decimals']) }}{{ $stat['suffix'] }}</dd>
                    </div>
                @endforeach
            </dl>
        </div>
    </section>

    {{-- ══════════════════════════════════════════════════════════════════
         HOW IT WORKS
         Genuinely a sequence, so the steps are numbered — as spreadsheet
         row references, because that is what the content actually is.
         ══════════════════════════════════════════════════════════════ --}}
    <section id="how-it-works" class="relative scroll-mt-24 py-20 sm:py-28">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            <div class="max-w-2xl" data-aos="fade-up">
                <span class="cell-ref">{{ __('sortifya.home.how.eyebrow') }}</span>
                <h2 class="mt-4 text-3xl font-bold sm:text-4xl">{{ __('sortifya.home.how.title') }}</h2>
                <p class="mt-4 text-[15px] leading-relaxed text-slate-600 dark:text-slate-400">
                    {{ __('sortifya.home.how.body') }}
                </p>
            </div>

            <div class="relative mt-14">
                {{-- The data flowing between the steps. Hidden on mobile,
                     where the cards stack and the line would mislead. --}}
                <div class="pointer-events-none absolute inset-x-[16%] top-[3.25rem] hidden border-t-2 border-dashed border-slate-200 dark:border-slate-800 lg:block"
                     aria-hidden="true"></div>

                <ol class="relative grid gap-5 lg:grid-cols-3">
                    @foreach ([
                        ['ref' => 'R1', 'icon' => 'file-text',        'key' => 1],
                        ['ref' => 'R2', 'icon' => 'table-properties', 'key' => 2],
                        ['ref' => 'R3', 'icon' => 'hand-coins',       'key' => 3],
                    ] as $index => $step)
                        <li class="panel panel-hover group relative p-6"
                            data-aos="fade-up" data-aos-delay="{{ $index * 110 }}">

                            <div class="mb-5 flex items-center gap-3">
                                <span class="flex h-[3.25rem] w-[3.25rem] items-center justify-center rounded-xl
                                             border border-slate-200 bg-white text-slate-500 transition-all duration-300
                                             group-hover:border-emerald-500/40 group-hover:text-emerald-500
                                             dark:border-slate-800 dark:bg-ink-850">
                                    <x-lucide :name="$step['icon']" :size="22" />
                                </span>
                                <span class="cell-ref">{{ $step['ref'] }}</span>
                            </div>

                            <h3 class="mb-2 text-[17px] font-semibold">
                                {{ __("sortifya.home.how.step_{$step['key']}_title") }}
                            </h3>
                            <p class="mb-5 text-sm leading-relaxed text-slate-600 dark:text-slate-400">
                                {{ __("sortifya.home.how.step_{$step['key']}_body") }}
                            </p>

                            <span class="inline-flex items-center gap-1.5 rounded-lg bg-slate-100 px-2.5 py-1
                                         font-mono text-[11px] text-slate-500 dark:bg-ink-800 dark:text-slate-400">
                                <x-lucide name="clock" :size="12" />
                                {{ __("sortifya.home.how.step_{$step['key']}_meta") }}
                            </span>
                        </li>
                    @endforeach
                </ol>
            </div>
        </div>
    </section>

    {{-- ══════════════════════════════════════════════════════════════════
         LIVE QUEUE
         ══════════════════════════════════════════════════════════════ --}}
    <section id="tasks" class="relative scroll-mt-24 pb-20 sm:pb-28">
        <div class="ambient inset-x-1/3 top-1/4 h-64" aria-hidden="true"></div>

        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mb-10 flex flex-wrap items-end justify-between gap-4" data-aos="fade-up">
                <div class="max-w-2xl">
                    <span class="cell-ref">{{ __('sortifya.home.tasks.eyebrow') }}</span>
                    <h2 class="mt-4 text-3xl font-bold sm:text-4xl">{{ __('sortifya.home.tasks.title') }}</h2>
                    <p class="mt-3 text-[15px] text-slate-600 dark:text-slate-400">{{ __('sortifya.home.tasks.body') }}</p>
                </div>

                @auth
                    <a href="{{ route('dashboard') }}" class="btn-ghost !py-2 text-[13px]">
                        {{ __('sortifya.home.tasks.view_all') }}
                        <x-lucide name="arrow-right" :size="15" class="flip-rtl" />
                    </a>
                @endauth
            </div>

            @if ($tasks->isEmpty())
                {{-- An empty screen is an invitation to act, not an apology. --}}
                <div class="panel flex flex-col items-center px-6 py-16 text-center" data-aos="fade-up">
                    <span class="mb-4 flex h-14 w-14 items-center justify-center rounded-2xl border border-slate-200 bg-white text-slate-400 dark:border-slate-800 dark:bg-ink-850">
                        <x-lucide name="inbox" :size="24" />
                    </span>
                    <h3 class="mb-2 text-lg font-semibold">{{ __('sortifya.home.tasks.empty_title') }}</h3>
                    <p class="mb-6 max-w-md text-sm text-slate-500 dark:text-slate-400">{{ __('sortifya.home.tasks.empty_body') }}</p>
                    @guest
                        <a href="{{ route('register') }}" class="btn-primary !py-2.5 text-[13px]">
                            {{ __('sortifya.nav.start') }}
                            <x-lucide name="arrow-right" :size="15" class="flip-rtl" />
                        </a>
                    @endguest
                </div>
            @else
                <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($tasks as $index => $task)
                        <x-task-card :task="$task" mode="public" :delay="($index % 3) * 90" />
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    {{-- ══════════════════════════════════════════════════════════════════
         PAYOUT TERMS — the three facts people actually want confirmed.
         ══════════════════════════════════════════════════════════════ --}}
    <section class="pb-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="panel relative overflow-hidden p-8 sm:p-12" data-aos="fade-up">
                <div class="ambient -end-20 -top-20 h-72 w-72" aria-hidden="true"></div>

                <div class="grid gap-10 lg:grid-cols-12 lg:items-center">
                    <div class="lg:col-span-6">
                        <span class="cell-ref">{{ __('sortifya.home.payout.eyebrow') }}</span>
                        <h2 class="mt-4 text-2xl font-bold sm:text-3xl">{{ __('sortifya.home.payout.title') }}</h2>
                        <p class="mt-4 max-w-lg text-[15px] leading-relaxed text-slate-600 dark:text-slate-400">
                            {{ __('sortifya.home.payout.body') }}
                        </p>

                        <a href="{{ auth()->check() ? route('wallet') : route('register') }}"
                           class="btn-primary mt-7 !px-6 !py-3">
                            {{ auth()->check() ? __('sortifya.dashboard.view_wallet') : __('sortifya.nav.start') }}
                            <x-lucide name="arrow-right" :size="17" class="flip-rtl" />
                        </a>
                    </div>

                    <div class="lg:col-span-6">
                        <dl class="divide-y divide-slate-200 border-y border-slate-200 dark:divide-slate-800 dark:border-slate-800">
                            @foreach ([
                                ['icon' => 'coins',     'label' => __('sortifya.home.payout.min'),      'value' => '$'.number_format(config('sortifya.minimum_withdrawal'), 2), 'mono' => true],
                                ['icon' => 'smartphone','label' => __('sortifya.home.payout.methods'),  'value' => __('sortifya.home.payout.methods_value'), 'mono' => false],
                                ['icon' => 'banknote',  'label' => __('sortifya.home.payout.currency'), 'value' => __('sortifya.home.payout.currency_value'), 'mono' => false],
                            ] as $fact)
                                <div class="flex items-center justify-between gap-4 py-4">
                                    <dt class="flex items-center gap-2.5 text-sm text-slate-500 dark:text-slate-400">
                                        <x-lucide :name="$fact['icon']" :size="16" class="text-emerald-500" />
                                        {{ $fact['label'] }}
                                    </dt>
                                    <dd class="{{ $fact['mono'] ? 'numeric font-mono text-lg' : 'text-sm' }} font-semibold text-slate-900 dark:text-white">
                                        {{ $fact['value'] }}
                                    </dd>
                                </div>
                            @endforeach
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </section>

</x-layouts.app>
