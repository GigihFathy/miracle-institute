@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="text-sm font-medium text-slate-500">
            Menampilkan
            <span class="font-semibold text-[#004777]">{{ $paginator->firstItem() ?? 0 }}</span>
            -
            <span class="font-semibold text-[#004777]">{{ $paginator->lastItem() ?? 0 }}</span>
            dari
            <span class="font-semibold text-[#004777]">{{ $paginator->total() }}</span>
            data
        </div>

        <div class="inline-flex items-center gap-2 rounded-[1.25rem] border border-[#004777]/10 bg-white/95 p-2 shadow-[0_18px_45px_-28px_rgba(0,71,119,0.35)]">
            @if ($paginator->onFirstPage())
                <span class="inline-flex min-w-[2.75rem] items-center justify-center rounded-xl border border-slate-200 bg-slate-100 px-3 py-2 text-sm font-semibold text-slate-400">
                    Prev
                </span>
            @else
                <button
                    type="button"
                    wire:click="previousPage('{{ $paginator->getPageName() }}')"
                    wire:loading.attr="disabled"
                    class="inline-flex min-w-[2.75rem] items-center justify-center rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-[#004777] transition hover:-translate-y-0.5 hover:border-[#35A7FF] hover:bg-[#eef8ff] disabled:cursor-not-allowed disabled:opacity-60"
                >
                    Prev
                </button>
            @endif

            <div class="hidden items-center gap-2 sm:inline-flex">
                @foreach ($elements as $element)
                    @if (is_string($element))
                        <span class="inline-flex min-w-[2.75rem] items-center justify-center rounded-xl px-2 py-2 text-sm font-semibold text-slate-400">
                            {{ $element }}
                        </span>
                    @endif

                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span aria-current="page" class="inline-flex min-w-[2.75rem] items-center justify-center rounded-xl bg-[#004777] px-3 py-2 text-sm font-bold text-white shadow-[0_12px_30px_-18px_rgba(0,71,119,0.85)]">
                                    {{ $page }}
                                </span>
                            @else
                                <button
                                    type="button"
                                    wire:click="gotoPage({{ $page }}, '{{ $paginator->getPageName() }}')"
                                    class="inline-flex min-w-[2.75rem] items-center justify-center rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-[#004777] transition hover:-translate-y-0.5 hover:border-[#35A7FF] hover:bg-[#eef8ff]"
                                >
                                    {{ $page }}
                                </button>
                            @endif
                        @endforeach
                    @endif
                @endforeach
            </div>

            <span class="inline-flex items-center rounded-xl bg-[#eef8ff] px-3 py-2 text-sm font-semibold text-[#004777] sm:hidden">
                {{ $paginator->currentPage() }} / {{ $paginator->lastPage() }}
            </span>

            @if ($paginator->hasMorePages())
                <button
                    type="button"
                    wire:click="nextPage('{{ $paginator->getPageName() }}')"
                    wire:loading.attr="disabled"
                    class="inline-flex min-w-[2.75rem] items-center justify-center rounded-xl bg-[#35A7FF] px-3 py-2 text-sm font-semibold text-white transition hover:-translate-y-0.5 hover:bg-[#1d9bf0] disabled:cursor-not-allowed disabled:opacity-60"
                >
                    Next
                </button>
            @else
                <span class="inline-flex min-w-[2.75rem] items-center justify-center rounded-xl border border-slate-200 bg-slate-100 px-3 py-2 text-sm font-semibold text-slate-400">
                    Next
                </span>
            @endif
        </div>
    </nav>
@endif
