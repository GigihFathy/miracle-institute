@php
    $mainMenus = [
        [
            'label' => __('admin.sidebar.dashboard'),
            'route' => 'admin.dashboard',
            'active_routes' => ['admin.dashboard'],
        ],
        [
            'label' => __('admin.sidebar.study_programs'),
            'route' => 'admin.study-programs.index',
            'active_routes' => ['admin.study-programs.*'],
        ],
        [
            'label' => __('admin.sidebar.courses'),
            'route' => 'admin.courses.index',
            'active_routes' => [
                'admin.courses.*',
                'admin.topics.*',
                'admin.materials.*',
                'admin.sessions.*',
                'admin.assessments.*',
                'admin.certificates.*',
            ],
        ],
        [
            'label' => __('admin.sidebar.users'),
            'route' => 'admin.users.index',
            'active_routes' => ['admin.users.*'],
        ],
        [
            'label' => __('admin.sidebar.roles'),
            'route' => 'admin.roles.index',
            'active_routes' => ['admin.roles.*'],
        ],
        [
            'label' => __('admin.sidebar.permissions'),
            'route' => 'admin.permissions.index',
            'active_routes' => ['admin.permissions.*'],
        ],
        [
            'label' => __('admin.sidebar.articles'),
            'route' => 'admin.articles.index',
            'active_routes' => ['admin.articles.*'],
        ],
        [
            'label' => __('admin.sidebar.settings'),
            'route' => 'admin.settings.index',
            'active_routes' => ['admin.settings.*'],
        ],
    ];

    $canMap = [
        'admin.dashboard' => 'view_reports',
        'admin.study-programs.index' => 'manage_courses',

        'admin.courses.index' => 'manage_courses',
        'admin.topics.index' => 'manage_topics',
        'admin.materials.index' => 'manage_topics',
        'admin.sessions.index' => 'manage_topics',
        'admin.assessments.index' => 'manage_assessments',
        'admin.users.index' => 'manage_users',
        'admin.roles.index' => 'manage_users',
        'admin.permissions.index' => 'manage_users',
        'admin.articles.index' => 'view_reports',
        'admin.audit.index' => 'view_reports',
        'admin.settings.index' => 'view_reports',
    ];

    $visibleMain = collect($mainMenus)->filter(function ($item) use ($canMap) {
        $ability = $canMap[$item['route']] ?? null;
        return ! $ability || (auth()->check() && auth()->user()->can($ability));
    });

    $isMenuActive = function (array $item): bool {
        $routes = $item['active_routes'] ?? [$item['route']];

        foreach ($routes as $route) {
            if (request()->routeIs($route)) {
                return true;
            }
        }

        return false;
    };

    $activeClass = fn (array $item) => $isMenuActive($item)
        ? 'block rounded-xl bg-mentor-primary px-4 py-2 text-sm font-semibold text-white shadow-sm'
        : 'block rounded-xl px-4 py-2 text-sm text-slate-700 hover:bg-mentor-primary-soft-2 hover:text-mentor-primary';

@endphp

<aside
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
            <a href="{{ localized_route($item['route']) }}" class="{{ $activeClass($item) }}">
                {{ $item['label'] }}
            </a>

        @endforeach
    </nav>
</aside>
