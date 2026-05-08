@props([
    'variant' => 'primary',
    'size' => 'md',
    'type' => 'button',
    'disabled' => false,
    'href' => null,
])

@php
    $baseClasses = 'inline-flex items-center justify-center font-medium rounded-xl transition-all duration-200';
    
    $variantClasses = match($variant) {
        'primary' => '[background-color:var(--color-primary)] text-white hover:[background-color:var(--color-primary-dark)] disabled:bg-slate-300 disabled:text-slate-500',
        'secondary' => 'bg-white border-2 [border-color:var(--color-primary)] [color:var(--color-primary)] hover:bg-blue-50 disabled:border-slate-300 disabled:text-slate-400',
        'outline' => 'border-2 [border-color:var(--color-primary)] [color:var(--color-primary)] hover:bg-blue-50 disabled:border-slate-300 disabled:text-slate-400',
        'success' => '[background-color:var(--color-success)] text-white hover:bg-green-600 disabled:bg-slate-300 disabled:text-slate-500',
        'error' => '[background-color:var(--color-error)] text-white hover:bg-red-600 disabled:bg-slate-300 disabled:text-slate-500',
        'warning' => '[background-color:var(--color-warning)] text-white hover:bg-amber-600 disabled:bg-slate-300 disabled:text-slate-500',
        'info' => '[background-color:var(--color-info)] text-white hover:bg-blue-600 disabled:bg-slate-300 disabled:text-slate-500',
        'ghost' => '[color:var(--color-primary)] hover:bg-blue-50 disabled:text-slate-400',
        default => 'bg-slate-900 text-white hover:bg-slate-800 disabled:bg-slate-300 disabled:text-slate-500',
    };
    
    $sizeClasses = match($size) {
        'sm' => 'px-3 py-1.5 text-xs',
        'md' => 'px-4 py-2 text-sm',
        'lg' => 'px-6 py-3 text-base',
        'xl' => 'px-8 py-4 text-lg',
        default => 'px-4 py-2 text-sm',
    };
    
    $classes = "$baseClasses $variantClasses $sizeClasses";
    
    $attributes = $attributes->merge([
        'class' => $classes,
        'disabled' => $disabled,
    ]);
@endphp

@if($href)
    <a href="{{ $href }}" @class(['pointer-events-none' => $disabled]) {{ $attributes }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes }}>
        {{ $slot }}
    </button>
@endif
