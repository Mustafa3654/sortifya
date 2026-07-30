@props([
    'name',
    'label',
    'type' => 'text',
    'value' => null,
    'hint' => null,
    'placeholder' => null,
    'required' => false,
    'autocomplete' => null,
    'icon' => null,
])

{{--
    One input, one error slot, one hint. The error replaces the hint rather
    than stacking below it, so the field never grows and pushes the form.
--}}

@php($hasError = $errors->has($name))

<div>
    <label for="{{ $name }}" class="label">
        {{ $label }}
        @unless ($required)
            <span class="ms-1 font-normal normal-case tracking-normal text-slate-400">({{ __('sortifya.common.optional') }})</span>
        @endunless
    </label>

    <div class="relative">
        @if ($icon)
            <span class="pointer-events-none absolute inset-y-0 start-0 flex items-center ps-3.5 text-slate-400">
                <x-lucide :name="$icon" :size="16" />
            </span>
        @endif

        <input
            id="{{ $name }}"
            name="{{ $name }}"
            type="{{ $type }}"
            value="{{ old($name, $value) }}"
            @if ($placeholder) placeholder="{{ $placeholder }}" @endif
            @if ($required) required @endif
            @if ($autocomplete) autocomplete="{{ $autocomplete }}" @endif
            @if ($hasError) aria-invalid="true" aria-describedby="{{ $name }}-error" @endif
            {{ $attributes->merge([
                'class' => 'field '.($icon ? 'ps-10 ' : '').($hasError ? 'border-rose-400 focus:border-rose-500 focus:ring-rose-500/30 dark:border-rose-800' : ''),
            ]) }}
        >
    </div>

    @if ($hasError)
        <p id="{{ $name }}-error" class="mt-1.5 flex items-start gap-1.5 text-xs text-rose-600 dark:text-rose-400">
            <x-lucide name="circle-alert" :size="13" class="mt-px" />
            {{ $errors->first($name) }}
        </p>
    @elseif ($hint)
        <p class="mt-1.5 text-xs text-slate-400">{{ $hint }}</p>
    @endif
</div>
