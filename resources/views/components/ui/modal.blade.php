@props([
    'title' => 'Modal',
    'open' => false,
    'maxWidth' => 'max-w-lg'
])

<div 
    x-data="{ open: @entangle($attributes->wire('model')) }"
    x-show="open"
    x-cloak
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4"
>
    <div class="bg-white w-full {{ $maxWidth }} rounded-2xl shadow-xl p-6 space-y-4">

        <!-- Header -->
        <div class="flex justify-between items-center">
            <h2 class="text-lg font-semibold">{{ $title }}</h2>

            <button @click="open = false" class="text-slate-500">
                ✕
            </button>
        </div>

        <!-- Body -->
        <div class="space-y-4">
            {{ $slot }}
        </div>

        <!-- Footer -->
        @isset($footer)
            <div class="flex justify-end gap-2 pt-3">
                {{ $footer }}
            </div>
        @endisset

    </div>
</div>