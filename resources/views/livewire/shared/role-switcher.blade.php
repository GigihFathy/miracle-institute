<div x-data="{ open: false }" class="relative">
    <button @click="open = !open"
            class="px-4 py-2 rounded-xl border bg-white text-sm flex items-center gap-2 shadow-sm">
        <span class="font-medium capitalize">{{ $activeRole }}</span>
        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M19 9l-7 7-7-7"/>
        </svg>
    </button>

    <div x-show="open"
         @click.outside="open = false"
         x-transition
         class="absolute right-0 mt-3 w-56 rounded-2xl border bg-white shadow-xl p-2 z-50">

        <div class="px-3 py-2 text-xs uppercase text-slate-400">
            Switch Role
        </div>

        @foreach($roles as $role)
            <button
                wire:click="switchRole('{{ $role['name'] }}')"
                class="w-full text-left px-4 py-2 rounded-xl text-sm flex items-center justify-between
                       hover:bg-slate-100 transition
                       {{ $activeRole === $role['name'] ? 'bg-slate-900 text-white' : '' }}"
            >
                <span>{{ $role['label'] }}</span>

                @if($activeRole === $role['name'])
                    <span class="text-xs">Active</span>
                @endif
            </button>
        @endforeach
    </div>
</div>