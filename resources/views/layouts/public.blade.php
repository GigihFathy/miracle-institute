<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'LMS') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @livewireStyles
    @stack('styles')

</head>
<body class="bg-slate-50 text-[#004777]">
    @yield('content')

    @livewireScripts
    @stack('scripts')
</body>
</html>
