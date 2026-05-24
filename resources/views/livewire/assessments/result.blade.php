<div class="mx-auto max-w-5xl space-y-6 p-4 sm:p-6 lg:px-12">
    <section class="rounded-3xl border border-slate-200 bg-white p-8 shadow-[0_18px_40px_color-mix(in_oklab,#004777_10%,transparent)]">
        <div class="text-center">
            <div class="text-xs uppercase tracking-[0.18em] text-[color:color-mix(in_oklab,#004777_48%,white)]">
                {{ __('general.assessment_result.title') }}
            </div>

            <h1 class="mt-3 text-3xl font-bold text-mentor-primary">
                {{ $assessment?->title ?? __('general.assessment_result.default_title') }}
            </h1>

            <p class="mt-3 text-sm text-slate-500">
                {{ __('general.assessment_result.course_attempt', [
                    'course' => $assessment?->course?->title ?? '-',
                    'no' => $attempt->attempt_no,
                ]) }}
            </p>

            <div class="mt-8 text-6xl font-bold text-mentor-primary">
                {{ $attempt->score ?? 0 }}
            </div>

            <div class="mt-3 inline-flex rounded-full px-4 py-2 text-sm font-semibold {{ $attempt->passed ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                {{ $attempt->passed ? __('general.assessment_result.status.passed') : __('general.assessment_result.status.failed') }}
            </div>
        </div>

        <div class="mt-8 grid grid-cols-2 gap-3 text-left text-sm sm:grid-cols-3">
            <div class="rounded-2xl border border-slate-200 bg-mentor-primary-soft-2 p-4">
                <div class="text-xs uppercase tracking-wide text-[color:color-mix(in_oklab,#004777_48%,white)]">{{ __('general.assessment_result.metrics.correct') }}</div>
                <div class="mt-1 font-semibold text-mentor-primary">{{ $correctAnswers }}</div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-mentor-primary-soft-2 p-4">
                <div class="text-xs uppercase tracking-wide text-[color:color-mix(in_oklab,#004777_48%,white)]">{{ __('general.assessment_result.metrics.wrong') }}</div>
                <div class="mt-1 font-semibold text-mentor-primary">{{ $wrongAnswers }}</div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-mentor-primary-soft-2 p-4">
                <div class="text-xs uppercase tracking-wide text-[color:color-mix(in_oklab,#004777_48%,white)]">{{ __('general.assessment_result.metrics.passing_grade') }}</div>
                <div class="mt-1 font-semibold text-mentor-primary">{{ $assessment?->passing_grade ?? '-' }}%</div>
            </div>
        </div>

        <div class="mt-6 rounded-2xl border p-5 text-left {{ $attempt->passed ? 'border-emerald-200 bg-emerald-50' : 'border-rose-200 bg-rose-50' }}">
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
            <div class="mt-6 rounded-2xl border border-slate-200 bg-slate-50 p-5 text-left">
                <div class="text-xs uppercase tracking-wide text-slate-400">{{ __('general.assessment_result.certificate.title') }}</div>
                <div class="mt-1 font-semibold text-mentor-primary">
                    {{ $certificate->certificate_number }}
                </div>
                <div class="mt-1 text-sm text-slate-600">
                    {{ __('general.assessment_result.certificate.status', ['status' => strtoupper($certificate->status)]) }}
                </div>
            </div>
        @endif

        <div class="mt-8 flex flex-wrap justify-center gap-3">
            <a href="{{ localized_route('learning.dashboard') }}"
               class="admin-primary-button rounded-xl px-5 py-3 text-sm">
                {{ __('general.assessment_result.actions.back_to_learning') }}
            </a>

            @if($assessment?->course?->slug)
                <a href="{{ localized_route('courses.show', $assessment->course->slug) }}"
                   class="admin-neutral-button rounded-xl px-5 py-3 text-sm">
                    {{ __('general.assessment_result.actions.back_to_course') }}
                </a>
            @endif
        </div>
    </section>
</div>
