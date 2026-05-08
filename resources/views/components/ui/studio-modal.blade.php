@props([
    'show' => 'showModal',
    'title' => 'Modal',
    'description' => null,
    'maxWidth' => 'max-w-3xl',
])

<div x-data="{ open: @entangle($show).live }">
    <template x-teleport="body">
        <div
            x-show="open"
            x-cloak
            x-transition.opacity
            @keydown.escape.window="open = false; $wire.set('{{ $show }}', false)"
            class="fixed inset-0 z-[100] flex items-center justify-center bg-black/60 p-4"
        >
            <div
                @click.outside="open = false; $wire.set('{{ $show }}', false)"
                class="w-full {{ $maxWidth }} rounded-2xl bg-white shadow-2xl overflow-hidden flex flex-col max-h-[90vh]"
            >
                <!-- Header -->
                <div class="flex items-start justify-between gap-4 border-b p-6">
                    <div>
                        <h2 class="text-lg font-semibold">{{ $title }}</h2>
                        @if($description)
                            <p class="text-sm text-slate-500 mt-1">{{ $description }}</p>
                        @endif
                    </div>

                    <button
                        type="button"
                        @click="open = false; $wire.set('{{ $show }}', false)"
                        class="text-slate-500 hover:text-slate-700"
                    >
                        ✕
                    </button>
                </div>

                <!-- Body -->
                <div class="flex-1 overflow-y-auto p-6">
                    {{ $slot }}
                </div>

                <!-- Footer -->
                @isset($footer)
                    <div class="border-t bg-slate-50 p-6">
                        {{ $footer }}
                    </div>
                @endisset
            </div>
        </div>
    </template>
</div>
