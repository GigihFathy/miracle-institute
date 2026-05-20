<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'LMS') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @livewireStyles
    @stack('styles')
    <style>
        [x-cloak] { display: none !important; }
    </style>

</head>
<body class="bg-slate-50 text-[#004777]">
    <div class="flex min-h-screen flex-col">
        @include('layouts.partials.learning-topbar')

        <main class="flex-1 px-4 py-5 sm:px-6 lg:px-8 lg:py-8">
            @if (session('success'))
                <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                    {{ session('error') }}
                </div>
            @endif

            {{ $slot }}

            <x-ui.flash-toasts />
        </main>

        @include('layouts.partials.footer')
    </div>
    
    @livewireScripts
    @stack('scripts')
</body>
</html>
