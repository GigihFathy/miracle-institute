@props([
    'title' => 'No data found',
    'description' => 'There is nothing to display yet.',
    'buttonLabel' => null,
    'buttonHref' => null,
])

<div class="rounded-2xl border bg-white p-8 text-center">
    <div class="mx-auto w-14 h-14 rounded-full bg-slate-100 flex items-center justify-center text-slate-400">
        <span class="text-xl">•</span>
    </div>

    <h3 class="mt-4 text-lg font-semibold text-slate-900">{{ $title }}</h3>
    <p class="mt-2 text-sm text-slate-500">{{ $description }}</p>

    @if($buttonLabel && $buttonHref)
        <a href="{{ $buttonHref }}"
           class="mt-5 inline-flex px-4 py-2 rounded-xl bg-primary text-white text-sm">
            {{ $buttonLabel }}
        </a>
    @endif

    @if(trim($slot) !== '')
        <div class="mt-5">
            {{ $slot }}
        </div>
    @endif
</div>