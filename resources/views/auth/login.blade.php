<x-layouts.auth
    :title="__('sortifya.auth.login_title')"
    :heading="__('sortifya.auth.login_title')"
    :body="__('sortifya.auth.login_body')"
>
    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <x-field
            name="email"
            type="email"
            icon="user"
            :label="__('sortifya.auth.email')"
            placeholder="you@example.com"
            autocomplete="username"
            required
        />

        <div>
            <div class="mb-1.5 flex items-baseline justify-between gap-3">
                <label for="password" class="label !mb-0">{{ __('sortifya.auth.password') }}</label>
                <a href="{{ route('password.request') }}"
                   class="text-xs font-medium text-emerald-600 transition-colors hover:text-emerald-500 dark:text-emerald-400">
                    {{ __('sortifya.auth.forgot_link') }}
                </a>
            </div>

            <div class="relative">
                <span class="pointer-events-none absolute inset-y-0 start-0 flex items-center ps-3.5 text-slate-400">
                    <x-lucide name="lock" :size="16" />
                </span>
                <input id="password" name="password" type="password" required autocomplete="current-password"
                       placeholder="••••••••"
                       class="field ps-10 {{ $errors->has('password') ? 'border-rose-400 focus:border-rose-500 focus:ring-rose-500/30 dark:border-rose-800' : '' }}">
            </div>

            @error('password')
                <p class="mt-1.5 flex items-start gap-1.5 text-xs text-rose-600 dark:text-rose-400">
                    <x-lucide name="circle-alert" :size="13" class="mt-px" />
                    {{ $message }}
                </p>
            @enderror
        </div>

        <label class="flex cursor-pointer items-center gap-2.5 text-sm text-slate-600 dark:text-slate-400">
            <input type="checkbox" name="remember" value="1"
                   class="h-4 w-4 rounded border-slate-300 text-emerald-500 focus:ring-emerald-500/40 dark:border-slate-700 dark:bg-ink-850">
            {{ __('sortifya.auth.remember') }}
        </label>

        <button type="submit" class="btn-primary w-full !py-3">
            {{ __('sortifya.auth.login_submit') }}
            <x-lucide name="arrow-right" :size="16" class="flip-rtl" />
        </button>
    </form>

    <p class="mt-7 text-center text-sm text-slate-500 dark:text-slate-400">
        {{ __('sortifya.auth.no_account') }}
        <a href="{{ route('register') }}" class="font-semibold text-emerald-600 transition-colors hover:text-emerald-500 dark:text-emerald-400">
            {{ __('sortifya.nav.start') }}
        </a>
    </p>
</x-layouts.auth>
