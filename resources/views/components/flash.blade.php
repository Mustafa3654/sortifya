@props(['class' => ''])

@php
    // One banner shape, three meanings. Success is emerald, "you need to do
    // something" is amber, a hard failure is rose. Nothing else uses amber.
    $notices = collect([
        ['key' => 'success', 'icon' => 'circle-check-big', 'tone' => 'border-emerald-500/30 bg-emerald-500/10 text-emerald-700 dark:text-emerald-300', 'mark' => 'text-emerald-500'],
        ['key' => 'warning', 'icon' => 'triangle-alert', 'tone' => 'border-amber-500/30 bg-amber-500/10 text-amber-700 dark:text-amber-300', 'mark' => 'text-amber-500'],
        ['key' => 'error', 'icon' => 'circle-alert', 'tone' => 'border-rose-500/30 bg-rose-500/10 text-rose-700 dark:text-rose-300', 'mark' => 'text-rose-500'],
        ['key' => 'status', 'icon' => 'info', 'tone' => 'border-slate-300 bg-slate-500/10 text-slate-700 dark:border-slate-700 dark:text-slate-300', 'mark' => 'text-slate-400'],
    ])->filter(fn ($n) => session()->has($n['key']));
@endphp

@if ($notices->isNotEmpty())
    <div class="{{ $class }} space-y-2">
        @foreach ($notices as $notice)
            <div
                x-data="{ show: true }"
                x-show="show"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 -translate-y-2"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                role="status"
                class="flex items-start gap-3 rounded-xl border px-4 py-3 text-sm {{ $notice['tone'] }}"
            >
                <x-lucide :name="$notice['icon']" :size="17" class="mt-0.5 {{ $notice['mark'] }}" />
                <p class="flex-1 leading-relaxed">{{ session($notice['key']) }}</p>
                <button type="button" x-on:click="show = false"
                        aria-label="{{ __('sortifya.common.close') }}"
                        class="opacity-50 transition-opacity hover:opacity-100">
                    <x-lucide name="x" :size="15" />
                </button>
            </div>
        @endforeach
    </div>
@endif
