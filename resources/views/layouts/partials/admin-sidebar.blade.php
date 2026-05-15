@php
    $mainMenus = [
        ['label' => __('admin.sidebar.dashboard'), 'route' => 'admin.dashboard'],
        ['label' => __('admin.sidebar.study_programs'), 'route' => 'admin.study-programs.index'],

        ['label' => __('admin.sidebar.learning'), 'type' => 'dropdown'],

        ['label' => __('admin.sidebar.users'), 'route' => 'admin.users.index'],
        ['label' => __('admin.sidebar.roles'), 'route' => 'admin.roles.index'],
        ['label' => __('admin.sidebar.permissions'), 'route' => 'admin.permissions.index'],
        ['label' => __('admin.sidebar.articles'), 'route' => 'admin.articles.index'],
        ['label' => __('admin.sidebar.settings'), 'route' => 'admin.settings.index'],
    ];

    $learningMenus = [
        ['label' => __('admin.sidebar.courses'), 'route' => 'admin.courses.index'],
        ['label' => __('admin.sidebar.topics'), 'route' => 'admin.topics.index'],
        ['label' => __('admin.sidebar.materials'), 'route' => 'admin.materials.index'],
        ['label' => __('admin.sidebar.sessions'), 'route' => 'admin.sessions.index'],
        ['label' => __('admin.sidebar.attendances'), 'route' => 'admin.attendances.index'],
        ['label' => __('admin.sidebar.assessments'), 'route' => 'admin.assessments.index'],
        ['label' => __('admin.sidebar.certificates'), 'route' => 'admin.certificates.index'],
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
        return ! $ability || (auth()->check() && auth()->user()->can($ability));
    });

    $visibleMain = collect($mainMenus)->filter(function ($item) use ($canMap) {
        if (($item['type'] ?? null) === 'dropdown') {
            return true;
        }

        $ability = $canMap[$item['route']] ?? null;
        return ! $ability || (auth()->check() && auth()->user()->can($ability));
    });

    $activeClass = fn ($route) => request()->routeIs($route)
        ? 'block rounded-xl bg-slate-900 px-4 py-2 text-sm text-white'
        : 'block rounded-xl px-4 py-2 text-sm text-slate-700 hover:bg-slate-100';

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
    class="hidden bg-white border-r border-slate-200 lg:fixed lg:inset-y-0 lg:flex lg:w-72 lg:flex-col"
>
    <div class="flex h-16 items-center border-b border-slate-200 px-6">
        <div>
            <div class="font-semibold">{{ __('admin.sidebar.panel_title') }}</div>
            <div class="text-xs text-slate-500">
                {{ auth()->user()->full_name ?? __('admin.sidebar.administrator') }}
            </div>
        </div>
    </div>

    <nav class="flex-1 space-y-1 overflow-y-auto p-4">
        @foreach($visibleMain as $item)

            @if(!isset($item['type']))
                <a href="{{ localized_route($item['route']) }}" wire:navigate class="{{ $activeClass($item['route']) }}">
                    {{ $item['label'] }}
                </a>
            @endif

            @if(($item['type'] ?? null) === 'dropdown' && $visibleLearning->count())
                <div class="mt-2">
                    <button
                        @click="openLearning = !openLearning"
                        class="flex w-full items-center justify-between rounded-xl px-4 py-2 text-sm text-slate-700 hover:bg-slate-100"
                    >
                        <span>{{ __('admin.sidebar.learning') }}</span>

                        <svg
                            :class="openLearning ? 'rotate-180' : ''"
                            class="h-4 w-4 transition-transform"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div x-show="openLearning" x-transition class="mt-1 ml-2 space-y-1">
                        @foreach($visibleLearning as $sub)
                            <a href="{{ localized_route($sub['route']) }}" wire:navigate class="{{ $activeClass($sub['route']) }}">
                                {{ $sub['label'] }}
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

        @endforeach
    </nav>
</aside>