@php
    $generalRoutes = [
        ['label' => 'Dashboard', 'route' => 'explore.dashboard'],
        ['label' => 'Courses', 'route' => 'courses.index'],
        ['label' => 'Articles', 'route' => 'articles.index'],
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

                </div>
            </div>

            <!-- RIGHT -->
            <div class="flex items-center gap-3">

                @auth
                    @livewire('shared.role-switcher')

                    @livewire('shared.profile-dropdown')
                @endauth

                @guest
                    <x-ui.button href="{{ route('login') }}" variant="outline" size="md">Login</x-ui.button>
                @endguest
            </div>
        </div>
    </div>
</header>