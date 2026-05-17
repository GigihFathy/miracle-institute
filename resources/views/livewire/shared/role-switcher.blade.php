<div x-data="{ open: false }" class="relative">
    <button @click="open = !open"
            class="flex items-center gap-2 rounded-xl border bg-white px-4 py-2 text-sm shadow-sm">
        <span class="font-medium capitalize">{{ $activeRole }}</span>
        <svg class="h-4 w-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M19 9l-7 7-7-7"/>
        </svg>
    </button>

    <div x-show="open"
         @click.outside="open = false"
         x-transition
         class="absolute right-0 z-50 mt-3 w-56 rounded-2xl border bg-white p-2 shadow-xl">

        <div class="px-3 py-2 text-xs uppercase text-slate-400">
            {{ __('general.shared.role_switcher.title') }}
        </div>

        @foreach($roles as $role)
            <button
                wire:click="switchRole('{{ $role['name'] }}')"
                class="flex w-full items-center justify-between rounded-xl px-4 py-2 text-left text-sm transition hover:bg-slate-100
                       {{ $activeRole === $role['name'] ? 'bg-slate-900 text-white' : '' }}"
            >
                <span>{{ $role['label'] }}</span>

                @if($activeRole === $role['name'])
                    <span class="text-xs">{{ __('general.shared.role_switcher.active') }}</span>
                @endif
            </button>
        @endforeach
    </div>
</div>