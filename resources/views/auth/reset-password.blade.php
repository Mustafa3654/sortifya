<x-layouts.auth
    :title="__('sortifya.auth.reset_title')"
    :heading="__('sortifya.auth.reset_title')"
    :body="__('sortifya.auth.reset_body')"
>
    <form method="POST" action="{{ route('password.store') }}" class="space-y-5">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <x-field
            name="email"
            type="email"
            icon="user"
            :label="__('sortifya.auth.email')"
            :value="$email"
            autocomplete="username"
            required
            readonly
            class="field bg-slate-50 dark:bg-ink-900"
        />

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
            <input id="password_confirmation" name="password_confirmation" type="password" required
                   autocomplete="new-password" placeholder="••••••••" class="field">
        </div>

        <button type="submit" class="btn-primary w-full !py-3">
            {{ __('sortifya.auth.reset_submit') }}
        </button>
    </form>
</x-layouts.auth>
