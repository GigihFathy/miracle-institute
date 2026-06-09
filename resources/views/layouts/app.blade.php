<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'LMS') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @livewireStyles
    @stack('styles')

</head>
<body class="bg-slate-50 text-slate-700">
    <div class="min-h-screen flex">
        @include('layouts.partials.admin-sidebar')

        <div class="flex-1 lg:pl-72">
            @include('layouts.partials.admin-topbar')

            <main class="p-4 sm:p-6 lg:p-8">
                {{ $slot }}

                <x-ui.flash-toasts />
            </main>
        </div>
    </div>

    @livewireScripts
    @stack('scripts')
</body>
</html>
