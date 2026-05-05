@php
    $role = auth()->check() ? session('active_role') : null;

    $generalRoutes = [
        ['label' => 'Dashboard', 'route' => 'explore.dashboard'],
        ['label' => 'Courses', 'route' => 'courses.index'],
        ['label' => 'Certificates', 'route' => 'certificates.index'],
        ['label' => 'Articles', 'route' => 'articles.index'],
    ];

    $learningRoutes = [
        ['label' => 'My Learning', 'route' => 'learning.dashboard'],
        ['label' => 'Assessment', 'route' => 'assessments.index'],
    ];

    $mentorRoutes = [
        ['label' => 'Mentor Dashboard', 'route' => 'mentor.dashboard'],
        ['label' => 'Mentored Topics', 'route' => 'mentor.topics.index'],
    ];

    $navClass = function ($routeName) {
        return request()->routeIs($routeName)
            ? 'text-slate-900 font-semibold'
            : 'text-slate-600 hover:text-slate-900';
    };
@endphp

<header class="sticky top-0 z-30 bg-white/95 backdrop-blur border-b border-slate-200">
    <div class="px-4 sm:px-6 lg:px-8">
        <div class="h-16 flex items-center justify-between gap-4">

            <!-- LEFT -->
            <div class="flex items-center gap-3 min-w-0">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 shrink-0">
                    <img src="{{ asset('images/logo.png') }}" alt="{{ config('app.name', 'LMS') }} logo" class="h-32 w-32 object-contain">
                    <span class="sr-only">{{ config('app.name', 'LMS') }}</span>
                </a>

                <!-- DESKTOP NAV -->
                <div class="hidden md:flex items-center gap-5 text-sm">

                    @foreach($generalRoutes as $item)
                        <a href="{{ route($item['route']) }}" class="{{ $navClass($item['route']) }}">
                            {{ $item['label'] }}
                        </a>
                    @endforeach

                    {{-- LEARNING (student only) --}}
                    @auth
                        @if($role === 'student')
                            <details class="relative">
                                <summary class="cursor-pointer list-none {{ request()->routeIs('learning.dashboard','assessments.index','assessments.take') ? 'text-slate-900 font-semibold' : 'text-slate-600 hover:text-slate-900' }}">
                                    Learning
                                </summary>

                                <div class="absolute left-0 mt-3 w-56 rounded-2xl border bg-white shadow-lg p-2">
                                    @foreach($learningRoutes as $item)
                                        <a href="{{ route($item['route']) }}"
                                           class="block px-4 py-2 rounded-xl text-sm hover:bg-slate-100 {{ $navClass($item['route']) }}">
                                            {{ $item['label'] }}
                                        </a>
                                    @endforeach
                                </div>
                            </details>
                        @endif

                        {{-- MENTOR (disciples only) --}}
                        @if($role === 'disciples')
                            <details class="relative">
                                <summary class="cursor-pointer list-none {{ request()->routeIs('mentor.dashboard','mentor.topics.index','mentor.topics.show') ? 'text-slate-900 font-semibold' : 'text-slate-600 hover:text-slate-900' }}">
                                    Mentor
                                </summary>

                                <div class="absolute left-0 mt-3 w-64 rounded-2xl border bg-white shadow-lg p-2">
                                    @foreach($mentorRoutes as $item)
                                        <a href="{{ route($item['route']) }}"
                                           class="block px-4 py-2 rounded-xl text-sm hover:bg-slate-100 {{ $navClass($item['route']) }}">
                                            {{ $item['label'] }}
                                        </a>
                                    @endforeach
                                </div>
                            </details>
                        @endif
                    @endauth

                </div>
            </div>

            <!-- RIGHT -->
            <div class="flex items-center gap-3">

                @auth
                    @livewire('shared.role-switcher')

                    @livewire('shared.profile-dropdown')
                @endauth

                @guest
                    <a href="{{ route('login') }}"
                       class="hidden sm:block px-3 py-2 rounded-xl border text-sm hover:bg-slate-50">
                        Login
                    </a>
                @endguest

                <!-- MOBILE MENU -->
                <details class="md:hidden relative">
                    <summary class="cursor-pointer list-none px-3 py-2 rounded-xl border bg-white text-sm">
                        Menu
                    </summary>

                    <div class="absolute right-0 mt-3 w-72 rounded-2xl border bg-white shadow-lg p-2">

                        <!-- GENERAL -->
                        <div class="px-3 py-2 text-xs uppercase tracking-wide text-slate-400">General</div>
                        @foreach($generalRoutes as $item)
                            <a href="{{ route($item['route']) }}"
                               class="block px-4 py-2 rounded-xl text-sm hover:bg-slate-100">
                                {{ $item['label'] }}
                            </a>
                        @endforeach

                        @auth
                            {{-- LEARNING --}}
                            @if($role === 'student')
                                <div class="mt-2 px-3 py-2 text-xs uppercase tracking-wide text-slate-400">Learning</div>
                                @foreach($learningRoutes as $item)
                                    <a href="{{ route($item['route']) }}"
                                       class="block px-4 py-2 rounded-xl text-sm hover:bg-slate-100">
                                        {{ $item['label'] }}
                                    </a>
                                @endforeach
                            @endif

                            {{-- MENTOR --}}
                            @if($role === 'disciples')
                                <div class="mt-2 px-3 py-2 text-xs uppercase tracking-wide text-slate-400">Mentor</div>
                                @foreach($mentorRoutes as $item)
                                    <a href="{{ route($item['route']) }}"
                                       class="block px-4 py-2 rounded-xl text-sm hover:bg-slate-100">
                                        {{ $item['label'] }}
                                    </a>
                                @endforeach
                            @endif
                        @endauth

                        <!-- AUTH ACTION -->
                        @auth
                            <form method="POST" action="{{ route('logout') }}" class="mt-2">
                                @csrf
                                <button class="w-full text-left px-4 py-2 rounded-xl text-sm hover:bg-slate-100">
                                    Logout
                                </button>
                            </form>
                        @endauth

                        @guest
                            <a href="{{ route('login') }}"
                               class="block px-4 py-2 rounded-xl text-sm hover:bg-slate-100 mt-2">
                                Login
                            </a>
                        @endguest

                    </div>
                </details>

            </div>
        </div>
    </div>
</header>