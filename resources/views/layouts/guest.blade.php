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

    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="w-full max-w-md">
            {{ $slot ?? '' }}
        </div>
    </div>

    @livewireScripts
    @stack('scripts')
</body>
</html>