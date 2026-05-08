@props([
    'variant' => 'default',
    'size' => 'sm',
])

@php
    $variantClasses = match($variant) {
        'success' => '[background-color:rgb(16_185_129_/_0.1)] [color:var(--color-success)]',
        'error' => '[background-color:rgb(239_68_68_/_0.1)] [color:var(--color-error)]',
        'warning' => '[background-color:rgb(245_158_11_/_0.1)] [color:var(--color-warning)]',
        'info' => '[background-color:rgb(59_130_246_/_0.1)] [color:var(--color-info)]',
        'primary' => '[background-color:rgb(2_123_206_/_0.1)] [color:var(--color-primary)]',
        default => 'bg-slate-100 text-slate-700',
    };
    
    $sizeClasses = match($size) {
        'xs' => 'px-2 py-0.5 text-xs',
        'sm' => 'px-2.5 py-1 text-xs',
        'md' => 'px-3 py-1.5 text-sm',
        'lg' => 'px-4 py-2 text-base',
        default => 'px-2.5 py-1 text-xs',
    };
    
    $classes = "inline-flex items-center justify-center font-medium rounded-full $variantClasses $sizeClasses";
@endphp

<span @class([$classes])>
    {{ $slot }}
</span>
