<x-layouts.app :title="__('sortifya.nav.dashboard')">

    <div class="relative mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="ambient -top-10 start-1/3 h-64 w-96" aria-hidden="true"></div>

        <x-page-header
            :heading="__('sortifya.dashboard.greeting', ['name' => auth()->user()->name])"
            :body="__('sortifya.dashboard.subtitle')"
        >
            <x-slot:actions>
                <a href="{{ route('wallet') }}" class="btn-ghost !py-2 text-[13px]">
                    <x-lucide name="wallet" :size="15" />
                    {{ __('sortifya.dashboard.view_wallet') }}
                </a>
            </x-slot:actions>
        </x-page-header>

        {{-- ── Money and workload, in the order they matter ── --}}
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <x-stat-tile
                icon="wallet"
                tone="money"
                :label="__('sortifya.dashboard.balance')"
                :value="'$'.number_format($balance, 2)"
                :hint="__('sortifya.dashboard.balance_hint')"
                :delay="0"
            />
            <x-stat-tile
                icon="clock"
                tone="wait"
                :label="__('sortifya.dashboard.pending')"
                :value="'$'.number_format($pendingEarnings, 2)"
                :hint="__('sortifya.dashboard.pending_hint')"
                :delay="70"
            />
            <x-stat-tile
                icon="circle-check-big"
                :label="__('sortifya.dashboard.approved_count')"
                :value="number_format($approvedCount)"
                :hint="__('sortifya.dashboard.approved_hint')"
                :delay="140"
            />
            <x-stat-tile
                icon="lock"
                :label="__('sortifya.dashboard.active_lock')"
                :value="number_format($activeTasks->count())"
                :hint="__('sortifya.dashboard.active_lock_hint')"
                :delay="210"
            />
        </div>

        {{-- ── Held tasks ── --}}
        <section class="mt-12">
            <div class="mb-5">
                <h2 class="text-xl font-bold">{{ __('sortifya.dashboard.active_title') }}</h2>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ __('sortifya.dashboard.active_body') }}</p>
            </div>

            @if ($activeTasks->isEmpty())
                <div class="panel flex items-center gap-3 px-5 py-8 text-sm text-slate-500 dark:text-slate-400">
                    <x-lucide name="inbox" :size="18" class="text-slate-400" />
                    {{ __('sortifya.dashboard.active_empty') }}
                </div>
            @else
                <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($activeTasks as $index => $task)
                        <x-task-card :task="$task" mode="active" :delay="($index % 3) * 80" />
                    @endforeach
                </div>
            @endif
        </section>

        {{-- ── The queue ── --}}
        <section class="mt-12">
            <div class="mb-5">
                <h2 class="text-xl font-bold">{{ __('sortifya.dashboard.open_title') }}</h2>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ __('sortifya.dashboard.open_body') }}</p>
            </div>

            @if ($openTasks->isEmpty())
                <div class="panel flex flex-col items-center px-6 py-14 text-center">
                    <span class="mb-4 flex h-14 w-14 items-center justify-center rounded-2xl border border-slate-200 bg-white text-slate-400 dark:border-slate-800 dark:bg-ink-850">
                        <x-lucide name="search-x" :size="24" />
                    </span>
                    <h3 class="mb-2 text-lg font-semibold">{{ __('sortifya.dashboard.open_empty_title') }}</h3>
                    <p class="max-w-md text-sm text-slate-500 dark:text-slate-400">{{ __('sortifya.dashboard.open_empty_body') }}</p>
                </div>
            @else
                <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($openTasks as $index => $task)
                        <x-task-card :task="$task" mode="open" :delay="($index % 3) * 80" />
                    @endforeach
                </div>
            @endif
        </section>

        {{-- ── Recent uploads ── --}}
        <section class="mt-12">
            <h2 class="mb-5 text-xl font-bold">{{ __('sortifya.dashboard.recent_title') }}</h2>

            @if ($recentSubmissions->isEmpty())
                <div class="panel flex items-center gap-3 px-5 py-8 text-sm text-slate-500 dark:text-slate-400">
                    <x-lucide name="file-spreadsheet" :size="18" class="text-slate-400" />
                    {{ __('sortifya.dashboard.recent_empty') }}
                </div>
            @else
                <div class="panel divide-y divide-slate-200 overflow-hidden dark:divide-slate-800">
                    @foreach ($recentSubmissions as $submission)
                        <div class="flex flex-wrap items-center gap-4 px-5 py-4">
                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-400 dark:border-slate-800 dark:bg-ink-850">
                                <x-lucide name="file-spreadsheet" :size="16" />
                            </span>

                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-medium text-slate-900 dark:text-white">
                                    {{ $submission->task?->title() ?? __('sortifya.common.none') }}
                                </p>
                                <p class="mt-0.5 font-mono text-[11px] text-slate-400">
                                    {{ __('sortifya.task.submitted_at', ['time' => $submission->created_at->diffForHumans()]) }}
                                </p>
                            </div>

                            @if ($submission->status === \App\Enums\SubmissionStatus::Rejected && $submission->rejection_reason)
                                <p class="order-last w-full rounded-lg bg-rose-500/5 px-3 py-2 text-xs leading-relaxed text-rose-600 dark:text-rose-400 sm:order-none sm:w-auto sm:max-w-sm sm:bg-transparent sm:px-0 sm:py-0">
                                    {{ $submission->rejection_reason }}
                                </p>
                            @endif

                            <span class="{{ $submission->status->chipClass() }}">{{ $submission->status->getLabel() }}</span>

                            <a href="{{ route('submissions.download', $submission) }}"
                               class="rounded-lg p-2 text-slate-400 transition-colors hover:bg-slate-900/5 hover:text-slate-700 dark:hover:bg-white/5 dark:hover:text-slate-200"
                               title="{{ $submission->fileName() }}">
                                <x-lucide name="download" :size="16" />
                            </a>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>
    </div>

</x-layouts.app>
