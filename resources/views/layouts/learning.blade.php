<!DOCTYPE html>
<html lang="id">
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
<body class="bg-white text-[#004777]">
    <div class="flex min-h-screen flex-col">
        @include('layouts.partials.learning-topbar')

        <main class="flex-1 px-4 py-5 sm:px-6 lg:px-8 lg:py-8">
            {{ $slot }}

            <x-ui.flash-toasts />
        </main>

        @include('layouts.partials.footer')
    </div>
    
    @livewireScripts
    @stack('scripts')
</body>
</html>
