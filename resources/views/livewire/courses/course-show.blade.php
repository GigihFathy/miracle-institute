@php
    $isMentor = auth()->check() && session('active_role') === 'disciples';
    $isStudent = auth()->check() && session('active_role') === 'student';
    $canTrack = auth()->check() && $enrolled;

    $topicsToRender = $isMentor ? $mentoredTopics : $filteredTopics;
    $totalTopicsCount = $course->topics->count();
    $completedTopicsCount = $this->completedTopicsCount ?? 0;
    $progressPercent = $totalTopicsCount > 0 ? (int) round(($completedTopicsCount / $totalTopicsCount) * 100) : 0;
    $hasPassedAssessment = $this->hasPassedAssessment;
    $continueTopic = $filteredTopics->firstWhere('progress_status', 'in_progress')
        ?? $filteredTopics->firstWhere('progress_status', 'available')
        ?? $filteredTopics->first();

    $poster = $course->poster ?? $course->image ?? null;
    $posterSrc = null;

    if ($poster) {
        if (\Illuminate\Support\Str::startsWith($poster, ['http://', 'https://'])) {
            $posterSrc = $poster;
        } elseif (file_exists(public_path($poster))) {
            $posterSrc = asset($poster);
        } elseif (file_exists(public_path('storage/' . $poster))) {
            $posterSrc = asset('storage/' . $poster);
        }
    }
@endphp

<div class="pb-10">
    <div class="space-y-6 lg:px-36">
        @if(session('success'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="rounded-2xl border border-rose-200 bg-rose-50 px-5 py-4 text-sm text-rose-700">
                {{ session('error') }}
            </div>
        @endif

        <section class="space-y-6">
            <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6 lg:p-8">
                <div class="space-y-6">
                <a
                    href="{{ localized_route('courses.index') }}"
                    class="inline-flex items-center gap-2 rounded-xl border border-[#35A7FF]/30 bg-white px-4 py-2 text-sm font-medium text-[#004777] transition hover:bg-[#35A7FF]/10"
                >
                    <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M17 10a.75.75 0 0 1-.75.75H5.56l3.22 3.22a.75.75 0 1 1-1.06 1.06l-4.5-4.5a.75.75 0 0 1 0-1.06l4.5-4.5a.75.75 0 0 1 1.06 1.06L5.56 9.25h10.69A.75.75 0 0 1 17 10Z" clip-rule="evenodd" />
                    </svg>
                    <span>{{ __('general.assessment_result.actions.back_to_course') }}</span>
                </a>

                <div class="flex flex-col gap-5 border-b border-slate-200 pb-6 sm:flex-row sm:items-start">
                    <div class="relative aspect-16/10 w-full max-w-60 shrink-0 overflow-hidden rounded-2xl border border-slate-200 bg-slate-100 sm:w-60">
                        <img
                            src="{{ $posterSrc ?? asset('images/thumbnail/thumbnail_candle.png') }}"
                            alt="{{ $course->title }}"
                            class="h-full w-full object-cover"
                        >
                    </div>

                    <div class="min-w-0 flex-1">
                        <div class="truncate text-[10px] uppercase tracking-[0.22em] text-[#004777]/60 sm:text-xs">
                            {{ $course->studyProgram?->title }}
                        </div>

                        <h1 class="mt-2 text-2xl font-bold leading-tight tracking-tight text-[#004777] sm:text-3xl">
                            {{ $course->title }}
                        </h1>

                        <p class="mt-3 max-w-3xl text-sm leading-6 text-slate-600">
                            {{ $course->description ?: __('general.course_catalog.defaults.no_description') }}
                        </p>

                        <div class="mt-5 flex flex-wrap items-center gap-2">
                            @if(!$isMentor)
                                @if(!auth()->check())
                                    <a
                                        href="{{ localized_route('login') }}"
                                        class="admin-primary-button inline-flex items-center justify-center rounded-xl px-4 py-2.5 text-xs sm:text-sm"
                                    >
                                        {{ __('general.course_show.login_to_track') }}
                                    </a>
                                @else
                                    @if($enrolled)
                                        @if($continueTopic)
                                            <a
                                                href="{{ localized_route('topics.show', $continueTopic->slug) }}"
                                                class="admin-primary-button inline-flex items-center justify-center rounded-xl px-4 py-2.5 text-xs leading-none sm:text-sm"
                                            >
                                                {{ __('general.course_show.continue_learning') }}
                                            </a>
                                        @endif
                                    @else
                                        <button
                                            wire:click="enroll"
                                            wire:loading.attr="disabled"
                                            wire:target="enroll"
                                            class="admin-primary-button inline-flex items-center justify-center rounded-xl px-4 py-2.5 text-xs disabled:opacity-70 sm:text-sm"
                                        >
                                            <span wire:loading.remove wire:target="enroll">{{ __('general.course_show.enroll') }}</span>

                                            <span wire:loading.flex wire:target="enroll" class="items-center gap-2">
                                                <svg class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                                                </svg>
                                                {{ __('general.course_show.processing') }}
                                            </span>
                                        </button>
                                    @endif
                                @endif
                            @else
                                <span class="inline-flex items-center rounded-xl border border-slate-200 bg-slate-100 px-4 py-2 text-xs text-slate-600 sm:text-sm">
                                    {{ __('general.course_show.mentor_mode') }}
                                </span>
                            @endif
                        </div>

                        @guest
                            <div class="mt-4 rounded-2xl border border-slate-200 bg-slate-50 p-4 text-xs leading-6 text-slate-600 sm:text-sm">
                                {{ __('general.course_show.guest_notice') }}
                            </div>
                        @endguest
                    </div>
                </div>

                <div class="grid gap-3 border-b border-slate-200 pb-6 sm:grid-cols-3">
                    <div class="rounded-2xl bg-slate-50 px-4 py-4">
                        <div class="text-xs uppercase tracking-wide text-[#004777]/70">
                            {{ __('general.course_show.topics_stat') }}
                        </div>
                        <div class="mt-1 text-2xl font-bold text-[#004777]">
                            {{ $totalTopicsCount }}
                        </div>
                    </div>

                    <div class="rounded-2xl bg-slate-50 px-4 py-4">
                        <div class="text-xs uppercase tracking-wide text-[#004777]/70">
                            {{ __('general.course_show.progress_stat') }}
                        </div>
                        <div class="mt-1 text-2xl font-bold text-[#004777]">
                            {{ $progressPercent }}%
                        </div>
                        <div class="mt-2 text-xs text-slate-500">
                            {{ __('general.course_show.completed_count', ['count' => $completedTopicsCount]) }}
                        </div>
                        <div class="mt-3 h-2 overflow-hidden rounded-full bg-white">
                            <div class="h-full rounded-full bg-[#004777]" style="width: {{ $progressPercent }}%"></div>
                        </div>
                    </div>

                    <div class="rounded-2xl bg-slate-50 px-4 py-4">
                        <div class="text-xs uppercase tracking-wide text-[#004777]/70">
                            {{ __('general.course_show.assessment_stat') }}
                        </div>
                        <div class="mt-2 text-lg font-semibold text-[#004777]">
                            {{ $assessmentMeta ? ($hasPassedAssessment ? __('general.course_show.assessment_completed') : ($this->assessmentUnlocked ? __('general.course_show.assessment_available') : __('general.course_show.assessment_pending'))) : __('general.course_show.not_published') }}
                        </div>
                    </div>
                </div>

                @if($isStudent)
                    <div class="space-y-5">
                        <div>
                            <h2 class="text-sm font-semibold uppercase tracking-wide text-[#004777]">{{ __('general.course_show.course_access') }}</h2>
                            <p class="mt-1 text-sm text-slate-500">{{ __('general.course_show.course_access_description') }}</p>
                        </div>

                        <div class="grid gap-6 lg:grid-cols-2">
                            <div class="space-y-3 rounded-2xl border border-slate-200 bg-slate-50 p-4 sm:p-5">
                                <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                    <div>
                                        <div class="text-xs uppercase tracking-wide text-slate-400">{{ __('general.course_show.assessment_label') }}</div>
                                        <h3 class="mt-1 text-lg font-semibold text-[#004777]">
                                            {{ $assessmentMeta['title'] ?? __('general.course_show.assessment_label') }}
                                        </h3>
                                    </div>

                                    @if($assessmentMeta)
                                        @if($hasPassedAssessment)
                                            <span class="inline-flex self-start whitespace-nowrap rounded-full border border-emerald-200 bg-emerald-50 px-2.5 py-1 text-[10px] font-medium text-emerald-700 sm:text-[11px]">{{ __('general.course_show.assessment_passed_badge') }}</span>
                                        @elseif($this->assessmentUnlocked)
                                            <span class="inline-flex self-start whitespace-nowrap rounded-full border border-emerald-200 bg-emerald-50 px-2.5 py-1 text-[10px] font-medium text-emerald-700 sm:text-[11px]">{{ __('general.course_show.unlocked') }}</span>
                                        @else
                                            <span class="inline-flex self-start whitespace-nowrap rounded-full border border-amber-200 bg-amber-50 px-2.5 py-1 text-[10px] font-medium text-amber-700 sm:text-[11px]">{{ __('general.course_show.locked') }}</span>
                                        @endif
                                    @else
                                        <span class="inline-flex self-start whitespace-nowrap rounded-full border border-slate-200 bg-slate-100 px-2.5 py-1 text-[10px] font-medium text-slate-600 sm:text-[11px]">{{ __('general.course_show.not_published') }}</span>
                                    @endif
                                </div>

                                <p class="text-sm leading-6 text-slate-500">
                                    @if(! $assessmentMeta)
                                        {{ __('general.course_show.assessment_unavailable') }}
                                    @elseif($hasPassedAssessment)
                                        {{ __('general.course_show.assessment_passed_description') }}
                                    @elseif($this->assessmentUnlocked)
                                        {{ __('general.course_show.assessment_ready_description') }}
                                    @else
                                        {{ __('general.course_show.assessment_pending_description') }}
                                    @endif
                                </p>

                                <div class="flex flex-wrap gap-2">
                                    @if($assessmentMeta && $hasPassedAssessment)
                                        <span class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-2 text-sm font-medium text-emerald-700">
                                            {{ __('general.course_show.assessment_completed_cta') }}
                                        </span>
                                    @elseif($assessmentMeta && $this->assessmentUnlocked)
                                        <a href="{{ localized_route('assessments.take', $assessment->id) }}" class="admin-primary-button inline-flex items-center rounded-xl px-4 py-2 text-sm">
                                            {{ $this->activeAttempt ? __('general.course_show.resume_test') : __('general.course_show.start_test') }}
                                        </a>
                                    @elseif($assessmentMeta)
                                        <span class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm text-slate-600">
                                            {{ __('general.course_show.complete_topics_first') }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="space-y-3 rounded-2xl border border-slate-200 bg-slate-50 p-4 sm:p-5">
                                <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                    <div>
                                        <div class="text-xs uppercase tracking-wide text-slate-400">{{ __('general.course_show.certificate_label') }}</div>
                                        <h3 class="mt-1 text-lg font-semibold text-[#004777]">
                                            {{ __('general.course_show.certificate_label') }}
                                        </h3>
                                    </div>

                                    @if($courseCertificate)
                                        <span class="inline-flex self-start whitespace-nowrap rounded-full border border-emerald-200 bg-emerald-50 px-2.5 py-1 text-[10px] font-medium text-emerald-700 sm:text-[11px]">{{ __('general.course_show.issued') }}</span>
                                    @elseif($certificateEligibility['eligible'])
                                        <span class="inline-flex self-start whitespace-nowrap rounded-full border border-emerald-200 bg-emerald-50 px-2.5 py-1 text-[10px] font-medium text-emerald-700 sm:text-[11px]">{{ __('general.course_show.eligible') }}</span>
                                    @else
                                        <span class="inline-flex self-start whitespace-nowrap rounded-full border border-amber-200 bg-amber-50 px-2.5 py-1 text-[10px] font-medium text-amber-700 sm:text-[11px]">{{ __('general.course_show.locked') }}</span>
                                    @endif
                                </div>

                                <p class="text-sm leading-6 text-slate-500">
                                    @if($courseCertificate)
                                        {{ __('general.course_show.certificate_issued_description') }}
                                    @elseif($certificateEligibility['eligible'])
                                        {{ __('general.course_show.certificate_ready') }}
                                    @else
                                        {{ __('general.course_show.certificate_unavailable') }}
                                    @endif
                                </p>

                                <div class="flex flex-wrap gap-2">
                                    @if($courseCertificate && $enrolled)
                                        <a
                                            href="{{ localized_route('certificates.download', $courseCertificate->id) }}"
                                            class="inline-flex items-center rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-2 text-sm font-medium text-emerald-700 transition hover:bg-emerald-100"
                                        >
                                            {{ __('general.course_show.download_certificate') }}
                                        </a>
                                    @elseif($certificateEligibility['eligible'])
                                        <a href="{{ localized_route('certificates.course.claim', $course->id) }}" class="admin-primary-button inline-flex items-center rounded-xl px-4 py-2 text-sm">
                                            {{ __('general.course_show.claim_certificate') }}
                                        </a>
                                    @else
                                        <span class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm text-slate-600">
                                            {{ __('general.course_show.complete_topics_first') }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                </div>
            </div>
        </section>

        @if($isStudent)
            <section class="space-y-5">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <div class="text-xs uppercase tracking-[0.18em] text-[#004777]/60">
                            {{ __('general.course_show.course_overview') }}
                        </div>
                        <h2 class="mt-2 text-xl font-bold tracking-tight text-[#004777] sm:text-2xl">
                            {{ __('general.course_show.course_topics') }}
                        </h2>
                        <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-500">
                            {{ __('general.course_show.course_overview_description') }}
                        </p>
                    </div>

                    <div class="flex items-center gap-2 text-xs text-slate-500">
                        <span>{{ trans_choice('general.course_show.topics_count', $totalTopicsCount, ['count' => $totalTopicsCount]) }}</span>
                        <span>&middot;</span>
                        <span>{{ __('general.course_show.completed_count', ['count' => $completedTopicsCount]) }}</span>
                    </div>
                </div>

                <div class="overflow-hidden border border-slate-200 bg-white">
                    @foreach($topicsToRender as $index => $topic)
                        @php
                            $status = $topic->progress_status ?? 'not_started';
                            $sessionStatus = $topic->videoSessions->first()->status ?? null;

                            $badge = match ($status) {
                                'completed' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                                'in_progress' => 'bg-amber-100 text-amber-700 border-amber-200',
                                'available' => 'bg-indigo-100 text-indigo-700 border-indigo-200',
                                'mentor' => 'bg-slate-100 text-slate-700 border-slate-200',
                                default => 'bg-slate-100 text-slate-600 border-slate-200',
                            };

                            $sessionBadge = match ($sessionStatus) {
                                'completed' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                                'ongoing' => 'bg-amber-100 text-amber-700 border-amber-200',
                                'scheduled' => 'bg-indigo-100 text-indigo-700 border-indigo-200',
                                'cancelled' => 'bg-red-100 text-red-700 border-red-200',
                                default => 'bg-slate-100 text-slate-600 border-slate-200',
                            };
                        @endphp

                        <div class="{{ $index !== 0 ? 'border-t border-slate-200' : '' }} p-5 sm:p-6">
                            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                                <div class="flex min-w-0 items-start gap-3 sm:gap-4">
                                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-[#004777] text-sm font-bold text-white">
                                        {{ $index + 1 }}
                                    </div>

                                    <div class="min-w-0">
                                        <h3 class="text-base font-semibold leading-tight text-[#004777] sm:text-lg">
                                            {{ $topic->name }}
                                        </h3>

                                        <p class="mt-1 text-sm leading-6 text-slate-500">
                                            {{ $topic->description ?: __('general.course_show.topic_description_fallback') }}
                                        </p>

                                        <div class="mt-3 flex flex-wrap gap-2 text-[11px] sm:text-xs">
                                            <span class="rounded-full border px-2.5 py-1 {{ $badge }}">
                                                @if($status === 'available')
                                                    {{ __('general.course_show.status.available') }}
                                                @elseif($status === 'mentor')
                                                    {{ __('general.course_show.status.review') }}
                                                @elseif($status === 'completed')
                                                    {{ __('general.course_show.status.completed') }}
                                                @elseif($status === 'in_progress')
                                                    {{ __('general.course_show.status.in_progress') }}
                                                @else
                                                    {{ __('general.course_show.status.not_started') }}
                                                @endif
                                            </span>

                                            <span class="rounded-full border px-2.5 py-1 {{ $sessionBadge }}">
                                                {{ $topic->videoSessions->isNotEmpty()
                                                    ? __('general.course_show.session_label', ['status' => __('general.course_show.session_status.' . ($sessionStatus ?? 'none'))])
                                                    : __('general.course_show.no_session') }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex shrink-0 flex-wrap gap-2 sm:justify-end">
                                    @guest
                                        <a href="{{ localized_route('login') }}" class="admin-primary-button inline-flex items-center justify-center rounded-xl px-4 py-2.5 text-xs sm:text-sm">
                                            {{ __('general.course_show.topic_cta_guest') }}
                                        </a>
                                    @endguest

                                    @auth
                                        <a href="{{ localized_route('topics.show', $topic->slug) }}" class="admin-primary-button inline-flex items-center rounded-xl px-5 py-2.5 text-xs sm:text-sm">
                                            {{ __('general.course_show.topic_cta_enrolled') }}
                                        </a>
                                    @endauth
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        @elseif($isMentor)
            <section class="space-y-5">
                <div class="flex flex-col gap-2">
                    <h2 class="text-xl font-bold tracking-tight text-[#004777] sm:text-2xl">
                        {{ __('general.course_show.mentored_topics.title') }}
                    </h2>
                    <p class="text-sm text-slate-500">
                        {{ __('general.course_show.mentored_topics.description') }}
                    </p>
                </div>

                @if($hasMentoredTopics)
                    <div class="border border-slate-200 bg-white p-5 sm:p-6">
                        <div class="space-y-3">
                            @foreach($mentoredTopics as $topic)
                                @php
                                    $mentorRole = $topic->mentor_role ?? 'collaborator';
                                    $roleBadge = $mentorRole === 'owner'
                                        ? 'bg-emerald-50 text-emerald-700 border-emerald-200'
                                        : 'bg-indigo-50 text-indigo-700 border-indigo-200';

                                    $roleLabel = $mentorRole === 'owner'
                                        ? __('general.course_show.role.owner')
                                        : __('general.course_show.role.collaborator');
                                @endphp

                                <div class="border border-slate-200 p-4">
                                    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                        <div class="min-w-0">
                                            <div class="text-sm font-semibold text-[#004777]">
                                                {{ $topic->name }}
                                            </div>
                                            <div class="mt-1 line-clamp-2 text-xs text-slate-500">
                                                {{ $topic->description }}
                                            </div>
                                        </div>

                                        <div class="flex shrink-0 flex-wrap items-center gap-2">
                                            <span class="rounded-full border px-3 py-1 text-[11px] font-medium {{ $roleBadge }}">
                                                {{ $roleLabel }}
                                            </span>

                                            <a href="{{ localized_route('mentor.topics.show', $topic->slug) }}" class="admin-neutral-button rounded-xl px-3 py-2 text-xs">
                                                {{ __('general.course_show.workspace') }}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="border border-slate-200 bg-white p-5 sm:p-6">
                        <div class="border border-dashed bg-slate-50 p-6">
                            <div class="text-sm font-semibold text-[#004777]">
                                {{ __('general.course_show.mentored_topics.empty_title') }}
                            </div>
                            <p class="mt-2 text-sm leading-6 text-slate-500">
                                {{ __('general.course_show.mentored_topics.empty_description') }}
                            </p>
                        </div>
                    </div>
                @endif
            </section>
        @endif
    </div>

    @if($showAssessmentModal && $assessmentMeta && $isStudent)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/60 p-4">
            <div class="absolute inset-0" wire:click="closeAssessmentModal"></div>

            <div class="relative z-10 max-h-[90vh] w-full max-w-6xl overflow-y-auto border border-slate-200 bg-white shadow-2xl">
                <div class="flex items-center justify-between border-b border-slate-200 px-6 py-5">
                    <div>
                        <h3 class="text-lg font-semibold text-[#004777]">{{ __('general.course_show.assessment_modal.title') }}</h3>
                        <p class="text-sm text-slate-500">{{ $course->title }}</p>
                    </div>

                    <button
                        type="button"
                        wire:click="closeAssessmentModal"
                        class="rounded-xl border border-slate-200 px-3 py-2 text-sm text-slate-600 transition hover:bg-slate-50"
                    >
                        {{ __('general.course_show.close') }}
                    </button>
                </div>

                <div class="grid grid-cols-1 gap-6 p-6 xl:grid-cols-[0.95fr_1.05fr]">
                    <div class="space-y-4">
                        <div class="border border-slate-200 p-5">
                            <div class="text-xs uppercase tracking-wide text-[#004777]/60">{{ __('general.course_show.assessment_modal.assessment') }}</div>
                            <h4 class="mt-2 text-2xl font-bold text-[#004777]">{{ $assessmentMeta['title'] }}</h4>
                            <p class="mt-2 text-sm text-slate-500">
                                {{ $assessmentMeta['status'] }}
                            </p>
                        </div>

                        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                            <div class="border border-slate-200 p-4">
                                <div class="text-xs text-slate-500">{{ __('general.course_show.assessment_modal.questions') }}</div>
                                <div class="mt-1 font-semibold text-[#004777]">{{ $assessmentMeta['question_count'] }}</div>
                            </div>

                            <div class="border border-slate-200 p-4">
                                <div class="text-xs text-slate-500">{{ __('general.course_show.assessment_modal.passing_grade') }}</div>
                                <div class="mt-1 font-semibold text-[#004777]">{{ $assessmentMeta['passing_grade'] }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="border border-slate-200 bg-slate-50 p-6">
                        <div class="mb-4 text-xs uppercase tracking-wide text-slate-400">
                            {{ __('general.course_show.assessment_modal.instructions') }}
                        </div>

                        <ul class="list-disc space-y-3 pl-5 text-sm text-slate-700">
                            @foreach($assessmentMeta['instructions'] as $instruction)
                                <li>{{ $instruction }}</li>
                            @endforeach
                        </ul>

                        <div class="mt-6 flex flex-wrap gap-3">
                            @if($hasPassedAssessment)
                                <span class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-2 text-sm font-medium text-emerald-700">
                                    {{ __('general.course_show.assessment_completed_cta') }}
                                </span>
                            @elseif($this->assessmentUnlocked)
                                @if($this->activeAttempt)
                                    <a href="{{ localized_route('assessments.take', $assessment->id) }}" class="rounded-xl bg-amber-500 px-4 py-2 text-sm text-white transition hover:bg-amber-600">
                                        {{ __('general.course_show.resume_test') }}
                                    </a>
                                @else
                                    <a href="{{ localized_route('assessments.take', $assessment->id) }}" class="admin-primary-button rounded-xl px-4 py-2 text-sm">
                                        {{ __('general.course_show.start_test') }}
                                    </a>
                                @endif
                            @else
                                <span class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm text-slate-500">
                                    {{ __('general.course_show.locked_until_complete') }}
                                </span>
                            @endif

                            <button type="button" wire:click="closeAssessmentModal" class="admin-neutral-button rounded-xl px-4 py-2 text-sm">
                                {{ __('general.course_show.close') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
