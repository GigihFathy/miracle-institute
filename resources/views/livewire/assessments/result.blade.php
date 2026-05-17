<div class="mx-auto max-w-4xl space-y-6 p-4 sm:p-6">
    <section class="rounded-3xl border bg-white p-8 text-center shadow-sm">
        <div class="text-xs uppercase tracking-wide text-slate-400">
            {{ __('general.assessment_result.title') }}
        </div>

        <h1 class="mt-3 text-3xl font-bold">
            {{ $assessment?->title ?? __('general.assessment_result.default_title') }}
        </h1>

        <p class="mt-3 text-sm text-slate-500">
            {{ __('general.assessment_result.course_attempt', [
                'course' => $assessment?->course?->title ?? '-',
                'no' => $attempt->attempt_no,
            ]) }}
        </p>

        <div class="mt-8 text-6xl font-bold">
            {{ $attempt->score ?? 0 }}
        </div>

        <div class="mt-3 text-lg font-semibold {{ $attempt->passed ? 'text-emerald-700' : 'text-rose-700' }}">
            {{ $attempt->passed ? __('general.assessment_result.status.passed') : __('general.assessment_result.status.failed') }}
        </div>

        <div class="mt-6 grid grid-cols-2 gap-3 text-left text-sm sm:grid-cols-4">
            <div class="rounded-2xl border bg-slate-50 p-4">
                <div class="text-xs text-slate-500">{{ __('general.assessment_result.metrics.correct') }}</div>
                <div class="mt-1 font-semibold">{{ $correctAnswers }}</div>
            </div>

            <div class="rounded-2xl border bg-slate-50 p-4">
                <div class="text-xs text-slate-500">{{ __('general.assessment_result.metrics.wrong') }}</div>
                <div class="mt-1 font-semibold">{{ $wrongAnswers }}</div>
            </div>

            <div class="rounded-2xl border bg-slate-50 p-4">
                <div class="text-xs text-slate-500">{{ __('general.assessment_result.metrics.unanswered') }}</div>
                <div class="mt-1 font-semibold">{{ $unansweredQuestions }}</div>
            </div>

            <div class="rounded-2xl border bg-slate-50 p-4">
                <div class="text-xs text-slate-500">{{ __('general.assessment_result.metrics.passing_grade') }}</div>
                <div class="mt-1 font-semibold">{{ $assessment?->passing_grade ?? '-' }}%</div>
            </div>
        </div>

        <div class="mt-6 rounded-2xl border p-4 text-left {{ $attempt->passed ? 'border-emerald-200 bg-emerald-50' : 'border-rose-200 bg-rose-50' }}">
            <div class="font-semibold {{ $attempt->passed ? 'text-emerald-800' : 'text-rose-800' }}">
                {{ $attempt->passed ? __('general.assessment_result.notice.passed') : __('general.assessment_result.notice.failed') }}
            </div>
            <div class="mt-1 text-sm leading-6 text-slate-700">
                {{ $attempt->passed
                    ? __('general.assessment_result.notice.passed_description')
                    : __('general.assessment_result.notice.failed_description') }}
            </div>
        </div>

        @if($certificate)
            <div class="mt-6 rounded-2xl border bg-slate-50 p-4 text-left">
                <div class="text-xs uppercase tracking-wide text-slate-400">{{ __('general.assessment_result.certificate.title') }}</div>
                <div class="mt-1 font-semibold">
                    {{ $certificate->certificate_number }}
                </div>
                <div class="mt-1 text-sm text-slate-600">
                    {{ __('general.assessment_result.certificate.status', ['status' => strtoupper($certificate->status)]) }}
                </div>
            </div>
        @endif

        <div class="mt-8 flex flex-wrap justify-center gap-3">
            <a href="{{ localized_route('learning.dashboard') }}"
               class="rounded-xl bg-slate-900 px-5 py-3 text-sm text-white">
                {{ __('general.assessment_result.actions.back_to_dashboard') }}
            </a>

            @unless($attempt->passed)
                <a href="{{ localized_route('assessments.take', $assessment->id) }}"
                   class="rounded-xl border px-5 py-3 text-sm">
                    {{ __('general.assessment_result.actions.retry') }}
                </a>
            @endunless

            <a href="{{ localized_route('certificates.index') }}"
               class="rounded-xl border px-5 py-3 text-sm">
                {{ __('general.assessment_result.actions.view_certificates') }}
            </a>
        </div>
    </section>
</div>