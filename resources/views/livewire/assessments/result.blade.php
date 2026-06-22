@php
    use Illuminate\Support\Str;

    $score = (float) ($attempt->score ?? 0);
    $passingGrade = (float) ($assessment?->passing_grade ?? 0);
    $totalQuestions = max(
        (int) ($attempt->total_questions ?? 0),
        $correctAnswers + $wrongAnswers + $unansweredQuestions
    );
    $scoreWidth = min(100, max(0, $score));

    $course = $assessment?->course;
    $poster = $course?->poster ?? $course?->image ?? null;
    $posterSrc = null;

    if ($poster) {
        if (Str::startsWith($poster, ['http://', 'https://'])) {
            $posterSrc = $poster;
        } elseif (file_exists(public_path($poster))) {
            $posterSrc = asset($poster);
        } elseif (file_exists(public_path('storage/' . $poster))) {
            $posterSrc = asset('storage/' . $poster);
        }
    }

    $statusClasses = $attempt->passed
        ? [
            'badge' => 'bg-emerald-100 text-emerald-700',
            'accent' => 'text-emerald-700',
            'surface' => 'bg-emerald-50 border-emerald-200',
            'bar' => 'bg-emerald-500',
        ]
        : [
            'badge' => 'bg-rose-100 text-rose-700',
            'accent' => 'text-rose-700',
            'surface' => 'bg-rose-50 border-rose-200',
            'bar' => 'bg-rose-500',
        ];
@endphp

<div class="min-h-screen bg-white px-4 pb-16 pt-8 text-[#0f172a] sm:px-6 sm:pb-24 sm:pt-12 lg:px-8">
    <main class="mx-auto max-w-6xl space-y-8">
        <section class="overflow-hidden rounded-[2rem] border border-[#d9ecfb] bg-white">
            <div class="relative overflow-hidden bg-[#eef8ff] px-6 py-8 sm:px-8 sm:py-10">
                <div class="pointer-events-none absolute -left-20 -top-20 h-52 w-52 rounded-full bg-[#7dd3fc]/35 blur-3xl" aria-hidden="true"></div>
                <div class="pointer-events-none absolute -bottom-24 right-0 h-64 w-64 rounded-full bg-[#35A7FF]/15 blur-3xl" aria-hidden="true"></div>

                <div class="relative grid items-start gap-8 lg:grid-cols-[minmax(0,1.15fr)_320px]">
                    <div class="min-w-0">
                        <p class="text-sm font-bold uppercase tracking-[0.16em] text-[#35A7FF]">
                            {{ __('general.assessment_result.title') }}
                        </p>
                        <h1 class="mt-3 text-3xl font-bold leading-tight text-[#004777] sm:text-4xl">
                            {{ $assessment?->title ?? __('general.assessment_result.default_title') }}
                        </h1>
                        <p class="mt-4 max-w-2xl text-sm leading-7 text-slate-600 sm:text-base">
                            {{ __('general.assessment_result.course', [
                                'course' => $course?->title ?? '-',
                            ]) }}
                        </p>

                        <div class="mt-6 flex flex-wrap items-center gap-3">
                            <span class="inline-flex rounded-full px-4 py-2 text-xs font-bold uppercase tracking-wide {{ $statusClasses['badge'] }}">
                                {{ $attempt->passed ? __('general.assessment_result.status.passed') : __('general.assessment_result.status.failed') }}
                            </span>

                            <span class="inline-flex rounded-full border border-[#004777]/10 bg-white/80 px-4 py-2 text-xs font-semibold text-[#004777]">
                                {{ __('general.assessment_result.metrics.submitted_at') }}: {{ $attempt->submitted_at?->format('d M Y, H:i') ?? '-' }}
                            </span>
                        </div>
                    </div>

                    <div class="overflow-hidden rounded-[1.5rem] border border-white/80 bg-white/80 p-3 backdrop-blur">
                        @if($posterSrc)
                            <img
                                src="{{ $posterSrc }}"
                                alt="{{ $course?->title ?? __('general.assessment_result.default_title') }}"
                                class="aspect-[4/3] w-full rounded-[1.1rem] object-cover"
                            >
                        @else
                            <div class="flex aspect-[4/3] w-full items-center justify-center rounded-[1.1rem] bg-gradient-to-br from-[#004777]/10 to-[#35A7FF]/20 text-sm font-semibold text-[#004777]">
                                {{ $course?->title ?? __('general.assessment_result.default_title') }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </section>

        <section class="grid gap-6 lg:grid-cols-[minmax(0,1.2fr)_340px]">
            <div class="rounded-[1.75rem] border border-slate-200 bg-white p-6 sm:p-8">
                <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-4">
                    <div class="sm:col-span-2 xl:col-span-1">
                        <p class="text-xs font-bold uppercase tracking-[0.16em] text-slate-500">
                            {{ __('general.assessment_result.metrics.score') }}
                        </p>
                        <div class="mt-3 text-5xl font-bold leading-none text-[#004777] sm:text-6xl">
                            {{ number_format($score, 0) }}
                        </div>
                        <p class="mt-2 text-sm text-slate-500">
                            {{ __('general.assessment_result.metrics.passing_grade') }}: {{ number_format($passingGrade, 0) }}%
                        </p>
                    </div>

                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                            {{ __('general.assessment_result.metrics.questions') }}
                        </p>
                        <p class="mt-2 text-3xl font-bold text-[#004777]">{{ $totalQuestions }}</p>
                    </div>

                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                            {{ __('general.assessment_result.metrics.correct') }}
                        </p>
                        <p class="mt-2 text-3xl font-bold text-emerald-600">{{ $correctAnswers }}</p>
                    </div>

                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                            {{ __('general.assessment_result.metrics.wrong') }}
                        </p>
                        <p class="mt-2 text-3xl font-bold text-rose-600">{{ $wrongAnswers }}</p>
                    </div>
                </div>

                <div class="mt-8">
                    <div class="flex items-center justify-between gap-4 text-sm">
                        <span class="font-medium text-slate-500">{{ __('general.assessment_result.metrics.score') }}</span>
                        <span class="font-semibold text-[#004777]">{{ number_format($score, 0) }}%</span>
                    </div>

                    <div class="mt-3 h-3 overflow-hidden rounded-full bg-slate-200">
                        <div
                            class="h-full rounded-full {{ $statusClasses['bar'] }}"
                            style="width: {{ $scoreWidth }}%"
                        ></div>
                    </div>
                </div>

                <div class="mt-8 border-t border-slate-200 pt-6">
                    <h2 class="text-lg font-bold {{ $statusClasses['accent'] }}">
                        {{ $attempt->passed ? __('general.assessment_result.notice.passed') : __('general.assessment_result.notice.failed') }}
                    </h2>
                    <p class="mt-2 max-w-3xl text-sm leading-7 text-slate-600">
                        {{ $attempt->passed
                            ? __('general.assessment_result.notice.passed_description')
                            : __('general.assessment_result.notice.failed_description') }}
                    </p>
                </div>
            </div>

            <aside class="rounded-[1.75rem] border border-slate-200 bg-white p-6 sm:p-8">
                <h2 class="text-lg font-bold text-[#004777]">
                    Ringkasan
                </h2>

                <dl class="mt-5 space-y-4 text-sm">
                    <div class="flex items-start justify-between gap-4 border-b border-slate-100 pb-4">
                        <dt class="text-slate-500">{{ __('general.assessment_result.metrics.submitted_at') }}</dt>
                        <dd class="text-right font-semibold text-[#004777]">
                            {{ $attempt->submitted_at?->format('d M Y, H:i') ?? '-' }}
                        </dd>
                    </div>

                    <div class="flex items-start justify-between gap-4 border-b border-slate-100 pb-4">
                        <dt class="text-slate-500">{{ __('general.assessment_result.metrics.passing_grade') }}</dt>
                        <dd class="font-semibold text-[#004777]">{{ number_format($passingGrade, 0) }}%</dd>
                    </div>

                    @if($unansweredQuestions > 0)
                        <div class="flex items-start justify-between gap-4 border-b border-slate-100 pb-4">
                            <dt class="text-slate-500">Tidak dijawab</dt>
                            <dd class="font-semibold text-[#004777]">{{ $unansweredQuestions }}</dd>
                        </div>
                    @endif

                    @if($certificate)
                        <div class="flex items-start justify-between gap-4">
                            <dt class="text-slate-500">{{ __('general.assessment_result.certificate.title') }}</dt>
                            <dd class="break-all text-right font-semibold text-[#004777]">
                                {{ $certificate->certificate_number }}
                            </dd>
                        </div>
                    @endif
                </dl>

                <div class="mt-6 flex flex-col gap-3">
                    @if($course?->slug)
                        <a href="{{ localized_route('courses.show', $course->slug) }}"
                           class="inline-flex items-center justify-center rounded-xl bg-[#004777] px-5 py-3 text-sm font-semibold text-white transition hover:bg-[#00395f]">
                            {{ __('general.assessment_result.actions.back_to_course') }}
                        </a>
                    @endif

                    <a href="{{ localized_route('learning.dashboard') }}"
                       class="inline-flex items-center justify-center rounded-xl border border-slate-200 px-5 py-3 text-sm font-semibold text-[#004777] transition hover:bg-slate-50">
                        {{ __('general.assessment_result.actions.back_to_learning') }}
                    </a>
                </div>
            </aside>
        </section>
    </main>
</div>
