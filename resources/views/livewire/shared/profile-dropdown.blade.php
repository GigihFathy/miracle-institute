<div x-data="{ open: false }" class="relative">
    @php
        
        $learningMenus = [
            ['label' => 'Courses', 'route' => 'mentor.courses.index'],
        ];

        $activeRole = session('active_role');
    @endphp

    <button @click="open = !open" 
            class="flex items-center gap-2 rounded-xl border bg-white px-2 py-1 text-sm shadow-sm transition hover:bg-slate-50 sm:px-2 sm:py-1"
            aria-haspopup="true">
        <svg class="h-6 w-6 text-slate-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
        </svg>
        
        <svg class="h-3 w-3 text-slate-400 transition-transform" :class="open ? 'rotate-180' : ''" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
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
            class="absolute right-0 mt-2 w-[calc(100vw-1.5rem)] max-w-72 rounded-2xl border bg-white p-2 shadow-xl z-50 sm:w-56 sm:max-w-none"
         style="display: none;">
        
        <div class="px-3 py-2 text-xs font-semibold uppercase tracking-wider text-slate-400">Account</div>

        <a href="{{ url('/profile') }}" class="flex items-center px-4 py-2 rounded-xl text-sm text-slate-700 hover:bg-slate-100 transition">
            <svg class="h-4 w-4 text-slate-400 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
            Profile
        </a>

        @if($activeRole === 'student')
            <a href="{{ route('learning.dashboard') }}" class="flex items-center px-4 py-2 rounded-xl text-sm text-slate-700 hover:bg-slate-100 transition">
                <svg class="h-4 w-4 text-slate-400 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
                My Learning
            </a>
        @elseif($activeRole === 'disciples')
            <a href="{{ route('mentor.dashboard') }}" class="flex items-center px-4 py-2 rounded-xl text-sm text-slate-700 hover:bg-slate-100 transition">
                <svg class="h-4 w-4 text-slate-400 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
                Mentor Dashboard
            </a>

            <a href="{{ route('mentor.study-recap.index') }}" class="flex items-center px-4 py-2 rounded-xl text-sm text-slate-700 hover:bg-slate-100 transition">
                <svg class="h-4 w-4 text-slate-400 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
                Recap Study
            </a>

            <div x-data="{ openLearning: {{ request()->routeIs('disciples.*') ? 'true' : 'false' }} }" class="space-y-1">
                <button @click="openLearning = !openLearning"
                    class="w-full flex items-center justify-between px-4 py-2 rounded-xl text-sm text-slate-700 hover:bg-slate-100">

                    <span>Learning</span>

                    <svg class="w-4 h-4 transition-transform"
                        :class="openLearning ? 'rotate-180' : ''"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <div x-show="openLearning"
                    x-transition
                    class="pl-3 space-y-1">

                    @foreach($learningMenus as $menu)
                        <a href="{{ route($menu['route']) }}"
                        class="block px-4 py-2 rounded-xl text-sm {{ request()->routeIs($menu['route']) ? 'bg-slate-900 text-white' : 'text-slate-700 hover:bg-slate-100' }}">
                            {{ $menu['label'] }}
                        </a>
                    @endforeach

                </div>
            </div>

            
        @endif

        <a href="{{ route('certificates.index') }}" class="flex items-center px-4 py-2 rounded-xl text-sm text-slate-700 hover:bg-slate-100 transition">
            <svg class="h-4 w-4 text-slate-400 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 3.75h-9A2.25 2.25 0 005.25 6v12A2.25 2.25 0 007.5 20.25h9A2.25 2.25 0 0018.75 18V6A2.25 2.25 0 0016.5 3.75zM9 9h6m-6 3h6m-6 3h4.5" />
            </svg>
            Certificates
        </a>

        <hr class="my-2 border-slate-100">

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="flex items-center w-full text-left px-4 py-2 rounded-xl text-sm text-red-600 hover:bg-red-50 transition">
                <svg class="h-4 w-4 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                Logout
            </button>
        </form>
    </div>
</div>