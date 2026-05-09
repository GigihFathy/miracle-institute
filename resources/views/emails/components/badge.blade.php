@props([
    'text',
    'color' => '#0f172a',
    'soft' => '#e2e8f0',
])

<span style="display:inline-block;background:{{ $soft }};color:{{ $color }};border:1px solid rgba(15,23,42,.08);padding:6px 10px;border-radius:999px;font-size:12px;font-weight:700;letter-spacing:.02em;">
    {{ $text }}
</span>