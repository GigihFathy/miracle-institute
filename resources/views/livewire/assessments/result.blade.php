<div class="max-w-4xl mx-auto p-4 sm:p-6 space-y-6">
    <section class="rounded-3xl bg-white border p-8 text-center shadow-sm">
        <div class="text-xs uppercase tracking-wide text-slate-400">
            Assessment Result
        </div>

        <h1 class="text-3xl font-bold mt-3">
            {{ $assessment?->title ?? 'Assessment' }}
        </h1>

        <p class="mt-3 text-sm text-slate-500">
            Course: {{ $assessment?->course?->title ?? '-' }} · Attempt #{{ $attempt->attempt_no }}
        </p>

        <div class="mt-8 text-6xl font-bold">
            {{ $attempt->score ?? 0 }}
        </div>

        <div class="mt-3 text-lg font-semibold {{ $attempt->passed ? 'text-emerald-700' : 'text-rose-700' }}">
            {{ $attempt->passed ? 'PASSED' : 'FAILED' }}
        </div>

        <div class="mt-6 grid grid-cols-2 sm:grid-cols-4 gap-3 text-sm text-left">
            <div class="rounded-2xl border bg-slate-50 p-4">
                <div class="text-xs text-slate-500">Correct</div>
                <div class="mt-1 font-semibold">{{ $correctAnswers }}</div>
            </div>

            <div class="rounded-2xl border bg-slate-50 p-4">
                <div class="text-xs text-slate-500">Wrong</div>
                <div class="mt-1 font-semibold">{{ $wrongAnswers }}</div>
            </div>

            <div class="rounded-2xl border bg-slate-50 p-4">
                <div class="text-xs text-slate-500">Unanswered</div>
                <div class="mt-1 font-semibold">{{ $unansweredQuestions }}</div>
            </div>

            <div class="rounded-2xl border bg-slate-50 p-4">
                <div class="text-xs text-slate-500">Passing Grade</div>
                <div class="mt-1 font-semibold">{{ $assessment?->passing_grade ?? '-' }}%</div>
            </div>
        </div>

        <div class="mt-6 rounded-2xl border p-4 text-left {{ $attempt->passed ? 'bg-emerald-50 border-emerald-200' : 'bg-rose-50 border-rose-200' }}">
            <div class="font-semibold {{ $attempt->passed ? 'text-emerald-800' : 'text-rose-800' }}">
                {{ $attempt->passed ? 'Kelulusan assessment tercapai.' : 'Assessment belum lulus.' }}
            </div>
            <div class="mt-1 text-sm text-slate-700 leading-6">
                {{ $attempt->passed
                    ? 'Jika seluruh topik course sudah selesai, certificate akan disinkronkan otomatis oleh backend.'
                    : 'Anda dapat mengulang assessment sampai lulus. Remedial attempt tersedia tanpa batas selama course masih aktif.' }}
            </div>
        </div>

        @if($certificate)
            <div class="mt-6 rounded-2xl border bg-slate-50 p-4 text-left">
                <div class="text-xs uppercase tracking-wide text-slate-400">Certificate</div>
                <div class="mt-1 font-semibold">
                    {{ $certificate->certificate_number }}
                </div>
                <div class="text-sm text-slate-600 mt-1">
                    Status: {{ strtoupper($certificate->status) }}
                </div>
            </div>
        @endif

        <div class="mt-8 flex flex-wrap justify-center gap-3">
            <a href="{{ route('learning.dashboard') }}"
               class="px-5 py-3 rounded-xl bg-slate-900 text-white text-sm">
                Back to Dashboard
            </a>

            @unless($attempt->passed)
                <a href="{{ route('assessments.take', $assessment->id) }}"
                   class="px-5 py-3 rounded-xl border text-sm">
                    Retry Assessment
                </a>
            @endunless

            <a href="{{ route('certificates.index') }}"
               class="px-5 py-3 rounded-xl border text-sm">
                View Certificates
            </a>
        </div>
    </section>
</div>