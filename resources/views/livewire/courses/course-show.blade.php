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
                <div class="p-4 sm:p-6 lg:p-7 space-y-5">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                        <div class="min-w-0">
                            <div class="text-[10px] sm:text-xs uppercase tracking-[0.22em] text-slate-400 truncate">
                                {{ $course->studyProgram?->title }}
                            </div>
        
                            <h1 class="mt-2 text-2xl sm:text-3xl font-bold tracking-tight text-slate-900 leading-tight">
                                {{ $course->title }}
                            </h1>
        
                            <p class="mt-3 text-sm leading-6 text-slate-600 line-clamp-4">
                                {{ $course->description }}
                            </p>
                        </div>
        
                        @if($isMentor)
                            <a href="{{ route('mentor.topics.index') }}"
                               class="inline-flex shrink-0 items-center justify-center rounded-xl bg-[#004777] px-4 py-2 text-xs sm:text-sm font-medium text-white hover:bg-[#003560] transition">
                                Manage Topics
                            </a>
                        @endif
                    </div>
        
                    <div class="grid grid-cols-2 gap-2 sm:grid-cols-4">
                        <div class="rounded-xl border bg-slate-50 p-3">
                            <div class="text-[11px] text-slate-500">Topics</div>
                            <div class="mt-1 text-base sm:text-lg font-semibold text-slate-900">
                                {{ $course->topics->count() }}
                            </div>
                        </div>
        
                        <div class="rounded-xl border bg-slate-50 p-3">
                            <div class="text-[11px] text-slate-500">Credit</div>
                            <div class="mt-1 text-base sm:text-lg font-semibold text-slate-900">
                                {{ $course->credit }}
                            </div>
                        </div>
        
                        <div class="rounded-xl border bg-slate-50 p-3">
                            <div class="text-[11px] text-slate-500">Quota</div>
                            <div class="mt-1 text-base sm:text-lg font-semibold text-slate-900">
                                {{ $course->quota }}
                            </div>
                        </div>
        
                        <div class="rounded-xl border bg-slate-50 p-3">
                            <div class="text-[11px] text-slate-500">Status</div>
                            <div class="mt-1 inline-flex rounded-full border px-2.5 py-1 text-[11px] font-medium text-slate-700">
                                {{ ucfirst($course->status) }}
                            </div>
                        </div>
                    </div>
        
                    <div class="flex flex-wrap gap-2">
                        @if(!$isMentor)
                            @if(!auth()->check())
                                <a href="{{ route('login') }}"
                                   class="inline-flex items-center justify-center rounded-xl bg-[#004777] px-4 py-2.5 text-xs sm:text-sm font-medium text-white hover:bg-[#003560] transition">
                                    Login to Track
                                </a>
                            @else
                                @if($enrolled)
                                    <span class="inline-flex items-center rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-2 text-xs sm:text-sm font-medium text-emerald-700">
                                        Enrolled
                                    </span>
                                @else
                                    <button
                                        wire:click="enroll"
                                        wire:loading.attr="disabled"
                                        wire:target="enroll"
                                        class="inline-flex items-center justify-center rounded-xl bg-[#004777] px-4 py-2.5 text-xs sm:text-sm font-medium text-white hover:bg-[#003560] transition disabled:opacity-70 disabled:cursor-not-allowed">
                                        <span wire:loading.remove wire:target="enroll">Enroll</span>
        
                                        <span wire:loading.flex wire:target="enroll" class="items-center gap-2">
                                            <svg class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                                            </svg>
                                            Processing...
                                        </span>
                                    </button>
                                @endif
                            @endif
                        @else
                            <span class="inline-flex items-center rounded-xl border bg-slate-100 px-4 py-2 text-xs sm:text-sm text-slate-600">
                                Mentor Mode
                            </span>
                        @endif
                    </div>
        
                    @guest
                        <div class="rounded-xl border bg-slate-50 p-3 text-xs sm:text-sm text-slate-600 leading-6">
                            Sign in to track progress, assessments, and certificate eligibility.
                        </div>
                    @endguest
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

                <div class="relative min-h-[220px] sm:min-h-[280px] bg-slate-100">
                    <img src="{{ $posterSrc ?? asset('images/thumbnail/thumbnail_candle.png') }}"
                        alt="{{ $course->title }}"
                        class="h-full w-full object-cover">

                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950/35 via-transparent to-transparent"></div>
                </div>
            </div>
        </section>
        
        @if($isStudent)
        
            <section class="rounded-2xl border bg-white shadow-sm">
                <div class="border-b px-5 py-4">
                    <h2 class="text-sm font-semibold uppercase tracking-wide text-[#004777]">Course Access</h2>
                    <p class="mt-1 text-xs text-slate-500">Assessment and certificate status in one place.</p>
                </div>

                <div class="divide-y">
                    <div class="px-5 py-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <div class="text-xs uppercase tracking-wide text-slate-400">Course Assessment</div>
                            <div class="mt-1 text-sm font-semibold text-slate-900">
                                {{ $assessmentMeta['title'] ?? 'No assessment published yet' }}
                            </div>
                        </div>

                        <div class="flex flex-wrap items-center gap-2">
                            @if($assessmentMeta)
                                @if($this->assessmentUnlocked)
                                    <span class="px-2.5 py-1 rounded-full text-[11px] bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        Unlocked
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[11px] bg-amber-50 text-amber-700 border border-amber-200">
                                        Locked
                                    </span>
                                @endif

                                @if($this->assessmentUnlocked)
                                    @if($this->activeAttempt)
                                        <a href="{{ route('assessments.take', $assessment->id) }}"
                                           class="px-3 py-2 rounded-lg bg-amber-500 text-white text-xs font-semibold hover:bg-amber-600 transition">
                                            Resume Test
                                        </a>
                                    @else
                                        <a href="{{ route('assessments.take', $assessment->id) }}"
                                           class="px-3 py-2 rounded-lg bg-emerald-500 text-white text-xs font-semibold hover:bg-emerald-600 transition">
                                            Start Test
                                        </a>
                                    @endif
                                @endif
                            @else
                                <span class="px-2.5 py-1 rounded-full text-[11px] bg-slate-100 text-slate-600 border border-slate-200">
                                    Not Published
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="px-5 py-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <div class="text-xs uppercase tracking-wide text-slate-400">Course Certificate</div>
                            <div class="mt-1 text-sm font-semibold text-slate-900">Certificate Access</div>
                        </div>

                        <div class="flex flex-wrap items-center gap-2">
                            @if($courseCertificate)
                                <span class="px-2.5 py-1 rounded-full text-[11px] bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    Issued
                                </span>

                                @if($isStudent && $enrolled)
                                    <a href="{{ route('certificates.download', $courseCertificate->id) }}"
                                       class="inline-flex px-3 py-2 rounded-lg bg-emerald-600 text-white text-xs font-medium hover:bg-emerald-700 transition">
                                        Download Certificate
                                    </a>
                                @endif
                            @elseif($certificateEligibility['eligible'])
                                <span class="px-2.5 py-1 rounded-full text-[11px] bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    Eligible
                                </span>

                                <a href="{{ route('certificates.course.claim', $course->id) }}"
                                   class="inline-flex px-3 py-2 rounded-lg bg-[#004777] text-white text-xs font-medium hover:bg-[#003560] transition">
                                    Claim Certificate
                                </a>
                            @else
                                <span class="px-2.5 py-1 rounded-full text-[11px] bg-amber-50 text-amber-700 border border-amber-200">
                                    Locked
                                </span>

                                @if($assessment && $this->assessmentUnlocked)
                                    <a href="{{ route('assessments.take', $assessment->id) }}"
                                       class="inline-flex px-3 py-2 rounded-lg bg-[#004777] text-white text-xs hover:bg-[#003560] transition">
                                        Go to assessment
                                    </a>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            </section>
        
            @if($isStudent && $canTrack)
                <section class="rounded-3xl border bg-white p-5 sm:p-6">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <h2 class="text-xl font-semibold tracking-tight text-slate-900">Learning Progress</h2>
                            <p class="mt-1 text-sm text-slate-500">Ringkasan progres topic pada course ini.</p>
                        </div>
        
                        <div class="flex flex-wrap gap-2 text-xs">
                            <span class="rounded-full border bg-emerald-50 px-3 py-1 font-medium text-emerald-700">
                                Completed {{ $this->completedTopicsCount }}
                            </span>
                            <span class="rounded-full border bg-blue-50 px-3 py-1 font-medium text-blue-700">
                                In Progress {{ $this->inProgressTopicsCount }}
                            </span>
                            <span class="rounded-full border bg-slate-50 px-3 py-1 font-medium text-slate-700">
                                Not Started {{ $this->notStartedTopicsCount }}
                            </span>
                        </div>
                    </div>
                </section>
            @endif
        
            <section class="space-y-5">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900">
                            Course Topics
                        </h2>
                    </div>
        
                    <div class="hidden md:flex items-center gap-2 text-xs text-slate-500">
                        <span>{{ $course->topics->count() }} Topics</span>
                        <span>•</span>
                        <span>{{ $this->completedTopicsCount }} Completed</span>
                    </div>
                </div>
        
                <div class="rounded-2xl border bg-white shadow-sm overflow-hidden">
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
                                <div class="flex items-start gap-3 sm:gap-4 min-w-0">
                                    <div class="flex h-10 w-10 sm:h-11 sm:w-11 shrink-0 items-center justify-center rounded-xl bg-[#004777] text-sm sm:text-base font-bold text-white">
                                        {{ $index + 1 }}
                                    </div>

                                    <div class="min-w-0">
                                        <h3 class="text-base sm:text-lg font-semibold text-slate-900 leading-tight">
                                            {{ $topic->name }}
                                        </h3>

                                        <p class="mt-1 text-xs sm:text-sm leading-6 text-slate-500 line-clamp-2">
                                            {{ $topic->description }}
                                        </p>

                                        <div class="mt-3 flex flex-wrap gap-2 text-[11px] sm:text-xs">
                                            <span class="px-2.5 py-1 rounded-full border {{ $badge }}">
                                                @if($status === 'available')
                                                    Available
                                                @elseif($status === 'mentor')
                                                    Review
                                                @else
                                                    {{ str_replace('_', ' ', ucfirst($status)) }}
                                                @endif
                                            </span>

                                            <span class="px-2.5 py-1 rounded-full {{ $sessionBadge }}">
                                                {{ $topic->videoSessions->isNotEmpty() ? 'Session ' . $sessionStatus : 'No Session' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex shrink-0 flex-wrap gap-2 sm:justify-end">
                                    @guest
                                        <a href="{{ route('login') }}"
                                           class="inline-flex items-center justify-center rounded-xl bg-[#004777] px-4 py-2.5 text-xs sm:text-sm font-medium text-white hover:bg-[#003560] transition">
                                            Login to access topic
                                        </a>
                                    @endguest

                                    @auth
                                        <a href="{{ route('topics.show', $topic->slug) }}"
                                           class="inline-flex items-center rounded-xl bg-[#004777] px-6 py-2.5 text-xs sm:text-sm font-medium text-white hover:bg-[#003560] transition">
                                            Open Topic
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
                    <h2 class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900">
                        Mentored Topics
                    </h2>
                    <p class="text-sm text-slate-500">
                        Topics where you are Owner or Collaborator.
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
        
                                    $roleLabel = $mentorRole === 'owner' ? 'Owner' : 'Collaborator';
                                @endphp
        
                                <div class="rounded-2xl border p-4">
                                    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                        <div class="min-w-0">
                                            <div class="text-sm font-semibold text-slate-900">
                                                {{ $topic->name }}
                                            </div>
                                            <div class="mt-1 text-xs text-slate-500 line-clamp-2">
                                                {{ $topic->description }}
                                            </div>
                                        </div>
        
                                        <div class="flex flex-wrap items-center gap-2 shrink-0">
                                            <span class="rounded-full border px-3 py-1 text-[11px] font-medium {{ $roleBadge }}">
                                                {{ $roleLabel }}
                                            </span>
        
                                            <a href="{{ route('mentor.topics.show', $topic->slug) }}"
                                               class="rounded-xl border border-[#004777] px-3 py-2 text-xs text-[#004777] hover:bg-[#004777] hover:text-white transition">
                                                Workspace
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
                                Tidak ada topik yang dikelola
                            </div>
                            <p class="mt-2 text-sm text-slate-500 leading-6">
                                Anda belum ditetapkan sebagai Owner atau Collaborator pada topik mana pun di course ini.
                            </p>
                        </div>
                    </div>
                @endif
            </section>
        @endif
    </div>

    @if($showAssessmentModal && $assessmentMeta && $isStudent)
        <div class="fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
            <div class="absolute inset-0" wire:click="closeAssessmentModal"></div>

            <div class="relative z-10 w-full max-w-6xl bg-white rounded-2xl shadow-2xl max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between p-6 border-b">
                    <div>
                        <h3 class="text-lg font-semibold">Assessment Details</h3>
                        <p class="text-sm text-slate-500">{{ $course->title }}</p>
                    </div>

                    <button type="button"
                            wire:click="closeAssessmentModal"
                            class="text-slate-500 hover:text-black text-2xl leading-none">
                        ✕
                    </button>
                </div>

                <div class="p-6 grid grid-cols-1 xl:grid-cols-[0.95fr_1.05fr] gap-6">
                    <div class="space-y-4">
                        <div class="rounded-2xl border p-5 bg-slate-50">
                            <div class="text-xs uppercase tracking-wide text-slate-400">Assessment</div>
                            <h4 class="text-2xl font-bold mt-2">{{ $assessmentMeta['title'] }}</h4>
                            <p class="text-sm text-slate-500 mt-2">
                                {{ $assessmentMeta['status'] }}
                            </p>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div class="rounded-xl border p-4 bg-slate-50">
                                <div class="text-xs text-slate-500">Questions</div>
                                <div class="font-semibold mt-1">{{ $assessmentMeta['question_count'] }}</div>
                            </div>

                            <div class="rounded-xl border p-4 bg-slate-50">
                                <div class="text-xs text-slate-500">Passing Grade</div>
                                <div class="font-semibold mt-1">{{ $assessmentMeta['passing_grade'] }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-2xl border bg-slate-50 p-6">
                        <div class="text-xs uppercase tracking-wide text-slate-400 mb-4">
                            Instructions
                        </div>

                        <ul class="space-y-3 text-sm text-slate-700 list-disc pl-5">
                            @foreach($assessmentMeta['instructions'] as $instruction)
                                <li>{{ $instruction }}</li>
                            @endforeach
                        </ul>

                        <div class="mt-6 flex flex-wrap gap-3">
                            @if($this->assessmentUnlocked)
                                @if($this->activeAttempt)
                                    <a href="{{ route('assessments.take', $assessment->id) }}"
                                       class="px-4 py-2 rounded-xl bg-amber-500 text-white text-sm hover:bg-amber-600 transition">
                                        Resume Test
                                    </a>
                                @else
                                    <a href="{{ route('assessments.take', $assessment->id) }}"
                                       class="px-4 py-2 rounded-xl bg-[#004777] text-white text-sm hover:bg-[#003560] transition">
                                        Start Test
                                    </a>
                                @endif
                            @else
                                <span class="px-4 py-2 rounded-xl border text-sm text-slate-500">
                                    Locked until all topics completed
                                </span>
                            @endif

                            <button type="button"
                                    wire:click="closeAssessmentModal"
                                    class="px-4 py-2 rounded-xl border text-sm">
                                Close
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>