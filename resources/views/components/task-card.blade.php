@props([
    'task',
    // 'public'   — landing page; guests are prompted to sign in
    // 'open'     — dashboard queue; claimable
    // 'active'   — held by the current worker; shows the countdown
    'mode' => 'public',
    'delay' => 0,
])

@php
    $status = $task->status;
    $held = $mode === 'active';
@endphp

<article
    data-aos="fade-up"
    data-aos-delay="{{ $delay }}"
    class="panel panel-hover group relative flex flex-col overflow-hidden p-5
           {{ $held ? 'border-amber-500/40 dark:border-amber-500/30' : '' }}"
>
    {{-- Reward is the first thing anyone looks for, so it leads and it is
         set in mono at display size. --}}
    <div class="mb-4 flex items-start justify-between gap-3">
        <div>
            <p class="mb-1 text-[10px] font-semibold uppercase tracking-widest text-slate-400">
                {{ __('sortifya.task.reward') }}
            </p>
            <p class="numeric font-mono text-2xl font-semibold leading-none text-slate-900 dark:text-white">
                <span class="text-emerald-500">$</span>{{ number_format((float) $task->reward_usd, 2) }}
            </p>
        </div>

        @if ($held)
            {{-- The one live element on the card. Turns amber under 5 min. --}}
            <div x-data="countdown(@js(optional($task->expires_at)->toIso8601String()), @js(__('sortifya.task.expired')))"
                 class="text-end">
                <p class="mb-1 text-[10px] font-semibold uppercase tracking-widest text-slate-400">
                    {{ __('sortifya.task.time_left') }}
                </p>
                <p class="numeric font-mono text-lg font-semibold leading-none"
                   :class="expired ? 'text-rose-500' : (urgent ? 'text-amber-500' : 'text-slate-900 dark:text-white')"
                   x-text="label">—</p>
            </div>
        @else
            <span class="{{ $status->chipClass() }}">{{ $status->getLabel() }}</span>
        @endif
    </div>

    <h3 class="mb-1.5 text-[15px] font-semibold leading-snug text-slate-900 dark:text-white">
        {{ $task->title() }}
    </h3>

    <p class="mb-4 line-clamp-2 flex-1 text-[13px] leading-relaxed text-slate-500 dark:text-slate-400">
        {{ $task->description() }}
    </p>

    <div class="mb-4 flex items-center gap-3 text-[11px] text-slate-400">
        <span class="inline-flex items-center gap-1.5">
            <x-lucide name="file-text" :size="13" />
            PDF
        </span>
        @if ($task->sample_template_path)
            <span class="inline-flex items-center gap-1.5">
                <x-lucide name="file-spreadsheet" :size="13" />
                {{ __('sortifya.task.download_template') }}
            </span>
        @endif
        <span class="ms-auto font-mono">{{ $task->created_at->diffForHumans() }}</span>
    </div>

    @if ($held)
        <div class="flex gap-2">
            <a href="{{ route('tasks.show', $task) }}" class="btn-primary flex-1 !py-2 text-[13px]">
                <x-lucide name="upload" :size="15" />
                {{ __('sortifya.task.open_workbench') }}
            </a>
            <form method="POST" action="{{ route('tasks.release', $task) }}"
                  onsubmit="return confirm(@js(__('sortifya.task.release_confirm')))">
                @csrf
                <button type="submit" class="btn-ghost !px-3 !py-2" title="{{ __('sortifya.task.release') }}">
                    <x-lucide name="rotate-ccw" :size="15" />
                </button>
            </form>
        </div>
    @elseif ($mode === 'open')
        <form method="POST" action="{{ route('tasks.claim', $task) }}">
            @csrf
            <button type="submit" class="btn-primary w-full !py-2 text-[13px]">
                <x-lucide name="lock" :size="15" />
                {{ __('sortifya.task.claim') }}
            </button>
        </form>
    @else
        {{-- Guests are asked to sign in at the point of claiming, never before. --}}
        @auth
            <form method="POST" action="{{ route('tasks.claim', $task) }}">
                @csrf
                <button type="submit" class="btn-primary w-full !py-2 text-[13px]">
                    <x-lucide name="lock" :size="15" />
                    {{ __('sortifya.task.claim') }}
                </button>
            </form>
        @else
            <a href="{{ route('login') }}" class="btn-ghost w-full !py-2 text-[13px]"
               title="{{ __('sortifya.home.tasks.guest_hint') }}">
                <x-lucide name="lock" :size="15" />
                {{ __('sortifya.home.tasks.guest_cta') }}
            </a>
        @endauth
    @endif
</article>
