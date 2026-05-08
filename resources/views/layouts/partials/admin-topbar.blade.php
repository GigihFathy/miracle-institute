<header class="sticky top-0 z-30 bg-white/95 backdrop-blur border-b border-slate-200">
    <div class="px-4 sm:px-6 lg:px-8">
        <div class="h-16 flex items-center justify-between gap-4">
            <div>
                <h1 class="font-semibold text-lg">Administration</h1>
            </div>

            <div class="flex items-center gap-3">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-ui.button type="submit" variant="primary" size="md">Logout</x-ui.button>
                </form>
            </div>
        </div>
    </div>
</header>