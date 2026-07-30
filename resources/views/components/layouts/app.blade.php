@props([
    'title' => null,
    'description' => null,
    'flush' => false,
])

{{--
    The standard page frame: fixed nav, content, footer.
    Pass `flush` for pages whose first section paints its own background all
    the way to the top (the landing hero); everything else gets the top pad
    that clears the fixed nav.
--}}

<x-layouts.shell :title="$title" :description="$description">
    @include('layouts.navigation')

    {{-- overflow-x-clip, not -hidden: the ambient glows are wider than the
         viewport by design and must not add a scrollbar, but `clip` avoids
         creating a scroll container, so the wallet's sticky panel still sticks. --}}
    <main id="main" class="overflow-x-clip {{ $flush ? '' : 'pt-16' }}">
        @if (! $flush)
            <x-flash class="mx-auto mt-6 max-w-7xl px-4 sm:px-6 lg:px-8" />
        @endif

        {{ $slot }}
    </main>

    @include('layouts.footer')
</x-layouts.shell>
