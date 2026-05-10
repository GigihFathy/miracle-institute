<div
    x-data="{ open: false }"
    x-init="
        if ($el.dataset.hasMessage === '1') {
            open = true;
            setTimeout(() => open = false, 3000);
        }
    "
    data-has-message="{{ session()->has('success') || session()->has('error') ? '1' : '0' }}"
    class="fixed right-4 top-4 z-50 w-full max-w-sm"
>
    @if(session('success'))
        <div
            x-show="open"
            x-transition
            class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 shadow-sm"
        >
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div
            x-show="open"
            x-transition
            class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700 shadow-sm"
        >
            {{ session('error') }}
        </div>
    @endif
</div>