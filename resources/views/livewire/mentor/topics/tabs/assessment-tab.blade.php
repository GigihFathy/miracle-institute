<section class="rounded-2xl border bg-white p-5">
    <h2 class="text-lg font-semibold">Assessment</h2>
    <p class="mt-1 text-sm text-slate-500">Ringkasan singkat assessment pada course ini.</p>

    @if($assessment)
        <div class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-4 text-sm">
            <div class="rounded-xl border bg-slate-50 p-4">
                <div class="text-xs text-slate-500">Title</div>
                <div class="mt-1 font-semibold">{{ $assessment->title }}</div>
            </div>
            <div class="rounded-xl border bg-slate-50 p-4">
                <div class="text-xs text-slate-500">Passing Grade</div>
                <div class="mt-1 font-semibold">{{ $assessment->passing_grade }}</div>
            </div>
            <div class="rounded-xl border bg-slate-50 p-4">
                <div class="text-xs text-slate-500">Time Limit</div>
                <div class="mt-1 font-semibold">{{ $assessment->time_limit_minutes ?? 'No limit' }}</div>
            </div>
            <div class="rounded-xl border bg-slate-50 p-4">
                <div class="text-xs text-slate-500">Questions</div>
                <div class="mt-1 font-semibold">{{ $assessment->question_limit ?? 'All' }}</div>
            </div>
        </div>
    @else
        <div class="mt-4 rounded-xl border border-dashed p-5 text-sm text-slate-500">
            Assessment belum tersedia.
        </div>
    @endif
</section>