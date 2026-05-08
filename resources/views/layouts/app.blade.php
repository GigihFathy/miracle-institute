<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'LMS') }}</title>

    @livewireStyles
    @stack('styles')

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 text-gray-900">
    <div class="min-h-screen flex">
        @include('layouts.partials.sidebar')

        <div class="flex-1 lg:pl-72">
            @include('layouts.partials.topbar')

            <main class="p-4 sm:p-6 lg:p-8">
                @if (session('success'))
                    <div class="mb-4 rounded-lg bg-green-100 text-green-700 px-4 py-3">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-4 rounded-lg bg-red-100 text-red-700 px-4 py-3">
                        {{ session('error') }}
                    </div>
                @endif
                
                @auth
                    <div class="mb-6">
                        @livewire('shared.role-switcher')
                    </div>
                @endauth
                {{ $slot }}
            </main>
        </div>
    </div>

    @livewireScripts
    @stack('scripts')
</body>
</html>