@props([
    'eyebrow' => null,
    'heading',
    'body' => null,
])

<div class="mb-8 flex flex-wrap items-end justify-between gap-4">
    <div>
        @if ($eyebrow)
            <span class="cell-ref">{{ $eyebrow }}</span>
        @endif
        <h1 class="{{ $eyebrow ? 'mt-3' : '' }} text-3xl font-bold sm:text-4xl">{{ $heading }}</h1>
        @if ($body)
            <p class="mt-2.5 text-[15px] text-slate-500 dark:text-slate-400">{{ $body }}</p>
        @endif
    </div>

    @if (isset($actions))
        <div class="flex items-center gap-2">{{ $actions }}</div>
    @endif
</div>
