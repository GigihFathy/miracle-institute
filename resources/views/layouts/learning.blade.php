<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'LMS') }}</title>

    @livewireStyles
    @stack('styles')

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>[x-cloak]{display:none !important;}</style>
</head>
<body class="bg-slate-50 text-slate-900">
    <div class="min-h-screen">
        @include('layouts.partials.learning-topbar')

        <main class="px-4 py-5 sm:px-6 lg:px-8 lg:py-8 pb-24">
            @if (session('success'))
                <div class="mb-4 rounded-xl bg-emerald-50 text-emerald-700 px-4 py-3 text-sm border border-emerald-200">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 rounded-xl bg-rose-50 text-rose-700 px-4 py-3 text-sm border border-rose-200">
                    {{ session('error') }}
                </div>
            @endif

            {{ $slot }}
        </main>

        @include('layouts.partials.footer')
    </div>
    
    @livewireScripts
    @stack('scripts')
</body>
</html>