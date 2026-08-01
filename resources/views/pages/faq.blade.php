@php
    $copy = __('sortifya.pages.faq');
    // A running number across every group, so "question 14" is unambiguous.
    $counter = 0;
@endphp

<x-layouts.app :title="$copy['title']" :description="$copy['body']">

    <div class="relative mx-auto max-w-4xl px-4 py-12 sm:px-6 lg:px-8">
        <div class="ambient -top-10 start-1/4 h-56 w-96" aria-hidden="true"></div>

        <header class="mb-10 max-w-2xl">
            <span class="cell-ref">{{ $copy['eyebrow'] }}</span>
            <h1 class="mt-4 text-3xl font-bold sm:text-4xl">{{ $copy['title'] }}</h1>
            <p class="mt-4 text-[15px] leading-relaxed text-slate-600 dark:text-slate-400">
                {{ $copy['body'] }}
            </p>
        </header>

        @foreach ($copy['groups'] as $groupIndex => $group)
            <section class="mb-8" data-aos="fade-up" data-aos-delay="{{ min($groupIndex * 60, 180) }}">
                <h2 class="mb-4 flex items-center gap-2.5 text-[11px] font-semibold uppercase tracking-widest text-slate-400">
                    <span class="h-px w-6 bg-slate-300 dark:bg-slate-700"></span>
                    {{ $group['heading'] }}
                </h2>

                <div class="panel divide-y divide-slate-200 dark:divide-slate-800">
                    @foreach ($group['items'] as $item)
                        @php($counter++)

                        {{-- <details> rather than an Alpine accordion: it opens
                             without JavaScript, it is keyboard-operable for
                             free, and the browser's find-in-page can reach the
                             answers inside it. --}}
                        <details class="group" @if ($loop->parent->first && $loop->first) open @endif>
                            <summary class="flex cursor-pointer list-none items-start gap-3 p-5 transition-colors
                                            hover:bg-slate-900/[0.02] dark:hover:bg-white/[0.02]">
                                <span class="numeric mt-0.5 shrink-0 font-mono text-[11px] text-slate-400">
                                    {{ str_pad($counter, 2, '0', STR_PAD_LEFT) }}
                                </span>

                                <h3 class="flex-1 text-[15px] font-semibold leading-snug text-slate-900 dark:text-white">
                                    {{ $item['q'] }}
                                </h3>

                                <span class="mt-0.5 shrink-0 text-slate-400 transition-transform duration-200 group-open:rotate-180">
                                    <x-lucide name="chevron-down" :size="17" />
                                </span>
                            </summary>

                            <div class="px-5 pb-5 ps-[3.25rem]">
                                <x-prose :text="$item['a']" />
                            </div>
                        </details>
                    @endforeach
                </div>
            </section>
        @endforeach

        <div class="panel mt-10 flex flex-wrap items-center justify-between gap-4 p-6">
            <div>
                <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ $copy['still_stuck'] }}</p>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                    {{ __('sortifya.pages.contact.response_body', ['hours' => config('sortifya.contact.response_hours')]) }}
                </p>
            </div>
            <a href="{{ route('contact') }}" class="btn-primary !py-2.5 text-[13px]">
                {{ $copy['contact_cta'] }}
                <x-lucide name="arrow-right" :size="15" class="flip-rtl" />
            </a>
        </div>
    </div>

</x-layouts.app>
