<div x-data="{ open: false }" class="relative">
    @php
        $activeRole = session('active_role');
    @endphp

    <button @click="open = !open"
            class="flex items-center gap-2 rounded-xl border bg-white px-2 py-1 text-sm shadow-sm transition hover:bg-slate-50 sm:px-2 sm:py-1"
            aria-haspopup="true">
        <svg class="h-6 w-6 text-slate-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
        </svg>
        
        <svg class="h-3 w-3 text-slate-400 transition-transform" :class="open ? 'rotate-180' : ''" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 10.94l3.71-3.71a.75.75 0 1 1 1.06 1.06l-4.24 4.24a.75.75 0 0 1-1.06 0L5.21 8.29a.75.75 0 0 1 .02-1.06z" clip-rule="evenodd" />
        </svg>
    </button>

    <div x-show="open" 
         @click.outside="open = false"
         x-transition:enter="transition ease-out duration-100"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="absolute right-0 z-50 mt-2 w-[calc(100vw-1.5rem)] max-w-72 rounded-2xl border bg-white p-2 shadow-xl sm:w-56 sm:max-w-none"
         style="display: none;">
        
        <div class="px-3 py-2 text-xs font-semibold uppercase tracking-wider text-slate-400">
            {{ __('general.shared.profile_dropdown.title') }}
        </div>

        <a href="{{ url('/profile') }}" class="flex items-center rounded-xl px-4 py-2 text-sm text-slate-700 transition hover:bg-slate-100">
            <svg class="mr-3 h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
            {{ __('general.shared.profile_dropdown.profile') }}
        </a>

        @if($activeRole === 'student')
            <a href="{{ localized_route('learning.dashboard') }}" class="flex items-center rounded-xl px-4 py-2 text-sm text-slate-700 transition hover:bg-slate-100">
                <svg class="mr-3 h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
                {{ __('general.shared.profile_dropdown.my_learning') }}
            </a>
        @elseif($activeRole === 'disciples')
            <a href="{{ localized_route('mentor.dashboard') }}" class="flex items-center rounded-xl px-4 py-2 text-sm text-slate-700 transition hover:bg-slate-100">
                <svg class="mr-3 h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
                {{ __('general.shared.profile_dropdown.mentor_dashboard') }}
            </a>
        @endif

        <hr class="my-2 border-slate-100">

        <form method="POST" action="{{ localized_route('logout') }}">
            @csrf
            <button type="submit" class="flex w-full items-center rounded-xl px-4 py-2 text-left text-sm text-red-600 transition hover:bg-red-50">
                <svg class="mr-3 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                {{ __('general.shared.profile_dropdown.logout') }}
            </button>
        </form>
    </div>
</div>