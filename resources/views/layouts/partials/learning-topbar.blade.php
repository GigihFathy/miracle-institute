@php
    $generalRoutes = [
        ['label' => __('general.navigation.dashboard'), 'route' => 'explore.dashboard'],
        ['label' => __('general.navigation.courses'), 'route' => 'courses.index'],
    ];

    $navClass = function ($routeName) {
        return request()->routeIs($routeName)
            ? 'font-semibold text-slate-900'
            : 'text-slate-600 hover:text-slate-900';
    };
@endphp

<header class="sticky top-0 z-30 border-b border-slate-200 bg-white/95 backdrop-blur">
    <div class="px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 items-center justify-between gap-4">

            <div class="min-w-0 flex flex-1 items-center gap-5">
                <a href="{{ localized_route('dashboard') }}" class="flex shrink-0 items-center gap-3">
                    <img src="{{ asset('images/logo.png') }}" alt="{{ config('app.name', 'LMS') }} logo" class="h-32 w-32 object-contain">
                    <span class="sr-only">{{ config('app.name', 'LMS') }}</span>
                </a>

                <div class="hidden items-center gap-5 text-sm md:flex">
                    @foreach($generalRoutes as $item)
                        <a href="{{ localized_route($item['route']) }}" class="{{ $navClass($item['route']) }}">
                            {{ $item['label'] }}
                        </a>
                    @endforeach
                </div>

                <div class="hidden w-full max-w-md lg:block">
                    @livewire('shared.topbar-course-search')
                </div>
            </div>

            <div class="flex items-center gap-3">
                @auth
                    @livewire('shared.profile-dropdown')
                @endauth

                @guest
                    <a href="{{ localized_route('login') }}"
                       class="hidden rounded-xl border px-3 py-2 text-sm hover:bg-slate-50 sm:block">
                        {{ __('general.navigation.login') }}
                    </a>
                @endguest
            </div>
        </div>

        <div class="pb-3 lg:hidden">
            @livewire('shared.topbar-course-search')
        </div>
    </div>
</header>
