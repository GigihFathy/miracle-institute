<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @include('layouts.partials.seo', [
        'seoRobots' => 'noindex, nofollow',
    ])

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @livewireStyles
    @stack('styles')

</head>
<body class="bg-slate-50 text-[#004777]">

    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="w-full max-w-md">
            {{ $slot ?? '' }}
        </div>
    </div>

    @livewireScripts
    @stack('scripts')
</body>
</html>
