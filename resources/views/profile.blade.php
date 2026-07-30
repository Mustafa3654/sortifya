<x-layouts.app :title="__('sortifya.profile.title')">

    <div class="relative mx-auto max-w-3xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="ambient -top-10 start-1/4 h-56 w-80" aria-hidden="true"></div>

        <x-page-header
            :eyebrow="__('sortifya.nav.profile')"
            :heading="__('sortifya.profile.title')"
            :body="__('sortifya.profile.subtitle')"
        />

        <section class="panel p-6 sm:p-8">
            <h2 class="mb-1.5 text-base font-semibold">{{ __('sortifya.profile.details') }}</h2>
            <p class="mb-6 text-sm text-slate-500 dark:text-slate-400">{{ __('sortifya.profile.details_body') }}</p>

            <form method="POST" action="{{ route('profile.update') }}" class="space-y-5">
                @csrf
                @method('PATCH')

                <x-field name="name" :label="__('sortifya.auth.name')" :value="$user->name" autocomplete="name" required />
                <x-field name="email" type="email" :label="__('sortifya.auth.email')" :value="$user->email" autocomplete="email" required />
                <x-field name="phone_number" type="tel" icon="smartphone" :label="__('sortifya.auth.phone')"
                         :value="$user->phone_number" :hint="__('sortifya.auth.phone_hint')" autocomplete="tel" />

                <div class="flex justify-end">
                    <button type="submit" class="btn-primary !py-2.5 text-[13px]">
                        <x-lucide name="check" :size="15" />
                        {{ __('sortifya.common.save') }}
                    </button>
                </div>
            </form>
        </section>

        <section class="panel mt-6 p-6 sm:p-8">
            <h2 class="mb-1.5 text-base font-semibold">{{ __('sortifya.profile.password_title') }}</h2>
            <p class="mb-6 text-sm text-slate-500 dark:text-slate-400">{{ __('sortifya.profile.password_body') }}</p>

            <form method="POST" action="{{ route('profile.password') }}" class="space-y-5">
                @csrf
                @method('PUT')

                <div>
                    <label for="current_password" class="label">{{ __('sortifya.profile.current_password') }}</label>
                    <input id="current_password" name="current_password" type="password" required autocomplete="current-password"
                           class="field {{ $errors->has('current_password') ? 'border-rose-400 dark:border-rose-800' : '' }}">
                    @error('current_password')
                        <p class="mt-1.5 flex items-start gap-1.5 text-xs text-rose-600 dark:text-rose-400">
                            <x-lucide name="circle-alert" :size="13" class="mt-px" />
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <label for="password" class="label">{{ __('sortifya.profile.new_password') }}</label>
                        <input id="password" name="password" type="password" required autocomplete="new-password"
                               class="field {{ $errors->has('password') ? 'border-rose-400 dark:border-rose-800' : '' }}">
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
                               autocomplete="new-password" class="field">
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="btn-primary !py-2.5 text-[13px]">
                        <x-lucide name="lock" :size="15" />
                        {{ __('sortifya.common.save') }}
                    </button>
                </div>
            </form>
        </section>
    </div>

</x-layouts.app>
