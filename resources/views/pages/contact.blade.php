@php
    $copy = __('sortifya.pages.contact');
    $supportEmail = config('sortifya.contact.support_email') ?: config('sortifya.contact.to');
    $supportPhone = config('sortifya.contact.support_phone');
@endphp

<x-layouts.app :title="$copy['title']" :description="$copy['body']">

    <div class="relative mx-auto max-w-6xl px-4 py-12 sm:px-6 lg:px-8">
        <div class="ambient -top-10 start-1/4 h-56 w-96" aria-hidden="true"></div>

        <header class="mb-10 max-w-2xl">
            <span class="cell-ref">{{ $copy['eyebrow'] }}</span>
            <h1 class="mt-4 text-3xl font-bold sm:text-4xl">{{ $copy['title'] }}</h1>
            <p class="mt-4 text-[15px] leading-relaxed text-slate-600 dark:text-slate-400">
                {{ $copy['body'] }}
            </p>
        </header>

        <div class="grid gap-6 lg:grid-cols-12">

            {{-- ── Form ── --}}
            <section class="min-w-0 lg:col-span-7 xl:col-span-8">
                <div class="panel p-6 sm:p-8">
                    <h2 class="mb-1.5 text-base font-semibold">{{ $copy['form_title'] }}</h2>

                    @auth
                        <p class="mb-6 font-mono text-[11px] text-slate-400">
                            {{ __('sortifya.pages.contact.signed_in_as', ['name' => auth()->user()->name]) }}
                        </p>
                    @else
                        <p class="mb-6 text-sm text-slate-500 dark:text-slate-400">{{ $copy['email_hint'] }}</p>
                    @endauth

                    <form method="POST" action="{{ route('contact.send') }}" class="space-y-5">
                        @csrf

                        {{-- Honeypot. Off-screen rather than display:none, which
                             some bots specifically skip, and never announced to
                             a screen reader. --}}
                        <div class="absolute -start-[9999px] top-auto h-px w-px overflow-hidden" aria-hidden="true">
                            <label for="company_website">Leave this field empty</label>
                            <input type="text" id="company_website" name="company_website" tabindex="-1" autocomplete="off">
                        </div>

                        <div class="grid gap-5 sm:grid-cols-2">
                            <x-field
                                name="name"
                                :label="$copy['name']"
                                :value="auth()->user()?->name"
                                autocomplete="name"
                                required
                            />
                            <x-field
                                name="email"
                                type="email"
                                :label="$copy['email']"
                                :value="auth()->user()?->email"
                                placeholder="you@example.com"
                                autocomplete="email"
                                required
                            />
                        </div>

                        <x-field
                            name="subject"
                            :label="$copy['subject']"
                            :placeholder="$copy['subject_placeholder']"
                            required
                        />

                        <div>
                            <label for="message" class="label">{{ $copy['message'] }}</label>
                            <textarea id="message" name="message" rows="7" required
                                      placeholder="{{ $copy['message_placeholder'] }}"
                                      class="field resize-y {{ $errors->has('message') ? 'border-rose-400 focus:border-rose-500 focus:ring-rose-500/30 dark:border-rose-800' : '' }}">{{ old('message') }}</textarea>
                            @error('message')
                                <p class="mt-1.5 flex items-start gap-1.5 text-xs text-rose-600 dark:text-rose-400">
                                    <x-lucide name="circle-alert" :size="13" class="mt-px" />
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" class="btn-primary !px-6 !py-3">
                                <x-lucide name="arrow-up-right" :size="16" class="flip-rtl" />
                                {{ $copy['submit'] }}
                            </button>
                        </div>
                    </form>
                </div>
            </section>

            {{-- ── Direct details ──
                 Skipped entirely when nothing is configured; a card headed
                 "Email" with no address under it reads as a broken page. --}}
            <aside class="min-w-0 lg:col-span-5 xl:col-span-4">
                @if ($supportEmail || $supportPhone)
                <div class="panel p-6">
                    <h2 class="mb-4 text-base font-semibold">{{ $copy['direct_title'] }}</h2>

                    <dl class="divide-y divide-slate-200 dark:divide-slate-800">
                        @if ($supportEmail)
                        <div class="flex items-center gap-3 py-3">
                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg border border-slate-200
                                         bg-white text-emerald-500 dark:border-slate-800 dark:bg-ink-850">
                                <x-lucide name="arrow-up-right" :size="16" />
                            </span>
                            <div class="min-w-0">
                                <dt class="text-[10px] font-semibold uppercase tracking-widest text-slate-400">
                                    {{ $copy['email_label'] }}
                                </dt>
                                <dd class="truncate">
                                    <a href="mailto:{{ $supportEmail }}"
                                       class="font-mono text-[13px] text-emerald-600 hover:underline dark:text-emerald-400">
                                        {{ $supportEmail }}
                                    </a>
                                </dd>
                            </div>
                        </div>
                        @endif

                        @if ($supportPhone)
                            <div class="flex items-center gap-3 py-3">
                                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg border border-slate-200
                                             bg-white text-emerald-500 dark:border-slate-800 dark:bg-ink-850">
                                    <x-lucide name="smartphone" :size="16" />
                                </span>
                                <div class="min-w-0">
                                    <dt class="text-[10px] font-semibold uppercase tracking-widest text-slate-400">
                                        {{ $copy['phone_label'] }}
                                    </dt>
                                    <dd class="numeric font-mono text-[13px] text-slate-700 dark:text-slate-300">
                                        {{ $supportPhone }}
                                    </dd>
                                </div>
                            </div>
                        @endif
                    </dl>
                </div>
                @endif

                <div class="panel {{ ($supportEmail || $supportPhone) ? 'mt-6' : '' }} p-6">
                    <div class="mb-2 flex items-center gap-2.5">
                        <x-lucide name="clock" :size="16" class="text-amber-500" />
                        <h2 class="text-base font-semibold">{{ $copy['response_title'] }}</h2>
                    </div>
                    <p class="text-sm leading-relaxed text-slate-500 dark:text-slate-400">
                        {{ __('sortifya.pages.contact.response_body', ['hours' => config('sortifya.contact.response_hours')]) }}
                    </p>
                </div>

                {{-- Deflect the easy questions; a fast self-serve answer beats
                     a good reply tomorrow. --}}
                <div class="panel mt-6 p-6">
                    <h2 class="mb-2 text-base font-semibold">{{ $copy['before_title'] }}</h2>
                    <p class="mb-4 text-sm leading-relaxed text-slate-500 dark:text-slate-400">
                        {{ $copy['before_body'] }}
                    </p>
                    <a href="{{ route('faq') }}" class="btn-ghost w-full !py-2 text-[13px]">
                        <x-lucide name="info" :size="15" />
                        {{ $copy['before_cta'] }}
                    </a>
                </div>
            </aside>
        </div>
    </div>

</x-layouts.app>
