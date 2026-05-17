<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'LMS') }} · {{ __('admin.layout.page_title_suffix') }}</title>

    @livewireStyles
    @stack('styles')

    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 text-[#004777]">
    <div
        x-data="{
            show: false,
            type: 'success',
            message: '',
            timer: null,
            variants: {
                success: {
                    panel: 'border-emerald-300 bg-emerald-50/95 text-emerald-950 shadow-emerald-950/15',
                    bar: 'bg-emerald-500',
                    iconBg: 'bg-emerald-500',
                    icon: 'M9.55 16.5 5.7 12.65l-1.2 1.2 5.05 5.05L19.8 8.65l-1.2-1.2-9.05 9.05Z'
                },
                error: {
                    panel: 'border-rose-300 bg-rose-50/95 text-rose-950 shadow-rose-950/15',
                    bar: 'bg-rose-500',
                    iconBg: 'bg-rose-500',
                    icon: 'M12 2.75A9.25 9.25 0 1 0 21.25 12 9.26 9.26 0 0 0 12 2.75Zm0 4.5a.9.9 0 0 1 .9.9v4.1a.9.9 0 1 1-1.8 0v-4.1a.9.9 0 0 1 .9-.9Zm0 11a1.15 1.15 0 1 1 0-2.3 1.15 1.15 0 0 1 0 2.3Z'
                },
                warning: {
                    panel: 'border-amber-300 bg-amber-50/95 text-amber-950 shadow-amber-950/15',
                    bar: 'bg-amber-500',
                    iconBg: 'bg-amber-500',
                    icon: 'M1.75 20.25h20.5L12 2.75 1.75 20.25Zm10.25-4a.9.9 0 1 1 0 1.8.9.9 0 0 1 0-1.8Zm-.9-6.75h1.8v5h-1.8v-5Z'
                },
                info: {
                    panel: 'border-sky-300 bg-sky-50/95 text-sky-950 shadow-sky-950/15',
                    bar: 'bg-sky-500',
                    iconBg: 'bg-sky-500',
                    icon: 'M12 2.75A9.25 9.25 0 1 0 21.25 12 9.26 9.26 0 0 0 12 2.75Zm0 4.05a1.15 1.15 0 1 1 0 2.3 1.15 1.15 0 0 1 0-2.3Zm1.2 10.95h-2.4v-6h2.4v6Z'
                }
            },
            openToast(payload) {
                this.type = payload?.type || 'success'
                this.message = payload?.message || ''
                this.show = true

                window.clearTimeout(this.timer)
                this.timer = window.setTimeout(() => {
                    this.show = false
                }, 2200)
            }
        }"
        x-on:toast.window="openToast($event.detail)"
        class="pointer-events-none fixed right-4 top-4 z-50 w-[calc(100%-2rem)] max-w-sm md:right-5 md:top-5 md:w-full"
    >
        <div
            x-show="show"
            x-cloak
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-y-3 scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
            x-transition:leave-end="opacity-0 translate-y-3 scale-95"
            class="pointer-events-none"
        >
            <div
                class="pointer-events-auto relative overflow-hidden rounded-2xl border px-4 py-3 text-sm shadow-[0_18px_50px_rgba(15,23,42,0.18)] backdrop-blur-xl"
                :class="variants[type]?.panel || variants.success.panel"
                role="alert"
                aria-live="assertive"
            >
                <div class="absolute inset-y-0 left-0 w-1.5 rounded-r-full" :class="variants[type]?.bar || variants.success.bar"></div>

                <div class="flex items-start gap-3 pl-2">
                    <div class="mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-full text-white shadow-lg" :class="variants[type]?.iconBg || variants.success.iconBg">
                        <svg viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5">
                            <path :d="variants[type]?.icon || variants.success.icon"></path>
                        </svg>
                    </div>

                    <div class="min-w-0 flex-1">
                        <div class="mb-0.5 text-[11px] font-semibold uppercase tracking-[0.16em] opacity-75" x-text="type"></div>
                        <div class="font-medium leading-5" x-text="message"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="min-h-screen lg:flex">
        @include('layouts.partials.admin-sidebar')

        <div class="flex-1 lg:pl-72">
            @include('layouts.partials.admin-topbar')

            <main class="px-4 py-5 sm:px-6 lg:px-8 lg:py-8">
                {{ $slot }}
            </main>
        </div>
    </div>

    <style>
        [x-cloak] { display: none !important; }
    </style>

    @livewireScripts
    @stack('scripts')
</body>
</html>