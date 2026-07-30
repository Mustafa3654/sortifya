@props([
    'name',
    'size' => 20,
    'stroke' => 2,
])

{{--
    Every icon in the application renders through here, so size, stroke weight
    and colour stay consistent and a typo shows up as nothing rather than as a
    broken glyph. Geometry comes from App\Support\LucideIcons.
--}}

@php($path = \App\Support\LucideIcons::path($name))

@if ($path)
    <svg
        xmlns="http://www.w3.org/2000/svg"
        width="{{ $size }}"
        height="{{ $size }}"
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        stroke-width="{{ $stroke }}"
        stroke-linecap="round"
        stroke-linejoin="round"
        aria-hidden="true"
        focusable="false"
        {{ $attributes->merge(['class' => 'shrink-0']) }}
    >{!! $path !!}</svg>
@endif
