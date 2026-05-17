@php
    $isMentor = auth()->check() && session('active_role') === 'disciples';
    $isStudent = auth()->check() && session('active_role') === 'student';
    $canTrack = auth()->check() && $enrolled;

    $topicsToRender = $isMentor ? $mentoredTopics : $filteredTopics;
@endphp

<div>
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
        
        <section class="overflow-hidden rounded-2xl border bg-white shadow-sm">
            <div class="grid lg:grid-cols-[1.1fr_0.9fr]">
                <div class="space-y-5 p-4 sm:p-6 lg:p-7">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                        <div class="min-w-0">
                            <div class="truncate text-[10px] uppercase tracking-[0.22em] text-slate-400 sm:text-xs">
                                {{ $course->studyProgram?->title }}
                            </div>
        
                            <h1 class="mt-2 text-2xl font-bold leading-tight tracking-tight text-slate-900 sm:text-3xl">
                                {{ $course->title }}
                            </h1>
        
                            <p class="mt-3 line-clamp-4 text-sm leading-6 text-slate-600">
                                {{ $course->description }}
                            </p>
                        </div>
                    </div>
        
                    <div class="flex flex-wrap gap-2">
                        @if(!$isMentor)
                            @if(!auth()->check())
                                <a href="{{ localized_route('login') }}"
                                   class="inline-flex items-center justify-center rounded-xl bg-[#004777] px-4 py-2.5 text-xs font-medium text-white transition hover:bg-[#003560] sm:text-sm">
                                    {{ __('general.course_show.login_to_track') }}
                                </a>
                            @else
                                @if($enrolled)
                                    <span class="inline-flex items-center rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-2 text-xs font-medium text-emerald-700 sm:text-sm">
                                        {{ __('general.course_show.enrolled') }}
                                    </span>
                                @else
                                    <button
                                        wire:click="enroll"
                                        wire:loading.attr="disabled"
                                        wire:target="enroll"
                                        class="inline-flex cursor-pointer items-center justify-center rounded-xl bg-[#004777] px-4 py-2.5 text-xs font-medium text-white transition hover:bg-[#003560] disabled:cursor-not-allowed disabled:opacity-70 sm:text-sm">
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
                            <span class="inline-flex items-center rounded-xl border bg-slate-100 px-4 py-2 text-xs text-slate-600 sm:text-sm">
                                {{ __('general.course_show.mentor_mode') }}
                            </span>
                        @endif
                    </div>
        
                    @guest
                        <div class="rounded-xl border bg-slate-50 p-3 text-xs leading-6 text-slate-600 sm:text-sm">
                            {{ __('general.course_show.guest_notice') }}
                        </div>
                    @endguest

                    @if($isStudent)
                        <div class="border-t pt-4">
                            <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                                <div>
                                    <h2 class="text-sm font-semibold uppercase tracking-wide text-[#004777]">{{ __('general.course_show.course_access') }}</h2>
                                    <p class="mt-1 text-xs text-slate-500">{{ __('general.course_show.course_access_description') }}</p>
                                </div>

                                <div class="flex flex-col gap-3 text-sm text-slate-700 sm:flex-row sm:items-center sm:gap-6">
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs uppercase tracking-wide text-slate-400">{{ __('general.course_show.assessment_label') }}</span>

                                        @if($assessmentMeta)
                                            @if($this->assessmentUnlocked)
                                                <span class="rounded-full border border-emerald-200 bg-emerald-50 px-2.5 py-1 text-[11px] text-emerald-700">{{ __('general.course_show.unlocked') }}</span>
                                            @else
                                                <span class="rounded-full border border-amber-200 bg-amber-50 px-2.5 py-1 text-[11px] text-amber-700">{{ __('general.course_show.locked') }}</span>
                                            @endif
                                        @else
                                            <span class="rounded-full border border-slate-200 bg-slate-100 px-2.5 py-1 text-[11px] text-slate-600">{{ __('general.course_show.not_published') }}</span>
                                        @endif
                                    </div>

                                    <div class="flex items-center gap-2">
                                        <span class="text-xs uppercase tracking-wide text-slate-400">{{ __('general.course_show.certificate_label') }}</span>

                                        @if($courseCertificate)
                                            <span class="rounded-full border border-emerald-200 bg-emerald-50 px-2.5 py-1 text-[11px] text-emerald-700">{{ __('general.course_show.issued') }}</span>
                                        @elseif($certificateEligibility['eligible'])
                                            <span class="rounded-full border border-emerald-200 bg-emerald-50 px-2.5 py-1 text-[11px] text-emerald-700">{{ __('general.course_show.eligible') }}</span>
                                        @else
                                            <span class="rounded-full border border-amber-200 bg-amber-50 px-2.5 py-1 text-[11px] text-amber-700">{{ __('general.course_show.locked') }}</span>
                                        @endif
                                    </div>

                                    <div class="flex flex-wrap gap-2">
                                        @if($assessmentMeta)
                                            @if($this->assessmentUnlocked)
                                                <a href="{{ localized_route('assessments.take', $assessment->id) }}"
                                                   class="inline-flex items-center rounded-xl bg-[#004777] px-3 py-2 text-xs font-medium text-white transition hover:bg-[#003560]">
                                                    {{ $this->activeAttempt ? __('general.course_show.resume_test') : __('general.course_show.start_test') }}
                                                </a>
                                            @endif
                                        @endif

                                        @if($courseCertificate && $isStudent && $enrolled)
                                            <a href="{{ localized_route('certificates.download', $courseCertificate->id) }}"
                                               class="inline-flex items-center rounded-xl border border-emerald-200 bg-emerald-50 px-3 py-2 text-xs font-medium text-emerald-700 transition hover:bg-emerald-100">
                                                {{ __('general.course_show.download_certificate') }}
                                            </a>
                                        @elseif($certificateEligibility['eligible'])
                                            <a href="{{ localized_route('certificates.course.claim', $course->id) }}"
                                               class="inline-flex items-center rounded-xl bg-[#004777] px-3 py-2 text-xs font-medium text-white transition hover:bg-[#003560]">
                                                {{ __('general.course_show.claim_certificate') }}
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
        
                @php
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

                <div class="relative min-h-[220px] bg-slate-100 sm:min-h-[280px]">
                    <img src="{{ $posterSrc ?? asset('images/thumbnail/thumbnail_candle.png') }}"
                        alt="{{ $course->title }}"
                        class="h-full w-full object-cover">

                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950/35 via-transparent to-transparent"></div>
                </div>
            </div>
        </section>

        @if($isStudent)
            <section class="space-y-5">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-xl font-bold tracking-tight text-slate-900 sm:text-2xl">
                            {{ __('general.course_show.course_topics') }}
                        </h2>
                    </div>
        
                    <div class="hidden items-center gap-2 text-xs text-slate-500 md:flex">
                        <span>{{ trans_choice('general.course_show.topics_count', $course->topics->count(), ['count' => $course->topics->count()]) }}</span>
                        <span>•</span>
                        <span>{{ __('general.course_show.completed_count', ['count' => $this->completedTopicsCount]) }}</span>
                    </div>
                </div>
        
                <div class="overflow-hidden rounded-2xl border bg-white shadow-sm">
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
        
                        <div class="{{ $index !== 0 ? 'border-t' : '' }} p-4 sm:p-5">
                            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                                <div class="flex min-w-0 items-start gap-3 sm:gap-4">
                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-[#004777] text-sm font-bold text-white sm:h-11 sm:w-11 sm:text-base">
                                        {{ $index + 1 }}
                                    </div>

                                    <div class="min-w-0">
                                        <h3 class="text-base font-semibold leading-tight text-slate-900 sm:text-lg">
                                            {{ $topic->name }}
                                        </h3>

                                        <p class="mt-1 line-clamp-2 text-xs leading-6 text-slate-500 sm:text-sm">
                                            {{ $topic->description }}
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

                                            <span class="rounded-full px-2.5 py-1 {{ $sessionBadge }}">
                                                {{ $topic->videoSessions->isNotEmpty()
                                                    ? __('general.course_show.session_label', ['status' => __('general.course_show.session_status.' . ($sessionStatus ?? 'none'))])
                                                    : __('general.course_show.no_session') }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex shrink-0 flex-wrap gap-2 sm:justify-end">
                                    @guest
                                        <a href="{{ localized_route('login') }}"
                                           class="inline-flex items-center justify-center rounded-xl bg-[#004777] px-4 py-2.5 text-xs font-medium text-white transition hover:bg-[#003560] sm:text-sm">
                                            {{ __('general.course_show.login_to_access_topic') }}
                                        </a>
                                    @endguest

                                    @auth
                                        <a href="{{ localized_route('topics.show', $topic->slug) }}"
                                           class="inline-flex items-center rounded-xl bg-[#004777] px-6 py-2.5 text-xs font-medium text-white transition hover:bg-[#003560] sm:text-sm">
                                            {{ __('general.course_show.open_topic') }}
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
                    <h2 class="text-xl font-bold tracking-tight text-slate-900 sm:text-2xl">
                        {{ __('general.course_show.mentored_topics.title') }}
                    </h2>
                    <p class="text-sm text-slate-500">
                        {{ __('general.course_show.mentored_topics.description') }}
                    </p>
                </div>
        
                @if($hasMentoredTopics)
                    <div class="rounded-3xl border bg-white p-5 sm:p-6">
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
        
                                <div class="rounded-2xl border p-4">
                                    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                        <div class="min-w-0">
                                            <div class="text-sm font-semibold text-slate-900">
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
        
                                            <a href="{{ localized_route('mentor.topics.show', $topic->slug) }}"
                                               class="rounded-xl border border-[#004777] px-3 py-2 text-xs text-[#004777] transition hover:bg-[#004777] hover:text-white">
                                                {{ __('general.course_show.workspace') }}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="rounded-3xl border bg-white p-5 sm:p-6">
                        <div class="rounded-2xl border border-dashed bg-slate-50 p-6">
                            <div class="text-sm font-semibold text-slate-900">
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
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div class="absolute inset-0" wire:click="closeAssessmentModal"></div>

            <div class="relative z-10 w-full max-w-6xl overflow-y-auto rounded-2xl bg-white shadow-2xl max-h-[90vh]">
                <div class="flex items-center justify-between border-b p-6">
                    <div>
                        <h3 class="text-lg font-semibold">{{ __('general.course_show.assessment_modal.title') }}</h3>
                        <p class="text-sm text-slate-500">{{ $course->title }}</p>
                    </div>

                    <button type="button"
                            wire:click="closeAssessmentModal"
                            class="text-2xl leading-none text-slate-500 transition hover:text-black">
                        ✕
                    </button>
                </div>

                <div class="grid grid-cols-1 gap-6 p-6 xl:grid-cols-[0.95fr_1.05fr]">
                    <div class="space-y-4">
                        <div class="rounded-2xl border bg-slate-50 p-5">
                            <div class="text-xs uppercase tracking-wide text-slate-400">{{ __('general.course_show.assessment_modal.assessment') }}</div>
                            <h4 class="mt-2 text-2xl font-bold">{{ $assessmentMeta['title'] }}</h4>
                            <p class="mt-2 text-sm text-slate-500">
                                {{ $assessmentMeta['status'] }}
                            </p>
                        </div>

                        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                            <div class="rounded-xl border bg-slate-50 p-4">
                                <div class="text-xs text-slate-500">{{ __('general.course_show.assessment_modal.questions') }}</div>
                                <div class="mt-1 font-semibold">{{ $assessmentMeta['question_count'] }}</div>
                            </div>

                            <div class="rounded-xl border bg-slate-50 p-4">
                                <div class="text-xs text-slate-500">{{ __('general.course_show.assessment_modal.passing_grade') }}</div>
                                <div class="mt-1 font-semibold">{{ $assessmentMeta['passing_grade'] }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-2xl border bg-slate-50 p-6">
                        <div class="mb-4 text-xs uppercase tracking-wide text-slate-400">
                            {{ __('general.course_show.assessment_modal.instructions') }}
                        </div>

                        <ul class="list-disc space-y-3 pl-5 text-sm text-slate-700">
                            @foreach($assessmentMeta['instructions'] as $instruction)
                                <li>{{ $instruction }}</li>
                            @endforeach
                        </ul>

                        <div class="mt-6 flex flex-wrap gap-3">
                            @if($this->assessmentUnlocked)
                                @if($this->activeAttempt)
                                    <a href="{{ localized_route('assessments.take', $assessment->id) }}"
                                       class="rounded-xl bg-amber-500 px-4 py-2 text-sm text-white transition hover:bg-amber-600">
                                        {{ __('general.course_show.resume_test') }}
                                    </a>
                                @else
                                    <a href="{{ localized_route('assessments.take', $assessment->id) }}"
                                       class="rounded-xl bg-[#004777] px-4 py-2 text-sm text-white transition hover:bg-[#003560]">
                                        {{ __('general.course_show.start_test') }}
                                    </a>
                                @endif
                            @else
                                <span class="rounded-xl border px-4 py-2 text-sm text-slate-500">
                                    {{ __('general.course_show.locked_until_complete') }}
                                </span>
                            @endif

                            <button type="button"
                                    wire:click="closeAssessmentModal"
                                    class="rounded-xl border px-4 py-2 text-sm">
                                {{ __('general.course_show.close') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>