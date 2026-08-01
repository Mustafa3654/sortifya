@props(['text'])

{{--
    Renders a copy block from the language files: blank lines become
    paragraphs and **this** becomes bold. Everything is escaped first, so a
    translator can never inject markup by accident.

    Deliberately this small — the alternative is a Markdown dependency for two
    features, or HTML inside the language files, which makes them unreadable
    and impossible to check for parity.
--}}

@php
    $paragraphs = preg_split('/\n\s*\n/', trim($text));
@endphp

<div {{ $attributes->merge(['class' => 'space-y-4']) }}>
    @foreach ($paragraphs as $paragraph)
        <p class="text-[15px] leading-relaxed text-slate-600 dark:text-slate-400">
            {!! preg_replace(
                '/\*\*(.+?)\*\*/s',
                '<strong class="font-semibold text-slate-900 dark:text-white">$1</strong>',
                nl2br(e(trim($paragraph))),
            ) !!}
        </p>
    @endforeach
</div>
