@props([
    'show' => false,
    'title' => '',
    'subtitle' => null,
    'maxWidth' => '2xl',
])

@php
    $maxWidthClass = match ($maxWidth) {
        'sm' => 'max-w-sm',
        'md' => 'max-w-md',
        'lg' => 'max-w-lg',
        'xl' => 'max-w-xl',
        '2xl' => 'max-w-2xl',
        '3xl' => 'max-w-3xl',
        default => 'max-w-2xl',
    };
@endphp

@if($show)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/50 p-3 sm:p-4">
        <div class="w-full {{ $maxWidthClass }} max-h-[92vh] overflow-y-auto rounded-3xl bg-white shadow-2xl transform-gpu scale-[0.98] sm:scale-100">
            <div class="flex items-start justify-between gap-4 border-b px-5 py-4 sm:px-6 sm:py-5">
                <div>
                    <h3 class="text-lg font-semibold text-slate-900">{{ $title }}</h3>
                    @if($subtitle)
                        <p class="mt-1 text-sm text-slate-500">{{ $subtitle }}</p>
                    @endif
                </div>

                <button
                    type="button"
                    {{ $attributes->whereStartsWith('wire:click') }}
                    class="rounded-xl border px-3 py-2 text-sm text-slate-600 hover:bg-slate-50"
                >
                    Close
                </button>
            </div>

            <div class="px-5 py-5 sm:px-6 sm:py-6">
                {{ $slot }}
            </div>
        </div>
    </div>
@endif