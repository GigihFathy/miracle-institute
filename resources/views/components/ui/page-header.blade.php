@props(['title', 'subtitle' => null])

<div class="flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
    <div>
        <h1 class="text-2xl font-bold text-brand-dark">{{ $title }}</h1>
        @if($subtitle)
            <p class="mt-1 text-sm text-brand-dark/70">{{ $subtitle }}</p>
        @endif
    </div>

    @if(trim($slot) !== '')
        <div class="flex flex-wrap gap-2">
            {{ $slot }}
        </div>
    @endif
</div>