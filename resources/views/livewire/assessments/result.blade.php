<div class="max-w-2xl mx-auto p-6">
    <div class="rounded-3xl bg-white border p-8 text-center shadow-sm">
        <div class="text-xs uppercase tracking-wide text-slate-400">
            Assessment Result
        </div>

        <h1 class="text-3xl font-bold mt-3">
            {{ $assessment?->title ?? 'Assessment' }}
        </h1>

        <div class="mt-8 text-6xl font-bold">
            {{ $attempt->score }}
        </div>

        <div class="mt-3 text-lg font-semibold {{ $attempt->passed ? 'text-emerald-700' : 'text-rose-700' }}">
            {{ $attempt->passed ? 'PASSED' : 'FAILED' }}
        </div>

        @if($assessment)
            <div class="space-y-6 mt-8">
                @livewire('assessments.assessment-analytics', ['assessmentId' => $assessment->id])
                @livewire('assessments.attempt-history', ['assessmentId' => $assessment->id])
            </div>
        @endif

        <div class="mt-8 flex flex-wrap justify-center gap-3">
            <a href="{{ route('learning.dashboard') }}"
               class="px-5 py-3 rounded-xl bg-primary text-white text-sm">
                Back to Dashboard
            </a>

            <a href="{{ route('certificates.index') }}"
               class="px-5 py-3 rounded-xl border text-sm">
                View Certificates
            </a>
        </div>
    </div>
</div>