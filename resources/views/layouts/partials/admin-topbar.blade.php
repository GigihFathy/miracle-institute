<header class="sticky top-0 z-30 border-b border-slate-200 bg-white/95 backdrop-blur">
    <div class="px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <button
                    type="button"
                    class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-600 transition hover:bg-slate-50 hover:text-slate-900 lg:hidden"
                    aria-label="Toggle menu admin"
                    data-admin-sidebar-toggle
                    onclick="window.__adminSidebarToggle && window.__adminSidebarToggle()"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                </button>

                <div>
                    <h1 class="text-lg font-semibold">{{ __('admin.layout.topbar_title') }}</h1>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <form method="POST" action="{{ localized_route('logout') }}">
                    @csrf
                    <button class="rounded-xl border border-brand-dark/20 bg-transparent px-3 py-2 text-sm text-brand-dark transition hover:bg-brand/10">
                        {{ __('admin.layout.logout') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>
