@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center justify-between gap-3 rounded-[1.25rem] border border-[#004777]/10 bg-white/95 p-2 shadow-[0_18px_45px_-28px_rgba(0,71,119,0.35)]">
        @if ($paginator->onFirstPage())
            <span class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-400">
                Prev
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-[#004777] transition hover:-translate-y-0.5 hover:border-[#35A7FF] hover:bg-[#eef8ff]">
                Prev
            </a>
        @endif

        <span class="px-3 text-sm font-medium text-slate-500">
            {{ $paginator->currentPage() }}
        </span>

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex items-center justify-center rounded-xl bg-[#35A7FF] px-4 py-2 text-sm font-semibold text-white transition hover:-translate-y-0.5 hover:bg-[#1d9bf0]">
                Next
            </a>
        @else
            <span class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-400">
                Next
            </span>
        @endif
    </nav>
@endif
