@php
    $mainMenus = [
        ['label' => __('admin.sidebar.dashboard'), 'route' => 'admin.dashboard'],
        ['label' => __('admin.sidebar.study_programs'), 'route' => 'admin.study-programs.index'],
        ['label' => __('admin.sidebar.courses'), 'route' => 'admin.courses.index'],

        ['label' => __('admin.sidebar.users'), 'route' => 'admin.users.index'],
        ['label' => __('admin.sidebar.roles'), 'route' => 'admin.roles.index'],
        ['label' => __('admin.sidebar.permissions'), 'route' => 'admin.permissions.index'],
        ['label' => __('admin.sidebar.articles'), 'route' => 'admin.articles.index'],
        ['label' => __('admin.sidebar.settings'), 'route' => 'admin.settings.index'],
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

    $activeClass = fn ($route) => request()->routeIs($route)
        ? 'block rounded-xl px-4 py-2 text-sm font-semibold bg-[#004777]/10 text-[#004777]'
        : 'block rounded-xl px-4 py-2 text-sm text-slate-700 hover:bg-[#004777]/10 hover:text-[#004777]';

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
            <a href="{{ localized_route($item['route']) }}" wire:navigate class="{{ $activeClass($item['route']) }}">
                {{ $item['label'] }}
            </a>

        @endforeach
    </nav>
</aside>