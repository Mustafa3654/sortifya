<x-layouts.app :title="$copy['title']" :description="$copy['intro']">

    <div class="relative mx-auto max-w-6xl px-4 py-12 sm:px-6 lg:px-8">
        <div class="ambient -top-10 start-1/4 h-56 w-96" aria-hidden="true"></div>

        {{-- ── Masthead ── --}}
        <header class="mb-10 max-w-3xl">
            <div class="mb-4 flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200
                             bg-white text-emerald-500 dark:border-slate-800 dark:bg-ink-850">
                    <x-lucide :name="$icon" :size="19" />
                </span>
                <span class="cell-ref">{{ $copy['eyebrow'] }}</span>
            </div>

            <h1 class="text-3xl font-bold sm:text-4xl">{{ $copy['title'] }}</h1>

            <p class="mt-4 text-[15px] leading-relaxed text-slate-600 dark:text-slate-400">
                {{ $copy['intro'] }}
            </p>

            <p class="mt-5 font-mono text-[11px] uppercase tracking-widest text-slate-400">
                {{ __("sortifya.pages.{$page}.updated", ['date' => \Illuminate\Support\Carbon::parse(config('sortifya.legal_updated', '2026-08-01'))->translatedFormat('d F Y')]) }}
            </p>
        </header>

        <div class="grid gap-10 lg:grid-cols-12">

            {{-- ── Contents. Numbered because the sections are referenced by
                 number in correspondence, and a legal page is one of the few
                 places where "see section 6" is genuinely how people talk. ── --}}
            <aside class="lg:col-span-4 xl:col-span-3">
                <nav class="panel sticky top-24 p-5" aria-label="{{ $copy['toc'] }}">
                    <p class="mb-3 text-[10px] font-semibold uppercase tracking-widest text-slate-400">
                        {{ $copy['toc'] }}
                    </p>
                    <ol class="space-y-1">
                        @foreach ($sections as $index => $section)
                            <li>
                                <a href="#section-{{ $index + 1 }}"
                                   class="group flex gap-2.5 rounded-lg px-2 py-1.5 text-[13px] leading-snug text-slate-600
                                          transition-colors hover:bg-slate-900/5 hover:text-slate-900
                                          dark:text-slate-400 dark:hover:bg-white/5 dark:hover:text-white">
                                    <span class="numeric shrink-0 font-mono text-[11px] text-slate-400 group-hover:text-emerald-500">
                                        {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}
                                    </span>
                                    {{ $section['heading'] }}
                                </a>
                            </li>
                        @endforeach
                    </ol>
                </nav>
            </aside>

            {{-- ── Body ── --}}
            <div class="min-w-0 lg:col-span-8 xl:col-span-9">
                <div class="panel divide-y divide-slate-200 dark:divide-slate-800">
                    @foreach ($sections as $index => $section)
                        <section id="section-{{ $index + 1 }}" class="scroll-mt-24 p-6 sm:p-8">
                            <div class="mb-3 flex items-baseline gap-3">
                                <span class="numeric font-mono text-[11px] font-medium text-emerald-500">
                                    {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}
                                </span>
                                <h2 class="text-lg font-semibold">{{ $section['heading'] }}</h2>
                            </div>

                            <x-prose :text="$section['body']" class="ps-0 sm:ps-8" />
                        </section>
                    @endforeach
                </div>

                {{-- Every legal page should end with a way to ask a human. --}}
                <div class="panel mt-6 flex flex-wrap items-center justify-between gap-4 p-6">
                    <div>
                        <p class="text-sm font-semibold text-slate-900 dark:text-white">
                            {{ __('sortifya.pages.faq.still_stuck') }}
                        </p>
                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                            {{ __('sortifya.pages.contact.body') }}
                        </p>
                    </div>
                    <a href="{{ route('contact') }}" class="btn-primary !py-2.5 text-[13px]">
                        {{ __('sortifya.pages.faq.contact_cta') }}
                        <x-lucide name="arrow-right" :size="15" class="flip-rtl" />
                    </a>
                </div>
            </div>
        </div>
    </div>

</x-layouts.app>
