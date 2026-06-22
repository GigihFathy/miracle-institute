@php
    $isMentor = auth()->check() && session('active_role') === 'disciples';
    $isStudent = auth()->check() && session('active_role') === 'student';
    $canTrack = auth()->check() && $enrolled;

    $topicsToRender = $paginatedTopics;
    $totalTopicsCount = $studentTopics->count();
    $completedTopicsCount = $this->completedTopicsCount ?? 0;
    $progressPercent = $totalTopicsCount > 0 ? (int) round(($completedTopicsCount / $totalTopicsCount) * 100) : 0;
    $hasPassedAssessment = $this->hasPassedAssessment;
    $continueTopic = $filteredTopics->firstWhere('progress_status', 'in_progress')
        ?? $filteredTopics->firstWhere('progress_status', 'available')
        ?? $filteredTopics->first();
    $topicOffset = ($paginatedTopics->currentPage() - 1) * $paginatedTopics->perPage();
    $mentorTabs = [
        'overview' => 'Ringkasan',
        'topics' => 'Sesi',
        'students' => 'Murid',
    ];
    $studentTabs = [
        'overview' => 'Ringkasan',
        'topics' => 'Sesi',
    ];
    $studentVisibleTabs = $enrolled
        ? $studentTabs
        : ['overview' => $studentTabs['overview']];
    $mentorTopicsToRender = $paginatedTopics;
    $mentorStudentsCount = $mentorStudents->count();
    $mentorMaterialsCount = $mentoredTopics->sum(fn ($topic) => $topic->materials_count ?? $topic->materials->count());
    $mentorSessionsCount = $mentoredTopics->sum(fn ($topic) => $topic->sessions_count ?? $topic->videoSessions->count());
    $selectedMentorTopic = $mentoredTopics->firstWhere('id', $this->selectedMentorTopicId) ?? $mentoredTopics->first();
    $selectedMentorMaterials = $selectedMentorTopic?->materials->sortBy('sort_order')->values() ?? collect();
    $selectedMentorMaterial = $selectedMentorMaterials->firstWhere('id', $this->selectedMentorMaterialId) ?? $selectedMentorMaterials->first();
    $selectedMentorMaterialPreviewUrl = app(\App\Services\Materials\MaterialAssetService::class)->resolvePreviewUrl($selectedMentorMaterial);
    $selectedMentorSession = $selectedMentorTopic?->videoSessions->firstWhere('id', $this->selectedMentorSessionId)
        ?? $selectedMentorTopic?->videoSessions->sortByDesc('start_at')->first();
    $selectedMentorSessionScheduleLabel = $selectedMentorSession?->start_at && $selectedMentorSession?->end_at
        ? 'Pertemuan: ' . $selectedMentorSession->start_at->format('H:i') . '-' . $selectedMentorSession->end_at->format('H:i') . ' ' . $selectedMentorSession->start_at->format('d M Y')
        : null;
    $showMentorZoomButton = filled($selectedMentorSession?->zoom_link)
        && (!$selectedMentorSession?->end_at || now()->lte($selectedMentorSession->end_at));
    $studentTopicsToRender = $paginatedTopics;
    $selectedStudentTopic = $studentTopicsToRender->firstWhere('id', $this->selectedStudentTopicId)
        ?? $studentTopics->firstWhere('id', $this->selectedStudentTopicId)
        ?? $studentTopicsToRender->first()
        ?? $studentTopics->first();
    $selectedStudentMaterials = $selectedStudentTopic?->materials->sortBy('sort_order')->values() ?? collect();
    $selectedStudentMaterial = $selectedStudentMaterials->firstWhere('id', $this->selectedStudentMaterialId) ?? $selectedStudentMaterials->first();
    $selectedStudentMaterialPreviewUrl = app(\App\Services\Materials\MaterialAssetService::class)->resolvePreviewUrl($selectedStudentMaterial);
    $selectedStudentMaterialProgress = $selectedStudentMaterial ? ($this->materialProgressMap[$selectedStudentMaterial->id] ?? 'not_started') : 'not_started';
    $selectedStudentYoutubeId = $selectedStudentMaterial && $selectedStudentMaterial->type === 'video'
        ? app(\App\Services\Materials\YoutubeService::class)->extractVideoId((string) $selectedStudentMaterial->external_url)
        : null;
    $isSelectedStudentTrackableVideo = $selectedStudentMaterial?->type === 'video' && filled($selectedStudentYoutubeId);
    $selectedStudentVideoUnlocked = $selectedStudentMaterial
        ? ($this->videoCompletionUnlocked[$selectedStudentMaterial->id] ?? false)
        : false;
    $selectedStudentSessions = $selectedStudentTopic?->videoSessions->filter(fn ($session) => $session->status !== 'draft')->values() ?? collect();
    $selectedStudentSession = $selectedStudentSessions->firstWhere('id', $this->selectedStudentSessionId)
        ?? $selectedStudentSessions->sortByDesc('start_at')->first();
    $selectedStudentSessionScheduleLabel = $selectedStudentSession?->start_at && $selectedStudentSession?->end_at
        ? 'Pertemuan: ' . $selectedStudentSession->start_at->format('H:i') . ' - ' . $selectedStudentSession->end_at->format('H:i') . ' ' . $selectedStudentSession->start_at->format('d M Y')
        : null;
    $selectedStudentSessionEndLabel = $selectedStudentSession?->end_at?->format('d M Y H:i');
    $isStudentMaterialLocked = !$selectedStudentSession?->end_at || now()->lt($selectedStudentSession->end_at);
    $showStudentZoomButton = filled($selectedStudentSession?->zoom_link)
        && (!$selectedStudentSession?->end_at || now()->lte($selectedStudentSession->end_at));

    $poster = $course->poster ?? $course->image ?? null;
    $posterSrc = null;

    if ($poster) {
        if (\Illuminate\Support\Str::startsWith($poster, ['http://', 'https://'])) {
            $posterSrc = $poster;
        } elseif ($thumbnailUrl = course_thumbnail_url($poster)) {
            $posterSrc = $thumbnailUrl;
        }
    }
@endphp

<div class="min-h-screen bg-white px-4 pb-16 pt-8 sm:px-6 sm:pb-24 sm:pt-12 lg:px-8">
    <div class="mx-auto max-w-6xl space-y-12">
        @if(session('error'))
            <div class="rounded-2xl border border-rose-200 bg-rose-50 px-5 py-4 text-sm text-rose-700">
                {{ session('error') }}
            </div>
        @endif

        @if($isMentor)
            <section class="overflow-hidden rounded-3xl border border-[color:color-mix(in_oklab,#004777_12%,white)] bg-white shadow-[0_20px_50px_color-mix(in_oklab,#004777_8%,transparent)]">
                <div class="relative overflow-hidden bg-gradient-to-br from-[var(--mentor-primary)] to-[#0a659b] px-5 py-6 text-white sm:px-7">
                    <div class="relative flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
                        <div class="space-y-2">
                            <h1 class="text-2xl font-bold tracking-tight text-white sm:text-3xl">
                                {{ $course->title }}
                            </h1>
                            <p class="max-w-3xl text-sm leading-6 text-white/75">
                                {{ $course->description ?: __('general.course_catalog.defaults.no_description') }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-3 px-5 py-5 text-sm sm:grid-cols-3 sm:px-7">
                    <div class="mentor-workspace-card p-4">
                        <div class="text-xs font-semibold uppercase tracking-wide text-[color:color-mix(in_oklab,#004777_55%,white)]">Sesi</div>
                        <div class="mt-1 font-semibold text-mentor-primary">{{ $mentoredTopics->count() }}</div>
                    </div>
                    <div class="mentor-workspace-card p-4">
                        <div class="text-xs font-semibold uppercase tracking-wide text-[color:color-mix(in_oklab,#004777_55%,white)]">Materi</div>
                        <div class="mt-1 font-semibold text-mentor-primary">{{ $mentorMaterialsCount }}</div>
                    </div>
                    <div class="mentor-workspace-card p-4">
                        <div class="text-xs font-semibold uppercase tracking-wide text-[color:color-mix(in_oklab,#004777_55%,white)]">Murid</div>
                        <div class="mt-1 font-semibold text-mentor-primary">{{ $mentorStudentsCount }}</div>
                    </div>
                </div>

                <div class="border-t border-slate-100 px-3 pt-3 sm:px-5">
                    <div class="flex gap-2 overflow-x-auto">
                        @foreach($mentorTabs as $key => $label)
                            <button type="button"
                                    wire:click="setTopicTab('{{ $key }}')"
                                    class="mentor-tab-button whitespace-nowrap {{ $activeTab === $key ? 'mentor-tab-button-active' : '' }}">
                                {{ $label }}
                            </button>
                        @endforeach
                    </div>
                </div>
            </section>
        @elseif($isStudent)
            <section class="overflow-hidden rounded-3xl border border-[color:color-mix(in_oklab,#004777_12%,white)] bg-white shadow-[0_20px_50px_color-mix(in_oklab,#004777_8%,transparent)]">
                <div class="relative overflow-hidden bg-gradient-to-br from-[#004777] to-[#0a659b] px-5 py-6 text-white sm:px-7">
                    <div class="relative flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
                        <div class="flex flex-col gap-5 lg:flex-row lg:items-start">
                            <div class="w-full max-w-[220px] overflow-hidden rounded-2xl p-2 backdrop-blur sm:max-w-[240px]">
                                <img
                                    src="{{ $posterSrc ?? asset('images/thumbnail/thumbnail_candle.png') }}"
                                    alt="{{ $course->title }}"
                                    class="aspect-[4/3] w-full rounded-xl object-cover"
                                >
                            </div>

                            <div class="space-y-2">
                                <h1 class="text-2xl font-bold tracking-tight text-white sm:text-3xl">
                                    {{ $course->title }}
                                </h1>
                                <p class="max-w-3xl text-sm leading-6 text-white/75">
                                    {{ $course->description ?: __('general.course_catalog.defaults.no_description') }}
                                </p>

                                @if(!$enrolled)
                                    <div class="pt-2">
                                        <button
                                            type="button"
                                            wire:click="confirmEnroll"
                                            class="inline-flex items-center justify-center rounded-xl bg-white px-5 py-3 text-sm font-semibold text-[#004777] transition hover:bg-slate-100"
                                        >
                                            Daftar sekarang
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="flex gap-3"></div>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-3 px-5 py-5 text-sm sm:grid-cols-2 xl:grid-cols-4 sm:px-7">
                    <div class="mentor-workspace-card p-4">
                        <div class="text-xs font-semibold uppercase tracking-wide text-[color:color-mix(in_oklab,#004777_55%,white)]">Progres</div>
                        <div class="mt-1 font-semibold text-mentor-primary">{{ $progressPercent }}%</div>
                    </div>
                    <div class="mentor-workspace-card p-4">
                        <div class="text-xs font-semibold uppercase tracking-wide text-[color:color-mix(in_oklab,#004777_55%,white)]">Sesi</div>
                        <div class="mt-1 font-semibold text-mentor-primary">{{ $completedTopicsCount }}/{{ $totalTopicsCount }}</div>
                    </div>
                    <div class="mentor-workspace-card p-4">
                        <div class="text-xs font-semibold uppercase tracking-wide text-[color:color-mix(in_oklab,#004777_55%,white)]">Assessment</div>
                        <div class="mt-1 font-semibold text-mentor-primary">
                            @if($assessmentMeta)
                                @if($hasPassedAssessment)
                                    {{ __('general.course_show.assessment_passed_badge') }}
                                @elseif($this->assessmentUnlocked)
                                    <a href="{{ localized_route('assessments.take', $assessment->id) }}" class="admin-primary-button inline-flex rounded-lg px-3 py-1.5 text-xs">
                                        {{ $this->activeAttempt ? __('general.course_show.resume_test') : __('general.course_show.start_test') }}
                                    </a>
                                @else
                                    {{ __('general.course_show.locked') }}
                                @endif
                            @else
                                {{ __('general.course_show.not_published') }}
                            @endif
                        </div>
                    </div>
                    <div class="mentor-workspace-card p-4">
                        <div class="text-xs font-semibold uppercase tracking-wide text-[color:color-mix(in_oklab,#004777_55%,white)]">Sertifikat</div>
                        <div class="mt-1 font-semibold text-mentor-primary">
                            @if($courseCertificate && $enrolled)
                                <a
                                    href="{{ localized_route('certificates.download', $courseCertificate->id) }}"
                                    class="inline-flex rounded-lg bg-emerald-50 px-3 py-1.5 text-xs font-medium text-emerald-700 transition hover:bg-emerald-100"
                                >
                                    {{ __('general.course_show.download_certificate') }}
                                </a>
                            @elseif($certificateEligibility['eligible'])
                                <a href="{{ localized_route('certificates.course.claim', $course->id) }}" class="admin-primary-button inline-flex rounded-lg px-3 py-1.5 text-xs">
                                    {{ __('general.course_show.claim_certificate') }}
                                </a>
                            @else
                                {{ __('general.course_show.locked') }}
                            @endif
                        </div>
                    </div>
                </div>

                <div class="border-t border-slate-100 px-3 pt-3 sm:px-5">
                    <div class="flex gap-2 overflow-x-auto">
                        @foreach($studentVisibleTabs as $key => $label)
                            <button type="button"
                                    wire:click="setTopicTab('{{ $key }}')"
                                    class="mentor-tab-button whitespace-nowrap {{ $activeTab === $key ? 'mentor-tab-button-active' : '' }}">
                                {{ $label }}
                            </button>
                        @endforeach
                    </div>
                </div>
            </section>
        @else
            <section>
                <div class="relative isolate overflow-hidden rounded-[2rem] bg-[#eef8ff] p-6 sm:p-10 lg:p-12">
                    <div class="pointer-events-none absolute -left-24 -top-24 -z-10 h-72 w-72 rounded-full bg-[#7DD3FC]/45 blur-3xl" aria-hidden="true"></div>
                    <div class="pointer-events-none absolute -bottom-28 right-10 -z-10 h-80 w-80 rounded-full bg-violet-300/25 blur-3xl" aria-hidden="true"></div>
                    <div class="space-y-8">
                        <a
                            href="{{ localized_route('courses.index') }}"
                            class="inline-flex items-center gap-2 text-sm font-medium text-[#004777] transition hover:text-[#35A7FF]"
                        >
                            <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M17 10a.75.75 0 0 1-.75.75H5.56l3.22 3.22a.75.75 0 1 1-1.06 1.06l-4.5-4.5a.75.75 0 0 1 0-1.06l4.5-4.5a.75.75 0 0 1 1.06 1.06L5.56 9.25h10.69A.75.75 0 0 1 17 10Z" clip-rule="evenodd" />
                            </svg>
                            <span>{{ __('general.assessment_result.actions.back_to_course') }}</span>
                        </a>

                        <div class="grid items-center gap-8 lg:grid-cols-[0.9fr_1.1fr]">
                            <div class="relative aspect-[16/11] w-full overflow-hidden rounded-2xl bg-white p-2">
                                <img
                                    src="{{ $posterSrc ?? asset('images/thumbnail/thumbnail_candle.png') }}"
                                    alt="{{ $course->title }}"
                                    class="h-full w-full rounded-xl object-cover"
                                >
                            </div>

                            <div class="min-w-0 flex-1">
                                <h1 class="mt-2 text-3xl font-bold leading-tight tracking-tight text-[#004777] sm:text-5xl">
                                    {{ $course->title }}
                                </h1>

                                <p class="mt-4 max-w-3xl text-sm leading-7 text-slate-600 sm:text-base">
                                    {{ $course->description ?: __('general.course_catalog.defaults.no_description') }}
                                </p>

                                <div class="mt-4 flex flex-wrap items-center gap-2">
                                    @if(!auth()->check())
                                        <a
                                            href="{{ localized_route('login') }}"
                                            class="inline-flex items-center justify-center rounded-xl bg-[#004777] px-5 py-3 text-sm font-semibold text-white transition hover:bg-[#00395f]"
                                        >
                                            {{ __('general.course_show.login_to_track') }}
                                        </a>
                                    @else
                                        @if($enrolled)
                                        @else
                                            <button
                                                wire:click="confirmEnroll"
                                                class="inline-flex items-center justify-center rounded-xl bg-[#004777] px-5 py-3 text-sm font-semibold text-white transition hover:bg-[#00395f]"
                                            >
                                                {{ __('general.course_show.enroll') }}
                                            </button>
                                        @endif
                                    @endif
                                </div>

                                @guest
                                    <div class="mt-3 max-w-3xl text-xs leading-6 text-slate-500 sm:text-sm">
                                        {{ __('general.course_show.guest_notice') }}
                                    </div>
                                @endguest
                            </div>
                        </div>

                    </div>
                </div>
            </section>
        @endif

        @if($isStudent)
            @if($activeTab === 'overview')
                <section class="space-y-5">
                    <div class="mentor-workspace-panel">
                        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                            <div>
                                <h2 class="mentor-workspace-heading">{{ __('general.course_show.course_access') }}</h2>
                                <p class="mentor-workspace-subheading">
                                    {{ __('general.course_show.completed_count', ['count' => $completedTopicsCount]) }} / {{ trans_choice('general.course_show.topics_count', $totalTopicsCount, ['count' => $totalTopicsCount]) }}
                                </p>
                            </div>

                            <div class="text-sm font-semibold text-[var(--mentor-primary)]">{{ $progressPercent }}%</div>
                        </div>

                        <div class="mt-4 h-2.5 overflow-hidden rounded-full bg-[#35A7FF]/15">
                            <div class="h-full rounded-full bg-[#004777] transition-all" style="width: {{ $progressPercent }}%"></div>
                        </div>
                    </div>
                </section>
            @elseif($activeTab === 'topics')
                <section class="mentor-workspace-panel">
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="mentor-workspace-heading">{{ __('general.course_show.course_topics') }}</h2>
                            <p class="mentor-workspace-subheading">Pilih sesi dan lihat preview materi pembelajaran pada topik pembelajaran ini.</p>
                        </div>
                    </div>

                    <div class="mt-6 flex flex-col gap-3 lg:flex-row lg:items-start lg:justify-between">
                        <div class="flex gap-3 overflow-x-auto pb-1">
                            @foreach($studentTopicsToRender as $topic)
                                <button type="button"
                                        wire:click="selectStudentTopic('{{ $topic->id }}')"
                                        class="shrink-0 rounded-2xl border px-4 py-3 text-left transition {{ (string) ($selectedStudentTopic?->id) === (string) $topic->id ? 'border-[var(--mentor-primary)] bg-[var(--mentor-primary)] text-white shadow-md' : 'border-slate-200 bg-white text-[var(--mentor-primary)] hover:border-[var(--mentor-primary)]' }}">
                                    <div class="text-sm font-semibold">{{ $topic->name }}</div>
                                </button>
                            @endforeach
                        </div>

                        @if($this->upcomingTopicsCount > 0)
                            <div class="shrink-0 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-600">
                                {{ $this->upcomingTopicsCount }} sesi lagi akan datang.
                            </div>
                        @endif
                    </div>

                    @if($paginatedTopics->hasPages())
                        <div class="mt-4">
                            {{ $paginatedTopics->links() }}
                        </div>
                    @endif

                    @if($selectedStudentTopic)
                        <div class="mt-6 grid gap-4 xl:grid-cols-[1.25fr_0.75fr]">
                            <div class="mentor-workspace-card p-5">
                                <div
                                    class="flex flex-col gap-4"
                                    @if($selectedStudentMaterial && !$isStudentMaterialLocked && $isSelectedStudentTrackableVideo && $selectedStudentMaterialProgress !== 'completed')
                                        x-data="courseShowVideoProgressTracker({
                                            materialId: @js((string) $selectedStudentMaterial->id),
                                            youtubeId: @js($selectedStudentYoutubeId),
                                            requiredPercent: 70,
                                            initiallyUnlocked: @js($selectedStudentVideoUnlocked),
                                        })"
                                        x-init="init()"
                                    @endif
                                >
                                    <div>
                                        <h3 class="text-lg font-semibold text-[var(--mentor-primary)]">{{ $selectedStudentTopic->name }}</h3>
                                        <p class="mt-1 text-sm leading-6 text-[color:color-mix(in_oklab,#004777_70%,white)]">
                                            {{ $selectedStudentTopic->description ?: __('general.course_show.topic_description_fallback') }}
                                        </p>
                                    </div>

                                    @if($selectedStudentMaterial)
                                        <div class="space-y-4">
                                            <div class="flex flex-col gap-3 rounded-2xl border border-slate-200 bg-white p-4 sm:flex-row sm:items-center sm:justify-between">
                                                <div>
                                                    <div class="text-base font-semibold text-[var(--mentor-primary)]">{{ $selectedStudentMaterial->name }}</div>
                                                    <div class="mt-1 text-xs uppercase tracking-wide text-[color:color-mix(in_oklab,#004777_55%,white)]">
                                                        {{ strtoupper($selectedStudentMaterial->type) }}
                                                        ·
                                                        {{ $selectedStudentMaterialProgress === 'completed' ? 'Selesai' : ($selectedStudentMaterialProgress === 'in_progress' ? 'Sedang dipelajari' : 'Belum selesai') }}
                                                    </div>
                                                </div>

                                                @if(!$isStudentMaterialLocked)
                                                    @if($selectedStudentMaterialProgress === 'completed')
                                                        <span class="inline-flex items-center gap-2 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-2 text-sm font-semibold text-emerald-700">
                                                            <span>✓</span>
                                                            Material selesai
                                                        </span>
                                                    @else
                                                        <button
                                                            type="button"
                                                            wire:click="markStudentMaterialComplete('{{ $selectedStudentMaterial->id }}')"
                                                            wire:loading.attr="disabled"
                                                            wire:target="markStudentMaterialComplete('{{ $selectedStudentMaterial->id }}')"
                                                            @if($isSelectedStudentTrackableVideo)
                                                                x-bind:disabled="!isUnlocked"
                                                                x-bind:class="!isUnlocked ? 'cursor-not-allowed opacity-50' : ''"
                                                            @endif
                                                            class="inline-flex items-center justify-center gap-2 rounded-xl bg-[#004777] px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-[#00395f] disabled:opacity-70"
                                                        >
                                                            <span>✓</span>
                                                            Tandai selesai
                                                        </button>
                                                    @endif
                                                @endif
                                            </div>

                                            @if($isStudentMaterialLocked)
                                                <div class="rounded-2xl border border-dashed border-amber-200 bg-amber-50 px-4 py-10 text-center">
                                                    <div class="text-sm font-semibold text-amber-700">Materi masih terkunci</div>
                                                    <div class="mt-2 text-sm text-amber-700/90">
                                                        Materi bisa dibuka setelah pertemuan selesai{{ $selectedStudentSessionEndLabel ? ' pada ' . $selectedStudentSessionEndLabel : '' }}.
                                                    </div>
                                                </div>
                                            @elseif($selectedStudentMaterial->type === 'video' && $selectedStudentMaterialPreviewUrl)
                                                @if($isSelectedStudentTrackableVideo && $selectedStudentMaterialProgress !== 'completed')
                                                    <div class="rounded-2xl border border-[#35A7FF]/20 bg-[#eef8ff] p-4 text-sm text-[#004777]">
                                                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                                            <div>
                                                                <div class="font-semibold">Tonton minimal 70% video untuk mengaktifkan tombol selesai.</div>
                                                                <div class="mt-1 text-xs text-slate-600" x-show="!isUnlocked">
                                                                    Sisa waktu tonton:
                                                                    <span class="font-semibold text-[#004777]" x-text="formattedRemaining"></span>
                                                                </div>
                                                                <div class="mt-1 text-xs text-emerald-700" x-show="isUnlocked">
                                                                    Durasi tonton sudah memenuhi syarat. Material sekarang bisa diselesaikan.
                                                                </div>
                                                            </div>
                                                            <div class="shrink-0 rounded-full border border-[#35A7FF]/20 bg-white px-3 py-1 text-xs font-semibold text-[#004777]">
                                                                <span x-text="progressLabel"></span>
                                                            </div>
                                                        </div>
                                                        <div class="mt-3 h-2 overflow-hidden rounded-full bg-white">
                                                            <div class="h-full rounded-full bg-[#35A7FF] transition-all duration-500" x-bind:style="`width: ${progressPercent}%`"></div>
                                                        </div>
                                                    </div>
                                                @endif

                                                <div class="overflow-hidden rounded-2xl border border-slate-200 bg-slate-950">
                                                    <iframe
                                                        id="course-show-video-player-{{ $selectedStudentMaterial->id }}"
                                                        src="{{ $selectedStudentMaterialPreviewUrl }}"
                                                        class="aspect-video w-full"
                                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                                        referrerpolicy="strict-origin-when-cross-origin"
                                                        allowfullscreen
                                                    ></iframe>
                                                </div>
                                            @elseif($selectedStudentMaterialPreviewUrl)
                                                <iframe
                                                    src="{{ $selectedStudentMaterialPreviewUrl }}"
                                                    class="min-h-[24rem] w-full rounded-2xl border border-slate-200 bg-white"
                                                ></iframe>
                                            @else
                                                <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 px-4 py-10 text-center text-sm text-[color:color-mix(in_oklab,#004777_70%,white)]">
                                                    Preview materi belum tersedia.
                                                </div>
                                            @endif
                                        </div>
                                    @else
                                        <div class="mt-4 rounded-2xl border border-dashed border-slate-300 bg-slate-50 px-4 py-10 text-center text-sm text-[color:color-mix(in_oklab,#004777_70%,white)]">
                                                    Sesi ini belum punya materi.
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <aside class="mentor-workspace-card p-5">
                                <div class="text-xs font-semibold uppercase tracking-wide text-[color:color-mix(in_oklab,#004777_55%,white)]">Material</div>
                                <div class="mt-4 rounded-xl bg-[var(--mentor-primary-soft-2)] text-sm text-[var(--mentor-primary)]">
                                    {{ $selectedStudentSessionScheduleLabel ?? 'Jadwal pertemuan belum tersedia' }}
                                </div>
                                @if($selectedStudentSession)
                                    <div class="mt-4">
                                        <livewire:sessions.join-session-button
                                            :video-session-id="$selectedStudentSession->id"
                                            :key="'course-show-session-' . $selectedStudentSession->id"
                                        />
                                    </div>
                                @endif

                                <div class="mt-6">
                                    <div class="text-xs font-semibold uppercase tracking-wide text-[color:color-mix(in_oklab,#004777_55%,white)]">List Materi ({{ $selectedStudentTopic->materials_count ?? $selectedStudentTopic->materials->count() }})</div>
                                    <div class="mt-3 space-y-2">
                                        @forelse($selectedStudentMaterials as $material)
                                            @php
                                                $materialProgressStatus = $this->materialProgressMap[$material->id] ?? 'not_started';
                                            @endphp
                                            <button type="button"
                                                    wire:key="student-material-{{ $material->id }}"
                                                    @if(!$isStudentMaterialLocked) wire:click="selectStudentMaterial('{{ $material->id }}')" @endif
                                                    class="w-full rounded-xl border p-4 text-left transition {{ (string) ($selectedStudentMaterial?->id) === (string) $material->id ? 'border-[var(--mentor-primary)] bg-[var(--mentor-primary)] text-white shadow-md' : 'border-slate-200 bg-[var(--mentor-primary-soft-2)] text-[var(--mentor-primary)] hover:border-[var(--mentor-primary)]' }} {{ $isStudentMaterialLocked ? 'cursor-not-allowed opacity-75' : '' }}">
                                                <div class="truncate text-sm font-medium">
                                                    #{{ $material->sort_order }} · {{ $material->name }}
                                                </div>
                                                <div class="mt-1 text-xs {{ (string) ($selectedStudentMaterial?->id) === (string) $material->id ? 'text-white/75' : '' }}">
                                                    {{ strtoupper($material->type) }} · {{ ucfirst($material->status) }}{{ $isStudentMaterialLocked ? ' · Terkunci' : '' }}
                                                </div>
                                                <div class="mt-2 text-[11px] font-semibold {{ (string) ($selectedStudentMaterial?->id) === (string) $material->id ? 'text-white' : 'text-emerald-700' }}">
                                                    {{ $materialProgressStatus === 'completed' ? '✓ Selesai' : ($materialProgressStatus === 'in_progress' ? 'Sedang dipelajari' : 'Belum selesai') }}
                                                </div>
                                            </button>
                                        @empty
                                            <div class="mentor-workspace-empty min-h-0">
                                                No materials
                                            </div>
                                        @endforelse
                                    </div>
                                </div>
                            </aside>
                        </div>
                    @endif
                </section>
            @endif
        @elseif($isMentor)
            @if($activeTab === 'overview')
                <section class="space-y-5">
                    <div class="mentor-workspace-panel">
                        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                            <div>
                                <h2 class="mentor-workspace-heading">Ringkasan Topik pembelajaran</h2>
                                <p class="mentor-workspace-subheading">
                                    {{ $course->description ?: __('general.course_catalog.defaults.no_description') }}
                                </p>
                            </div>

                            <span class="inline-flex items-center rounded-full border border-slate-200 bg-white px-3 py-1 text-xs font-medium uppercase tracking-wide text-[var(--mentor-primary)]">
                                Hanya baca
                            </span>
                        </div>

                        <div class="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                            <div class="mentor-workspace-card p-4">
                                <div class="text-xs font-medium uppercase tracking-wide text-[color:color-mix(in_oklab,#004777_50%,white)]">Status</div>
                                <div class="mt-2 text-sm font-semibold text-[var(--mentor-primary)]">{{ ucfirst($course->status) }}</div>
                            </div>
                            <div class="mentor-workspace-card p-4">
                                <div class="text-xs font-medium uppercase tracking-wide text-[color:color-mix(in_oklab,#004777_50%,white)]">Sesi</div>
                                <div class="mt-2 text-sm font-semibold text-[var(--mentor-primary)]">{{ $mentoredTopics->count() }}</div>
                            </div>
                            <div class="mentor-workspace-card p-4">
                                <div class="text-xs font-medium uppercase tracking-wide text-[color:color-mix(in_oklab,#004777_50%,white)]">Pertemuan</div>
                                <div class="mt-2 text-sm font-semibold text-[var(--mentor-primary)]">{{ $mentorSessionsCount }}</div>
                            </div>
                            <div class="mentor-workspace-card p-4">
                                <div class="text-xs font-medium uppercase tracking-wide text-[color:color-mix(in_oklab,#004777_50%,white)]">Murid</div>
                                <div class="mt-2 text-sm font-semibold text-[var(--mentor-primary)]">{{ $mentorStudentsCount }}</div>
                            </div>
                        </div>
                    </div>
                </section>
            @elseif($activeTab === 'topics')
                <section class="mentor-workspace-panel">
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="mentor-workspace-heading">Sesi</h2>
                            <p class="mentor-workspace-subheading">Pilih sesi dan lihat preview materi dengan akses cepat ke pengelolaan sesi.</p>
                        </div>
                    </div>

                    @if($hasMentoredTopics)
                        <div class="mt-6 flex gap-3 overflow-x-auto pb-1">
                            @foreach($mentorTopicsToRender as $topic)
                                <button type="button"
                                        wire:click="selectMentorTopic('{{ $topic->id }}')"
                                        class="shrink-0 rounded-2xl border px-4 py-3 text-left transition {{ (string) ($selectedMentorTopic?->id) === (string) $topic->id ? 'border-[var(--mentor-primary)] bg-[var(--mentor-primary)] text-white shadow-md' : 'border-slate-200 bg-white text-[var(--mentor-primary)] hover:border-[var(--mentor-primary)]' }}">
                                    <div class="text-sm font-semibold">{{ $topic->name }}</div>
                                </button>
                            @endforeach
                        </div>

                        @if($paginatedTopics->hasPages())
                            <div class="mt-4">
                                {{ $paginatedTopics->links() }}
                            </div>
                        @endif

                        @if($selectedMentorTopic)
                            <div class="mt-6 grid gap-4 xl:grid-cols-[1.25fr_0.75fr]">
                                <div class="mentor-workspace-card p-5">
                                    <div class="flex flex-col gap-4">
                                    <div>
                                        <h3 class="text-lg font-semibold text-[var(--mentor-primary)]">{{ $selectedMentorTopic->name }}</h3>
                                        <p class="mt-1 text-sm leading-6 text-[color:color-mix(in_oklab,#004777_70%,white)]">
                                            {{ $selectedMentorTopic->description ?: __('general.course_show.topic_description_fallback') }}
                                        </p>
                                    </div>

                                    <div class="rounded-xl bg-[var(--mentor-primary-soft-2)] px-4 py-3 text-sm text-[var(--mentor-primary)]">
                                        {{ $selectedMentorSessionScheduleLabel ?? 'Jadwal pertemuan belum tersedia' }}
                                    </div>

                                    @if($selectedMentorMaterial)
                                        <div class="space-y-4">
                                                @if($selectedMentorMaterial->type === 'video' && $selectedMentorMaterialPreviewUrl)
                                                    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-slate-950">
                                                        <iframe
                                                            src="{{ $selectedMentorMaterialPreviewUrl }}"
                                                            class="aspect-video w-full"
                                                            allowfullscreen
                                                        ></iframe>
                                                    </div>
                                                @elseif($selectedMentorMaterialPreviewUrl)
                                                    <iframe
                                                        src="{{ $selectedMentorMaterialPreviewUrl }}"
                                                        class="min-h-[24rem] w-full rounded-2xl border border-slate-200 bg-white"
                                                    ></iframe>
                                                @else
                                                    <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 px-4 py-10 text-center text-sm text-[color:color-mix(in_oklab,#004777_70%,white)]">
                                                        Preview materi belum tersedia.
                                                    </div>
                                                @endif
                                            </div>
                                        @else
                                            <div class="mt-4 rounded-2xl border border-dashed border-slate-300 bg-slate-50 px-4 py-10 text-center text-sm text-[color:color-mix(in_oklab,#004777_70%,white)]">
                                                Sesi ini belum punya materi.
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <aside class="mentor-workspace-card p-5">
                                    <div class="text-xs font-semibold uppercase tracking-wide text-[color:color-mix(in_oklab,#004777_55%,white)]">Material</div>
                                    <div class="mt-4 flex flex-col gap-3">
                                        <a href="{{ localized_route('mentor.topics.show', $selectedMentorTopic->slug) }}"
                                           class="admin-primary-button inline-flex items-center justify-center rounded-xl px-4 py-3 text-sm">
                                            Kelola
                                        </a>
                                        @if($assessment && (string) $assessment->teacher_id === (string) auth()->id())
                                            <a href="{{ localized_route('mentor.assessments.index', ['courseFilter' => $course->id]) }}"
                                               class="admin-neutral-button inline-flex items-center justify-center rounded-xl px-4 py-3 text-sm">
                                                Kelola Assessment
                                            </a>
                                        @endif
                                        @if($showMentorZoomButton)
                                            <a href="{{ $selectedMentorSession->zoom_link }}"
                                               target="_blank"
                                               rel="noopener noreferrer"
                                               class="admin-neutral-button inline-flex items-center justify-center rounded-xl px-4 py-3 text-sm">
                                                Buka Zoom
                                            </a>
                                        @endif
                                    </div>

                                    <div class="mt-6">
                                        <div class="text-xs font-semibold uppercase tracking-wide text-[color:color-mix(in_oklab,#004777_55%,white)]">List Materi ({{ $topic->materials_count ?? $topic->materials->count() }})</div>
                                        <div class="mt-3 space-y-2">
                                            @forelse($selectedMentorMaterials as $material)
                                                <button type="button"
                                                        wire:key="mentor-material-{{ $material->id }}"
                                                        wire:click="selectMentorMaterial('{{ $material->id }}')"
                                                        class="w-full rounded-xl border p-4 text-left transition {{ (string) ($selectedMentorMaterial?->id) === (string) $material->id ? 'border-[var(--mentor-primary)] bg-[var(--mentor-primary)] text-white shadow-md' : 'border-slate-200 bg-[var(--mentor-primary-soft-2)] text-[var(--mentor-primary)] hover:border-[var(--mentor-primary)]' }}">
                                                    <div class="truncate text-sm font-medium">
                                                        #{{ $material->sort_order }} · {{ $material->name }}
                                                    </div>
                                                    <div class="mt-1 text-xs {{ (string) ($selectedMentorMaterial?->id) === (string) $material->id ? 'text-white/75' : '' }}">
                                                        {{ strtoupper($material->type) }} · {{ ucfirst($material->status) }}
                                                    </div>
                                                </button>
                                            @empty
                                                <div class="mentor-workspace-empty min-h-0">
                                                    No materials
                                                </div>
                                            @endforelse
                                        </div>
                                    </div>
                                </aside>
                            </div>
                        @endif
                    @else
                        <div class="mentor-workspace-empty mt-6">
                            Belum ada topic yang terhubung ke mentor ini.
                        </div>
                    @endif
                </section>
            @elseif($activeTab === 'students')
                <section class="mentor-workspace-panel">
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="mentor-workspace-heading">Murid</h2>
                            <p class="mentor-workspace-subheading">Daftar peserta topik pembelajaran dalam tampilan hanya baca.</p>
                        </div>
                    </div>

                    <div class="mentor-workspace-table mt-6">
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead class="bg-[var(--mentor-primary)]">
                                    <tr class="text-left">
                                        <th class="px-5 py-4 font-bold text-white">Murid</th>
                                        <th class="px-5 py-4 font-bold text-white">Email</th>
                                        <th class="px-5 py-4 font-bold text-white">Progres</th>
                                        <th class="px-5 py-4 font-bold text-white">Enrolled</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 bg-white">
                                    @forelse($mentorStudents as $student)
                                        @php
                                            $completedTopicCount = $student->topicProgresses
                                                ->where('status', 'completed')
                                                ->count();
                                            $studentProgressPercent = $totalTopicsCount > 0
                                                ? (int) round(($completedTopicCount / $totalTopicsCount) * 100)
                                                : 0;
                                        @endphp
                                        <tr class="transition hover:bg-[var(--mentor-primary-soft-2)]">
                                            <td class="px-5 py-4 font-medium text-[var(--mentor-primary)]">{{ $student->user?->name ?? '-' }}</td>
                                            <td class="px-5 py-4 text-[color:color-mix(in_oklab,#004777_70%,white)]">{{ $student->user?->email ?? '-' }}</td>
                                            <td class="px-5 py-4">
                                                <div class="w-full min-w-40 max-w-xs">
                                                    <div class="h-2 overflow-hidden rounded-full bg-[var(--mentor-primary-soft)]">
                                                        <div
                                                            class="h-full rounded-full bg-[var(--mentor-primary)] transition-all"
                                                            style="width: {{ $studentProgressPercent }}%"
                                                        ></div>
                                                    </div>
                                                    <div class="mt-2 text-xs font-medium text-[color:color-mix(in_oklab,#004777_70%,white)]">
                                                        {{ $studentProgressPercent }}% · {{ $completedTopicCount }}/{{ $totalTopicsCount }} sesi
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-5 py-4 text-[color:color-mix(in_oklab,#004777_70%,white)]">{{ $student->enrolled_at?->format('d M Y H:i') ?? '-' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="px-6 py-14 text-center">
                                                <div class="text-sm font-medium text-[var(--mentor-primary)]">Belum ada student terdaftar.</div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>
            @endif
        @endif
    </div>

    @if($showAssessmentModal && $assessmentMeta && $isStudent)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/60 p-4">
            <div class="absolute inset-0" wire:click="closeAssessmentModal"></div>

            <div class="relative z-10 max-h-[90vh] w-full max-w-6xl overflow-y-auto rounded-[2rem] border border-slate-200 bg-white">
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
                        <div class="relative overflow-hidden rounded-2xl bg-[#f4f1ff] p-5">
                            <div class="text-xs uppercase tracking-wide text-[#004777]/60">{{ __('general.course_show.assessment_modal.assessment') }}</div>
                            <h4 class="mt-2 text-2xl font-bold text-[#004777]">{{ $assessmentMeta['title'] }}</h4>
                            <p class="mt-2 text-sm text-slate-500">
                                {{ $assessmentMeta['status'] }}
                            </p>
                            <img src="{{ asset('images/decor/assesment.png') }}" alt="" class="pointer-events-none absolute -bottom-16 -right-12 w-40 opacity-25" aria-hidden="true">
                        </div>

                        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                            <div class="rounded-2xl border border-slate-200 p-4">
                                <div class="text-xs text-slate-500">{{ __('general.course_show.assessment_modal.questions') }}</div>
                                <div class="mt-1 font-semibold text-[#004777]">{{ $assessmentMeta['question_count'] }}</div>
                            </div>

                            <div class="rounded-2xl border border-slate-200 p-4">
                                <div class="text-xs text-slate-500">{{ __('general.course_show.assessment_modal.passing_grade') }}</div>
                                <div class="mt-1 font-semibold text-[#004777]">{{ $assessmentMeta['passing_grade'] }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-2xl bg-[#eef8ff] p-6">
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

    @if($showEnrollModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/60 p-4">
            <button type="button" class="absolute inset-0" wire:click="closeEnrollModal" aria-label="Tutup modal konfirmasi"></button>

            <div class="relative z-10 w-full max-w-md rounded-[1rem] border border-slate-200 bg-white p-6 shadow-2xl">
                <div class="space-y-3">
                    <div>
                        <h3 class="text-xl font-bold text-[#004777]">Konfirmasi pendaftaran topik pembelajaran</h3>
                        <p class="mt-2 text-sm leading-6 text-slate-600">
                            Kamu yakin ingin mendaftar ke topik pembelajaran
                            <span class="font-semibold text-[#004777]">{{ $course->title }}</span>?
                        </p>
                    </div>
                </div>

                <div class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                    <button
                        type="button"
                        wire:click="closeEnrollModal"
                        class="inline-flex items-center justify-center rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50"
                    >
                        Batal
                    </button>

                    <button
                        type="button"
                        wire:click="enroll"
                        wire:loading.attr="disabled"
                        wire:target="enroll"
                        class="inline-flex items-center justify-center rounded-xl bg-[#004777] px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-[#00395f] disabled:cursor-not-allowed disabled:opacity-70"
                    >
                        <span wire:loading.remove wire:target="enroll">Ya, daftar sekarang</span>
                        <span wire:loading.inline-flex wire:target="enroll" class="items-center gap-2">
                            <svg class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-90" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                            </svg>
                            Memproses...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    @if($showTopicAccessWarningModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/60 p-4">
            <button type="button" class="absolute inset-0" wire:click="closeTopicAccessWarning" aria-label="Tutup modal peringatan"></button>

            <div class="relative z-10 w-full max-w-md rounded-[1rem] border border-slate-200 bg-white p-6 shadow-2xl">
                <div class="space-y-3">
                    <div>
                        <h3 class="text-xl font-bold text-[#004777]">Akses sesi dibatasi</h3>
                        <p class="mt-2 text-sm leading-6 text-slate-600">
                            Kamu belum terdaftar di topik pembelajaran ini, jadi sesi
                            <span class="font-semibold text-[#004777]">{{ $topicAccessWarningName }}</span>
                            belum bisa dibuka.
                        </p>
                        <p class="mt-2 text-sm leading-6 text-slate-600">
                            Silakan daftar ke topik pembelajaran ini terlebih dahulu untuk membuka sesi pembelajaran.
                        </p>
                    </div>
                </div>

                <div class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                    <button
                        type="button"
                        wire:click="closeTopicAccessWarning"
                        class="inline-flex items-center justify-center rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50"
                    >
                        Tutup
                    </button>

                    <button
                        type="button"
                        wire:click="confirmEnroll"
                        class="inline-flex items-center justify-center rounded-xl bg-[#004777] px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-[#00395f]"
                    >
                        Daftar Topik pembelajaran
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>

@once
    @push('scripts')
        <script>
            (() => {
                let youtubeApiReadyPromise;

                function loadYoutubeApi() {
                    if (window.YT && window.YT.Player) {
                        return Promise.resolve(window.YT);
                    }

                    if (youtubeApiReadyPromise) {
                        return youtubeApiReadyPromise;
                    }

                    youtubeApiReadyPromise = new Promise((resolve) => {
                        const previousHandler = window.onYouTubeIframeAPIReady;

                        window.onYouTubeIframeAPIReady = () => {
                            previousHandler?.();
                            resolve(window.YT);
                        };

                        const script = document.createElement('script');
                        script.src = 'https://www.youtube.com/iframe_api';
                        script.async = true;
                        document.head.appendChild(script);
                    });

                    return youtubeApiReadyPromise;
                }

                window.courseShowVideoProgressTracker = function courseShowVideoProgressTracker(config) {
                    return {
                        materialId: config.materialId,
                        youtubeId: config.youtubeId,
                        requiredPercent: config.requiredPercent ?? 70,
                        isUnlocked: !!config.initiallyUnlocked,
                        player: null,
                        playerState: -1,
                        timer: null,
                        lastTickAt: null,
                        durationSeconds: 0,
                        watchedSeconds: 0,
                        progressPercent: config.initiallyUnlocked ? 100 : 0,
                        progressLabel: config.initiallyUnlocked ? '70% terpenuhi' : '0% dari target',
                        formattedRemaining: '--:--',

                        async init() {
                            this.updateProgressUi();

                            if (!this.youtubeId || this.isUnlocked) {
                                return;
                            }

                            const yt = await loadYoutubeApi();
                            const elementId = `course-show-video-player-${this.materialId}`;
                            const iframe = document.getElementById(elementId);

                            if (!iframe) {
                                return;
                            }

                            this.player = new yt.Player(elementId, {
                                events: {
                                    onReady: (event) => this.handleReady(event),
                                    onStateChange: (event) => this.handleStateChange(event),
                                },
                            });
                        },

                        handleReady(event) {
                            const duration = Number(event.target.getDuration?.() || 0);

                            if (duration > 0) {
                                this.durationSeconds = duration;
                            }

                            this.updateProgressUi();
                        },

                        handleStateChange(event) {
                            this.playerState = event.data;

                            if (event.data === window.YT.PlayerState.PLAYING) {
                                this.startTimer();
                                return;
                            }

                            this.stopTimer();
                        },

                        startTimer() {
                            if (this.timer || this.isUnlocked) {
                                return;
                            }

                            this.lastTickAt = Date.now();
                            this.timer = window.setInterval(() => this.tick(), 500);
                        },

                        stopTimer() {
                            if (this.timer) {
                                window.clearInterval(this.timer);
                                this.timer = null;
                            }

                            this.lastTickAt = null;
                        },

                        tick() {
                            if (this.isUnlocked || this.playerState !== window.YT.PlayerState.PLAYING) {
                                return;
                            }

                            const now = Date.now();

                            if (this.lastTickAt === null) {
                                this.lastTickAt = now;
                                return;
                            }

                            const elapsedSeconds = Math.max(0, (now - this.lastTickAt) / 1000);
                            this.lastTickAt = now;
                            this.watchedSeconds += elapsedSeconds;
                            this.updateProgressUi();

                            if (this.progressPercent >= 100) {
                                this.finishThreshold();
                            }
                        },

                        updateProgressUi() {
                            const requiredSeconds = this.getRequiredSeconds();
                            const consumed = this.isUnlocked
                                ? requiredSeconds
                                : Math.min(this.watchedSeconds, requiredSeconds);
                            const remaining = Math.max(0, requiredSeconds - consumed);
                            const percent = requiredSeconds > 0
                                ? Math.min(100, Math.round((consumed / requiredSeconds) * 100))
                                : 0;

                            this.progressPercent = percent;
                            this.progressLabel = this.isUnlocked
                                ? '70% terpenuhi'
                                : `${percent}% dari target`;
                            this.formattedRemaining = this.formatDuration(remaining);
                        },

                        getRequiredSeconds() {
                            if (this.durationSeconds <= 0 && this.player?.getDuration) {
                                this.durationSeconds = Number(this.player.getDuration() || 0);
                            }

                            return this.durationSeconds > 0
                                ? this.durationSeconds * (this.requiredPercent / 100)
                                : 0;
                        },

                        async finishThreshold() {
                            if (this.isUnlocked) {
                                return;
                            }

                            this.isUnlocked = true;
                            this.stopTimer();
                            this.updateProgressUi();

                            await this.$wire.unlockStudentVideoCompletion(this.materialId);
                        },

                        formatDuration(totalSeconds) {
                            const safeSeconds = Math.max(0, Math.ceil(totalSeconds));
                            const minutes = Math.floor(safeSeconds / 60);
                            const seconds = safeSeconds % 60;

                            return `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
                        },
                    };
                };
            })();
        </script>
    @endpush
@endonce
