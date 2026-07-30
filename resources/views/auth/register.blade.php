<x-layouts.auth
    :title="__('sortifya.auth.register_title')"
    :heading="__('sortifya.auth.register_title')"
    :body="__('sortifya.auth.register_body')"
>
    <form method="POST" action="{{ route('register') }}" class="space-y-5">
        @csrf

        <x-field
            name="name"
            icon="user"
            :label="__('sortifya.auth.name')"
            autocomplete="name"
            required
        />

        <x-field
            name="email"
            type="email"
            :label="__('sortifya.auth.email')"
            placeholder="you@example.com"
            autocomplete="username"
            required
        />

        <x-field
            name="phone_number"
            type="tel"
            icon="smartphone"
            :label="__('sortifya.auth.phone')"
            :hint="__('sortifya.auth.phone_hint')"
            placeholder="+961 70 000 000"
            autocomplete="tel"
        />

        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <label for="password" class="label">{{ __('sortifya.auth.password') }}</label>
                <input id="password" name="password" type="password" required autocomplete="new-password"
                       placeholder="••••••••"
                       class="field {{ $errors->has('password') ? 'border-rose-400 focus:border-rose-500 focus:ring-rose-500/30 dark:border-rose-800' : '' }}">
                @error('password')
                    <p class="mt-1.5 flex items-start gap-1.5 text-xs text-rose-600 dark:text-rose-400">
                        <x-lucide name="circle-alert" :size="13" class="mt-px" />
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <div>
                <label for="password_confirmation" class="label">{{ __('sortifya.auth.password_confirm') }}</label>
                <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password"
                       placeholder="••••••••" class="field">
            </div>
        </div>

        <button type="submit" class="btn-primary w-full !py-3">
            {{ __('sortifya.auth.register_submit') }}
            <x-lucide name="arrow-right" :size="16" class="flip-rtl" />
        </button>
    </form>

    <p class="mt-7 text-center text-sm text-slate-500 dark:text-slate-400">
        {{ __('sortifya.auth.have_account') }}
        <a href="{{ route('login') }}" class="font-semibold text-emerald-600 transition-colors hover:text-emerald-500 dark:text-emerald-400">
            {{ __('sortifya.auth.login_submit') }}
        </a>
    </p>
</x-layouts.auth>
