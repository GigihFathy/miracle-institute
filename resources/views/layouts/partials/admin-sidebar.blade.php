@php
    $mainMenus = [
        ['label' => 'Dashboard', 'route' => 'admin.dashboard'],
        ['label' => 'Study Programs', 'route' => 'admin.study-programs.index'],

    
        ['label' => 'Learning', 'type' => 'dropdown'],

        ['label' => 'Users', 'route' => 'admin.users.index'],
        ['label' => 'Roles', 'route' => 'admin.roles.index'],
        ['label' => 'Permissions', 'route' => 'admin.permissions.index'],
        ['label' => 'Articles', 'route' => 'admin.articles.index'],
        ['label' => 'Settings', 'route' => 'admin.settings.index'],
    ];

    $learningMenus = [
        ['label' => 'Courses', 'route' => 'admin.courses.index'],
        ['label' => 'Topics', 'route' => 'admin.topics.index'],
        ['label' => 'Materials', 'route' => 'admin.materials.index'],
        ['label' => 'Sessions', 'route' => 'admin.sessions.index'],
        ['label' => 'Attendances', 'route' => 'admin.attendances.index'],
        ['label' => 'Assessments', 'route' => 'admin.assessments.index'],
        ['label' => 'Certificates', 'route' => 'admin.certificates.index'],
    ];

    $canMap = [
        'admin.dashboard' => 'view_reports',
        'admin.study-programs.index' => 'manage_courses',

        'admin.courses.index' => 'manage_courses',
        'admin.topics.index' => 'manage_topics',
        'admin.materials.index' => 'manage_topics',
        'admin.sessions.index' => 'manage_topics',
        'admin.attendances.index' => 'view_reports',
        'admin.assessments.index' => 'manage_assessments',
        'admin.certificates.index' => 'manage_certificates',

        'admin.users.index' => 'manage_users',
        'admin.roles.index' => 'manage_users',
        'admin.permissions.index' => 'manage_users',
        'admin.articles.index' => 'view_reports',
        'admin.audit.index' => 'view_reports',
        'admin.settings.index' => 'view_reports',
    ];

    $visibleLearning = collect($learningMenus)->filter(function ($item) use ($canMap) {
        $ability = $canMap[$item['route']] ?? null;
        return !$ability || (auth()->check() && auth()->user()->can($ability));
    });

    $visibleMain = collect($mainMenus)->filter(function ($item) use ($canMap) {
        if (($item['type'] ?? null) === 'dropdown') return true;

        $ability = $canMap[$item['route']] ?? null;
        return !$ability || (auth()->check() && auth()->user()->can($ability));
    });

    $activeClass = fn ($route) => request()->routeIs($route)
        ? 'block px-4 py-2 rounded-xl bg-slate-900 text-white text-sm'
        : 'block px-4 py-2 rounded-xl text-slate-700 hover:bg-slate-100 text-sm';

    $isLearningActive =
        request()->routeIs('admin.courses.*') ||
        request()->routeIs('admin.topics.*') ||
        request()->routeIs('admin.materials.*') ||
        request()->routeIs('admin.sessions.*') ||
        request()->routeIs('admin.attendances.*') ||
        request()->routeIs('admin.assessments.*') ||
        request()->routeIs('admin.certificates.*');
@endphp


<aside 
    x-data="{ openLearning: {{ $isLearningActive ? 'true' : 'false' }} }"
    class="hidden lg:flex lg:flex-col lg:fixed lg:inset-y-0 lg:w-72 bg-white border-r border-slate-200"
>

    {{-- HEADER --}}
    <div class="h-16 flex items-center px-6 border-b border-slate-200">
        <div>
            <div class="font-semibold">Admin Panel</div>
            <div class="text-xs text-slate-500">
                {{ auth()->user()->full_name ?? 'Administrator' }}
            </div>
        </div>
    </div>

    {{-- NAV --}}
    <nav class="flex-1 overflow-y-auto p-4 space-y-1">

        @foreach($visibleMain as $item)

            {{-- NORMAL MENU --}}
            @if(!isset($item['type']))
                <a href="{{ route($item['route']) }}" wire:navigate
                   class="{{ $activeClass($item['route']) }}">
                    {{ $item['label'] }}
                </a>
            @endif


            {{-- LEARNING DROPDOWN --}}
            @if(($item['type'] ?? null) === 'dropdown' && $visibleLearning->count())

                <div class="mt-2">

                    <button 
                        @click="openLearning = !openLearning"
                        class="w-full flex items-center justify-between px-4 py-2 text-sm rounded-xl text-slate-700 hover:bg-slate-100"
                    >
                        <span>Learning</span>

                        <svg 
                            :class="openLearning ? 'rotate-180' : ''"
                            class="w-4 h-4 transition-transform"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div x-show="openLearning" x-transition class="mt-1 ml-2 space-y-1">

                        @foreach($visibleLearning as $sub)
                            <a href="{{ route($sub['route']) }}" wire:navigate
                               class="{{ $activeClass($sub['route']) }}">
                                {{ $sub['label'] }}
                            </a>
                        @endforeach

                    </div>
                </div>

            @endif

        @endforeach

    </nav>

</aside>