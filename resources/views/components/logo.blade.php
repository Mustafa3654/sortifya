@props(['size' => 32])

{{--
    The mark is the product in miniature: a 2×2 cell grid where the last cell
    has snapped into place and lit up. Same idea as the hero, at 32px.
--}}

<svg
    width="{{ $size }}"
    height="{{ $size }}"
    viewBox="0 0 32 32"
    fill="none"
    xmlns="http://www.w3.org/2000/svg"
    aria-hidden="true"
    focusable="false"
    {{ $attributes->merge(['class' => 'shrink-0']) }}
>
    <defs>
        <linearGradient id="sortifya-mark" x1="0" y1="0" x2="32" y2="32" gradientUnits="userSpaceOnUse">
            <stop stop-color="#10B981" />
            <stop offset="1" stop-color="#14B8A6" />
        </linearGradient>
    </defs>

    <rect width="32" height="32" rx="9" fill="url(#sortifya-mark)" />

    {{-- Three cells still ruled, one filled. --}}
    <rect x="7.5" y="7.5" width="7.5" height="7.5" rx="1.6" stroke="white" stroke-opacity="0.55" stroke-width="1.6" />
    <rect x="17" y="7.5" width="7.5" height="7.5" rx="1.6" stroke="white" stroke-opacity="0.55" stroke-width="1.6" />
    <rect x="7.5" y="17" width="7.5" height="7.5" rx="1.6" stroke="white" stroke-opacity="0.55" stroke-width="1.6" />
    <rect x="17" y="17" width="7.5" height="7.5" rx="1.6" fill="white" />
</svg>
