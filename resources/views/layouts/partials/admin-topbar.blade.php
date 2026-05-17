<header class="sticky top-0 z-30 border-b border-slate-200 bg-white/95 backdrop-blur">
    <div class="px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 items-center justify-between gap-4">
            <div>
                <h1 class="text-lg font-semibold">{{ __('admin.layout.topbar_title') }}</h1>
            </div>

            <div class="flex items-center gap-3">
                @livewire('shared.language-switcher')
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