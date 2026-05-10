@props([
    'content' => '',
    'emptyText' => 'Article preview will appear here.',
])

@php
    $fallback = '<p style="color:#94a3b8">' . e($emptyText) . '</p>';
@endphp

<article {{ $attributes->merge(['class' => 'article-prose prose prose-slate max-w-none rounded-2xl bg-white p-5 sm:p-6']) }}>
    <div class="ql-editor !p-0">
        {!!$content ?: $fallback !!}
    </div>
</article>