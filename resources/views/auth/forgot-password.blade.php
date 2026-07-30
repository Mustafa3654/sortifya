<x-layouts.auth
    :title="__('sortifya.auth.forgot_title')"
    :heading="__('sortifya.auth.forgot_title')"
    :body="__('sortifya.auth.forgot_body')"
>
    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
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

        <button type="submit" class="btn-primary w-full !py-3">
            {{ __('sortifya.auth.forgot_submit') }}
            <x-lucide name="arrow-right" :size="16" class="flip-rtl" />
        </button>
    </form>

    <p class="mt-7 text-center text-sm">
        <a href="{{ route('login') }}"
           class="inline-flex items-center gap-1.5 font-medium text-slate-500 transition-colors hover:text-emerald-600 dark:text-slate-400 dark:hover:text-emerald-400">
            <x-lucide name="arrow-left" :size="15" class="flip-rtl" />
            {{ __('sortifya.auth.back_to_login') }}
        </a>
    </p>
</x-layouts.auth>
