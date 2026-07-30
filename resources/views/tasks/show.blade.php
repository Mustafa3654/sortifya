<x-layouts.app :title="__('sortifya.task.workbench')">

    @php
        $held = $task->isHeldBy(auth()->user());
        $canUpload = $held && ! $task->lockHasExpired();
        $maxMb = (int) round(config('sortifya.uploads.max_upload_kb') / 1024);
    @endphp

    <div class="relative mx-auto max-w-6xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="ambient -top-10 start-1/4 h-64 w-96" aria-hidden="true"></div>

        <a href="{{ route('dashboard') }}"
           class="mb-7 inline-flex items-center gap-1.5 text-sm font-medium text-slate-500 transition-colors hover:text-emerald-600 dark:text-slate-400 dark:hover:text-emerald-400">
            <x-lucide name="arrow-left" :size="15" class="flip-rtl" />
            {{ __('sortifya.task.back') }}
        </a>

        {{-- ── Header: what it is, what it pays, how long is left ── --}}
        <div class="panel mb-6 p-6 sm:p-8">
            <div class="flex flex-wrap items-start justify-between gap-6">
                <div class="min-w-0 flex-1">
                    <div class="mb-3 flex flex-wrap items-center gap-2">
                        <span class="cell-ref">{{ __('sortifya.task.workbench') }}</span>
                        <span class="{{ $task->status->chipClass() }}">{{ $task->status->getLabel() }}</span>
                    </div>

                    <h1 class="text-2xl font-bold sm:text-3xl">{{ $task->title() }}</h1>

                    <p class="mt-3 max-w-2xl whitespace-pre-line text-[15px] leading-relaxed text-slate-600 dark:text-slate-400">
                        {{ $task->description() }}
                    </p>
                </div>

                <div class="flex shrink-0 gap-8">
                    <div>
                        <p class="mb-1.5 text-[10px] font-semibold uppercase tracking-widest text-slate-400">
                            {{ __('sortifya.task.reward') }}
                        </p>
                        <p class="numeric font-mono text-3xl font-semibold leading-none text-slate-900 dark:text-white">
                            <span class="text-emerald-500">$</span>{{ number_format((float) $task->reward_usd, 2) }}
                        </p>
                    </div>

                    @if ($held)
                        <div x-data="countdown(@js(optional($task->expires_at)->toIso8601String()), @js(__('sortifya.task.expired')))">
                            <p class="mb-1.5 text-[10px] font-semibold uppercase tracking-widest text-slate-400">
                                {{ __('sortifya.task.time_left') }}
                            </p>
                            <p class="numeric font-mono text-3xl font-semibold leading-none"
                               :class="expired ? 'text-rose-500' : (urgent ? 'text-amber-500' : 'text-slate-900 dark:text-white')"
                               x-text="label">—</p>
                        </div>
                    @endif
                </div>
            </div>

            @if ($held)
                <div class="mt-7 border-t border-slate-200 pt-5 dark:border-slate-800">
                    <form method="POST" action="{{ route('tasks.release', $task) }}"
                          onsubmit="return confirm(@js(__('sortifya.task.release_confirm')))">
                        @csrf
                        <button type="submit" class="btn-ghost !py-2 text-[13px]">
                            <x-lucide name="rotate-ccw" :size="15" />
                            {{ __('sortifya.task.release') }}
                        </button>
                    </form>
                </div>
            @endif
        </div>

        {{-- ── Review outcome, if this has been through once already ── --}}
        @if ($submission)
            <div class="panel mb-6 p-6">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <p class="mb-1.5 text-[10px] font-semibold uppercase tracking-widest text-slate-400">
                            {{ __('sortifya.task.submission_status') }}
                        </p>
                        <div class="flex items-center gap-3">
                            <span class="{{ $submission->status->chipClass() }}">{{ $submission->status->getLabel() }}</span>
                            <span class="font-mono text-[11px] text-slate-400">
                                {{ __('sortifya.task.submitted_at', ['time' => $submission->created_at->diffForHumans()]) }}
                            </span>
                        </div>
                    </div>

                    <a href="{{ route('submissions.download', $submission) }}" class="btn-ghost !py-2 text-[13px]">
                        <x-lucide name="download" :size="15" />
                        {{ $submission->fileName() }}
                    </a>
                </div>

                @if ($submission->status === \App\Enums\SubmissionStatus::Rejected && $submission->rejection_reason)
                    <div class="mt-5 rounded-xl border border-rose-500/25 bg-rose-500/5 p-4">
                        <p class="mb-1.5 text-[10px] font-semibold uppercase tracking-widest text-rose-500">
                            {{ __('sortifya.task.rejection_reason') }}
                        </p>
                        <p class="text-sm leading-relaxed text-rose-700 dark:text-rose-300">{{ $submission->rejection_reason }}</p>
                    </div>
                @endif
            </div>
        @endif

        <div class="grid gap-6 lg:grid-cols-2">

            {{-- ── Source ── --}}
            <section class="panel p-6">
                <div class="mb-4 flex items-center gap-3">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 bg-white text-rose-500 dark:border-slate-800 dark:bg-ink-850">
                        <x-lucide name="file-text" :size="19" />
                    </span>
                    <div>
                        <h2 class="text-base font-semibold">{{ __('sortifya.task.source_title') }}</h2>
                        <p class="font-mono text-[11px] text-slate-400">{{ basename($task->pdf_file_path) }}</p>
                    </div>
                </div>

                <p class="mb-5 text-sm leading-relaxed text-slate-500 dark:text-slate-400">
                    {{ __('sortifya.task.source_body') }}
                </p>

                <a href="{{ Storage::disk(config('sortifya.uploads.tasks_disk'))->url($task->pdf_file_path) }}"
                   target="_blank" rel="noopener"
                   class="btn-primary w-full !py-2.5 text-[13px]">
                    <x-lucide name="download" :size="15" />
                    {{ __('sortifya.task.download_pdf') }}
                </a>
            </section>

            {{-- ── Template ── --}}
            <section class="panel p-6">
                <div class="mb-4 flex items-center gap-3">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 bg-white text-emerald-500 dark:border-slate-800 dark:bg-ink-850">
                        <x-lucide name="file-spreadsheet" :size="19" />
                    </span>
                    <div>
                        <h2 class="text-base font-semibold">{{ __('sortifya.task.template_title') }}</h2>
                        <p class="font-mono text-[11px] text-slate-400">
                            {{ $task->sample_template_path ? basename($task->sample_template_path) : '—' }}
                        </p>
                    </div>
                </div>

                @if ($task->sample_template_path)
                    <p class="mb-5 text-sm leading-relaxed text-slate-500 dark:text-slate-400">
                        {{ __('sortifya.task.template_body') }}
                    </p>
                    <a href="{{ Storage::disk(config('sortifya.uploads.tasks_disk'))->url($task->sample_template_path) }}"
                       class="btn-ghost w-full !py-2.5 text-[13px]">
                        <x-lucide name="download" :size="15" />
                        {{ __('sortifya.task.download_template') }}
                    </a>
                @else
                    <p class="text-sm leading-relaxed text-slate-500 dark:text-slate-400">
                        {{ __('sortifya.task.no_template') }}
                    </p>
                @endif
            </section>
        </div>

        {{-- ── Upload ── --}}
        @if ($canUpload)
            <section class="panel mt-6 p-6 sm:p-8">
                <h2 class="mb-1.5 text-base font-semibold">{{ __('sortifya.task.upload_title') }}</h2>
                <p class="mb-6 text-sm text-slate-500 dark:text-slate-400">{{ __('sortifya.task.upload_body') }}</p>

                <form method="POST" action="{{ route('tasks.submit', $task) }}" enctype="multipart/form-data"
                      x-data="dropzone(['xlsx', 'xls', 'csv'], {{ $maxMb }})">
                    @csrf

                    {{-- The label wraps the whole zone, so clicking anywhere in
                         it opens the picker and the keyboard path is the input
                         itself — no click handler standing in for focus. --}}
                    <label
                        x-ref="zone"
                        data-error-type="{{ __('sortifya.task.dropzone_error_type') }}"
                        data-error-size="{{ __('sortifya.task.dropzone_error_size') }}"
                        x-on:dragover.prevent="over = true"
                        x-on:dragleave.prevent="over = false"
                        x-on:drop.prevent="handleDrop($event)"
                        :class="over
                            ? 'border-emerald-500 bg-emerald-500/10'
                            : (fileName ? 'border-emerald-500/40 bg-emerald-500/5' : 'border-slate-300 dark:border-slate-700 hover:border-emerald-500/50')"
                        class="flex cursor-pointer flex-col items-center justify-center rounded-2xl border-2 border-dashed
                               px-6 py-12 text-center transition-colors duration-200
                               focus-within:ring-2 focus-within:ring-emerald-500 focus-within:ring-offset-2
                               focus-within:ring-offset-mist-50 dark:focus-within:ring-offset-ink-950"
                    >
                        <input
                            x-ref="input"
                            type="file"
                            name="spreadsheet"
                            required
                            accept=".xlsx,.xls,.csv"
                            x-on:change="handleSelect($event)"
                            class="sr-only"
                        >

                        {{-- Empty --}}
                        <template x-if="!fileName">
                            <div class="flex flex-col items-center">
                                <span class="mb-4 flex h-14 w-14 items-center justify-center rounded-2xl border border-slate-200
                                             bg-white text-slate-400 transition-colors dark:border-slate-800 dark:bg-ink-850"
                                      :class="over ? 'border-emerald-500/50 text-emerald-500' : ''">
                                    <x-lucide name="upload" :size="24" />
                                </span>
                                <p class="text-[15px] font-semibold text-slate-900 dark:text-white"
                                   x-text="over ? @js(__('sortifya.task.dropzone_over')) : @js(__('sortifya.task.dropzone_idle'))"></p>
                                <p class="mt-1.5 text-xs text-slate-400">{{ __('sortifya.task.dropzone_hint') }}</p>
                            </div>
                        </template>

                        {{-- Chosen --}}
                        <template x-if="fileName">
                            <div class="flex w-full max-w-md items-center gap-3 text-start">
                                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-emerald-500/15 text-emerald-500">
                                    <x-lucide name="file-spreadsheet" :size="20" />
                                </span>
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-semibold text-slate-900 dark:text-white" x-text="fileName"></p>
                                    <p class="numeric font-mono text-[11px] text-slate-400" x-text="fileSize"></p>
                                </div>
                                <button type="button" x-on:click.prevent.stop="reset()"
                                        class="rounded-lg p-2 text-slate-400 transition-colors hover:bg-rose-500/10 hover:text-rose-500"
                                        :aria-label="@js(__('sortifya.task.dropzone_change'))">
                                    <x-lucide name="x" :size="16" />
                                </button>
                            </div>
                        </template>
                    </label>

                    {{-- Client-side rejection. The server validates again. --}}
                    <p x-show="error" x-cloak x-text="error"
                       class="mt-3 flex items-start gap-1.5 text-xs text-rose-600 dark:text-rose-400"></p>

                    @error('spreadsheet')
                        <p class="mt-3 flex items-start gap-1.5 text-xs text-rose-600 dark:text-rose-400">
                            <x-lucide name="circle-alert" :size="13" class="mt-px" />
                            {{ $message }}
                        </p>
                    @enderror

                    <div class="mt-6 flex flex-wrap items-center justify-between gap-4">
                        <p class="text-xs text-slate-400">{{ __('sortifya.task.submit_hint') }}</p>
                        <button type="submit" class="btn-primary !px-6 !py-2.5" :disabled="!fileName || error">
                            <x-lucide name="check" :size="16" />
                            {{ __('sortifya.task.submit') }}
                        </button>
                    </div>
                </form>
            </section>
        @elseif ($held)
            <div class="panel mt-6 flex items-center gap-3 px-6 py-8 text-sm text-amber-600 dark:text-amber-400">
                <x-lucide name="triangle-alert" :size="18" />
                {{ __('sortifya.task.expired_notice') }}
            </div>
        @endif
    </div>

</x-layouts.app>
