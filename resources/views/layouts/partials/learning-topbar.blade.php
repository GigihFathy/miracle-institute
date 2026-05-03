@php
    $role = session('active_role');

    $generalRoutes = [
        ['label' => 'Dashboard', 'route' => 'explore.dashboard'],
        ['label' => 'My Courses', 'route' => 'courses.index'],
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
            <div class="flex items-center gap-3 min-w-0">
                <a href="{{ route('dashboard') }}" class="font-semibold text-xl shrink-0">
                    {{ config('app.name', 'LMS') }}
                </a>

                <div class="hidden md:flex items-center gap-5 text-sm">
                    @foreach($generalRoutes as $item)
                        <a href="{{ route($item['route']) }}" class="{{ $navClass($item['route']) }}">
                            {{ $item['label'] }}
                        </a>
                    @endforeach
                    
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
                </div>
            </div>

            <div class="flex items-center gap-3">
                @livewire('shared.role-switcher')

                <form method="POST" action="{{ route('logout') }}" class="hidden sm:block">
                    @csrf
                    <button class="px-3 py-2 rounded-xl bg-slate-900 text-white text-sm">
                        Logout
                    </button>
                </form>

                <details class="md:hidden relative">
                    <summary class="cursor-pointer list-none px-3 py-2 rounded-xl border bg-white text-sm">
                        Menu
                    </summary>

                    <div class="absolute right-0 mt-3 w-72 rounded-2xl border bg-white shadow-lg p-2">
                        <div class="px-3 py-2 text-xs uppercase tracking-wide text-slate-400">General</div>
                        @foreach($generalRoutes as $item)
                            <a href="{{ route($item['route']) }}" class="block px-4 py-2 rounded-xl text-sm hover:bg-slate-100">
                                {{ $item['label'] }}
                            </a>
                        @endforeach

                        <div class="mt-2 px-3 py-2 text-xs uppercase tracking-wide text-slate-400">Learning</div>
                        @foreach($learningRoutes as $item)
                            <a href="{{ route($item['route']) }}" class="block px-4 py-2 rounded-xl text-sm hover:bg-slate-100">
                                {{ $item['label'] }}
                            </a>
                        @endforeach

                        @if($role === 'disciples')
                            <div class="mt-2 px-3 py-2 text-xs uppercase tracking-wide text-slate-400">Mentor</div>
                            @foreach($mentorRoutes as $item)
                                <a href="{{ route($item['route']) }}" class="block px-4 py-2 rounded-xl text-sm hover:bg-slate-100">
                                    {{ $item['label'] }}
                                </a>
                            @endforeach
                        @endif

                        <form method="POST" action="{{ route('logout') }}" class="mt-2">
                            @csrf
                            <button class="w-full text-left px-4 py-2 rounded-xl text-sm hover:bg-slate-100">
                                Logout
                            </button>
                        </form>
                    </div>
                </details>
            </div>
        </div>
    </div>
</header>