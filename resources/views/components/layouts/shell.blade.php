@props([
    'title' => null,
    'description' => null,
])

@php
    $locale = app()->getLocale();
    $dir = config("sortifya.locales.{$locale}.dir", 'ltr');
@endphp

<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $dir }}" class="scroll-pt-24">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ? $title.' · '.__('sortifya.brand') : __('sortifya.brand').' — '.__('sortifya.tagline') }}</title>
    <meta name="description" content="{{ $description ?? __('sortifya.footer.blurb') }}">

    {{-- Theme is applied before first paint so a dark-mode reader never gets
         a white flash. Runs ahead of Alpine, which then reads the class. --}}
    <script>
        (function () {
            try {
                var stored = localStorage.getItem('sortifya.theme');
                var dark = stored ? stored === 'dark'
                    : window.matchMedia('(prefers-color-scheme: dark)').matches;
                document.documentElement.classList.toggle('dark', dark);
            } catch (e) {
                /* private mode — fall through to light */
            }
        })();
    </script>

    <link rel="icon" href="data:image/svg+xml,{{ rawurlencode('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32"><rect width="32" height="32" rx="9" fill="#10B981"/><rect x="7.5" y="7.5" width="7.5" height="7.5" rx="1.6" stroke="white" stroke-opacity="0.55" stroke-width="1.6"/><rect x="17" y="7.5" width="7.5" height="7.5" rx="1.6" stroke="white" stroke-opacity="0.55" stroke-width="1.6"/><rect x="7.5" y="17" width="7.5" height="7.5" rx="1.6" stroke="white" stroke-opacity="0.55" stroke-width="1.6"/><rect x="17" y="17" width="7.5" height="7.5" rx="1.6" fill="white"/></svg>') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700&family=Instrument+Sans:wght@400;500;600&family=JetBrains+Mono:wght@400;500;600&family=IBM+Plex+Sans+Arabic:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('head')
</head>

<body class="min-h-screen">
    {{-- The nav carries a dozen controls before the content starts. --}}
    <a href="#main"
       class="sr-only focus:not-sr-only focus:fixed focus:start-4 focus:top-4 focus:z-[100] focus:rounded-lg
              focus:bg-emerald-500 focus:px-4 focus:py-2 focus:text-sm focus:font-semibold focus:text-white">
        {{ __('sortifya.nav.home') }}
    </a>

    {{ $slot }}

    @stack('scripts')

    {{-- Reward moment: controllers flash this after a successful submission
         or payout request. --}}
    @if (session('celebrate'))
        <script>
            window.addEventListener('load', function () {
                if (typeof window.sortifyaCelebrate === 'function') window.sortifyaCelebrate();
            });
        </script>
    @endif
</body>
</html>
