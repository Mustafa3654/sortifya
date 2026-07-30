@props([
    'icon',
    'label',
    'value',
    'hint' => null,
    'tone' => 'default', // default | money | wait
    'delay' => 0,
])

@php
    $tones = [
        'default' => 'text-slate-900 dark:text-white',
        'money' => 'text-emerald-600 dark:text-emerald-400',
        'wait' => 'text-amber-600 dark:text-amber-400',
    ];

    $marks = [
        'default' => 'text-slate-400',
        'money' => 'text-emerald-500',
        'wait' => 'text-amber-500',
    ];
@endphp

<div class="panel p-5" data-aos="fade-up" data-aos-delay="{{ $delay }}">
    <div class="mb-3 flex items-center gap-2">
        <x-lucide :name="$icon" :size="15" class="{{ $marks[$tone] }}" />
        <p class="text-[11px] font-semibold uppercase tracking-widest text-slate-400">{{ $label }}</p>
    </div>

    <p class="numeric font-mono text-3xl font-semibold leading-none {{ $tones[$tone] }}">{{ $value }}</p>

    @if ($hint)
        <p class="mt-2.5 text-xs text-slate-400">{{ $hint }}</p>
    @endif
</div>
