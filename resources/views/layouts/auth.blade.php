<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'LMS') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-screen bg-[radial-gradient(circle_at_top,#d9eeff_0%,#f4faff_40%,#eef7ff_100%)] text-[#004777]">
    <div class="min-h-screen px-4 py-6 sm:px-6 lg:px-8">
        <div class="mx-auto grid min-h-[calc(100vh-3rem)] max-w-6xl overflow-hidden rounded-[2.5rem] border border-[#004777]/10 bg-white/90 shadow-[0_24px_80px_-32px_rgba(0,71,119,0.35)] backdrop-blur lg:grid-cols-[1.05fr_0.95fr]">
            <div class="relative hidden overflow-hidden bg-gradient-to-br from-[#004777] via-[#00365E] to-[#35A7FF] p-10 text-white lg:flex lg:flex-col lg:justify-between">
                <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(255,255,255,0.18),transparent_35%),radial-gradient(circle_at_bottom_left,rgba(255,255,255,0.12),transparent_30%)]"></div>

                <div class="relative z-10 max-w-md space-y-4">
                    <div class="inline-flex rounded-full border border-white/20 bg-white/10 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.18em] text-white/80">
                        Miracle Institute
                    </div>
                    <h1 class="text-4xl font-bold leading-tight xl:text-5xl">
                        Grow in faith with a calm, focused learning space.
                    </h1>
                </div>
            </div>

            <div class="flex items-center justify-center px-4 py-8 sm:px-8 lg:px-10">
                <div class="w-full max-w-md">
                    @yield('content')
                </div>
            </div>
        </div>
    </div>
    @livewireScripts
</body>
</html>