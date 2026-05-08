@php
    $isMentor = auth()->check() && session('active_role') === 'disciples';
    $isStudent = auth()->check() && session('active_role') === 'student';
    $isGuest = !auth()->check();
    $topicsTotal = $course->topics->count();

    $statusColors = [
        'not_started' => [
            'badge' => 'bg-red-100 text-red-700',
            'bar'   => 'bg-red-500',
        ],
        'in_progress' => [
            'badge' => 'bg-blue-100 text-blue-700',
            'bar'   => 'bg-blue-500',
        ],
        'completed' => [
            'badge' => 'bg-green-100 text-green-700',
            'bar'   => 'bg-green-500',
        ],
    ];
@endphp

<div class="space-y-8 lg:px-36 pb-10">

    <section class="rounded-3xl bg-white border overflow-hidden shadow-sm">
        <div class="grid xl:grid-cols-[1.15fr_0.85fr]">
            <div class="p-8 sm:p-10 space-y-6">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div class="text-xs uppercase tracking-widest text-slate-400">
                        {{ $course->studyProgram?->title }}
                    </div>

                    <div class="flex flex-wrap gap-2">
                        @if($isGuest)
                            <span class="px-3 py-1 rounded-full text-xs bg-slate-100 text-slate-600">
                                Guest Preview
                            </span>
                        @elseif($isMentor)
                            <span class="px-3 py-1 rounded-full text-xs bg-indigo-100 text-indigo-700">
                                Mentor View
                            </span>
                            <a href="{{ route('mentor.topics.index') }}"
                               class="px-4 py-2 rounded-xl bg-slate-900 text-white text-sm">
                                Manage Topics
                            </a>
                        @else
                            <span class="px-3 py-1 rounded-full text-xs bg-emerald-100 text-emerald-700">
                                Student View
                            </span>
                        @endif
                    </div>
                </div>

                <div class="space-y-3">
                    <h1 class="text-3xl sm:text-4xl font-bold tracking-tight">
                        {{ $course->title }}
                    </h1>

                    <p class="text-slate-600 max-w-3xl leading-7">
                        {{ $course->description }}
                    </p>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    <div class="rounded-2xl border bg-slate-50 p-4">
                        <div class="text-xs text-slate-500">Topics</div>
                        <div class="text-2xl font-bold mt-1">{{ $topicsTotal }}</div>
                    </div>

                    <div class="rounded-2xl border bg-slate-50 p-4">
                        <div class="text-xs text-slate-500">Completed</div>
                        <div class="text-2xl font-bold mt-1">{{ $this->completedTopicsCount }}</div>
                    </div>

                    <div class="rounded-2xl border bg-slate-50 p-4">
                        <div class="text-xs text-slate-500">Assessment</div>
                        <div class="text-2xl font-bold mt-1">
                            {{ $assessment ? 'Active' : 'None' }}
                        </div>
                    </div>

                    <div class="rounded-2xl border bg-slate-50 p-4">
                        <div class="text-xs text-slate-500">Certificate</div>
                        <div class="text-2xl font-bold mt-1">
                            {{ $courseCertificate ? 'Issued' : 'Pending' }}
                        </div>
                    </div>
                </div>

                <div class="flex flex-wrap gap-3">
                    @if($isGuest)
                        <a href="{{ route('login') }}"
                           class="inline-flex items-center justify-center px-4 h-10 bg-slate-900 text-white rounded-xl text-sm">
                            Login to access full course
                        </a>

                        <span class="inline-flex items-center justify-center px-4 h-10 bg-slate-100 text-slate-600 rounded-xl text-sm">
                            Read-only preview
                        </span>
                    @elseif($isMentor)
                        <span class="inline-flex items-center justify-center px-4 h-10 bg-slate-100 text-slate-700 rounded-xl text-sm">
                            Mentor Mode
                        </span>
                    @else
                        @if(!auth()->check())
                            <a href="{{ route('login') }}"
                               class="inline-flex items-center justify-center px-4 h-10 bg-slate-900 text-white rounded-xl text-sm">
                                Login to enroll
                            </a>
                        @else
                            @if($enrolled)
                                <span class="inline-flex items-center justify-center px-4 h-10 bg-emerald-50 text-emerald-700 rounded-xl text-sm">
                                    Enrolled
                                </span>
                            @else
                                <button wire:click="enroll"
                                        class="inline-flex items-center justify-center px-4 h-10 bg-slate-900 text-white rounded-xl text-sm">
                                    Enroll
                                </button>
                            @endif
                        @endif
                    @endif

                    @if($courseCertificate && $isStudent && $enrolled)
                        <a href="{{ route('certificates.download', $courseCertificate->id) }}"
                           class="inline-flex items-center justify-center px-4 h-10 rounded-xl bg-emerald-50 text-emerald-700 text-sm">
                            View Certificate
                        </a>
                    @elseif($isStudent)
                        <span class="inline-flex items-center justify-center px-4 h-10 rounded-xl bg-slate-100 text-slate-400 text-sm cursor-not-allowed">
                            Certificate Locked
                        </span>
                    @endif
                </div>
            </div>

            <div class="bg-slate-100">
                <img src="{{ asset('images/test.png') }}"
                     class="w-full h-full object-cover min-h-[280px]"
                     alt="{{ $course->title }}">
            </div>
        </div>
    </section>

    @if($isGuest)
        <section class="rounded-3xl bg-white border p-6 space-y-5 shadow-sm">
            <div class="flex items-end justify-between gap-4">
                <div>
                    <h2 class="text-xl font-semibold">Course Preview</h2>
                    <p class="text-sm text-slate-500">
                        Tamu hanya melihat pratinjau read-only dari struktur course dan cuplikan materi.
                    </p>
                </div>

                <div class="rounded-xl border bg-slate-50 px-3 py-2 text-sm text-slate-600">
                    Preview mode
                </div>
            </div>

            @if($guestPreviewTopics->isEmpty())
                <x-ui.empty-state
                    title="No preview available"
                    description="Course ini belum memiliki topic yang bisa dipratinjau."
                />
            @else
                <div class="grid md:grid-cols-2 xl:grid-cols-3 gap-4">
                    @foreach($guestPreviewTopics as $topic)
                        <div class="rounded-3xl bg-white border shadow-sm p-5 space-y-4">
                            <div class="flex items-start justify-between gap-3">
                                <div class="space-y-1">
                                    <h3 class="font-semibold text-lg leading-tight">
                                        {{ $topic['name'] }}
                                    </h3>
                                    <p class="text-sm text-slate-500 leading-6 line-clamp-3">
                                        {{ $topic['description'] }}
                                    </p>
                                </div>

                                <span class="text-[11px] px-2.5 py-1 rounded-full whitespace-nowrap bg-slate-100 text-slate-600">
                                    PREVIEW
                                </span>
                            </div>

                            <div class="grid grid-cols-2 gap-2 text-xs text-slate-500">
                                <div class="rounded-xl border bg-slate-50 p-3">
                                    Materials: {{ $topic['materials_count'] }}
                                </div>
                                <div class="rounded-xl border bg-slate-50 p-3">
                                    Sessions: {{ $topic['video_sessions_count'] }}
                                </div>
                            </div>

                            <div class="rounded-2xl border bg-slate-50 p-4 space-y-2">
                                <div class="text-xs uppercase tracking-wide text-slate-400">Material preview</div>
                                @forelse($topic['preview_materials'] as $material)
                                    <div class="text-sm text-slate-700 flex items-center justify-between gap-3">
                                        <span>{{ $material['name'] }}</span>
                                        <span class="text-xs text-slate-400">{{ strtoupper($material['type']) }}</span>
                                    </div>
                                @empty
                                    <div class="text-sm text-slate-400">No material preview available.</div>
                                @endforelse
                            </div>

                            <div class="flex flex-wrap gap-2">
                                <a href="{{ route('login') }}"
                                   class="px-4 py-2 rounded-xl bg-slate-900 text-white text-sm">
                                    Login to open
                                </a>
                                <span class="px-4 py-2 rounded-xl border text-sm text-slate-500">
                                    Read-only
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>
    @else
        <section class="rounded-3xl bg-white border p-6 space-y-5 shadow-sm">
            <div class="flex flex-wrap items-end justify-between gap-4">
                <div>
                    <h2 class="text-xl font-semibold">Topics</h2>
                    <p class="text-sm text-slate-500">
                        Cari, sortir, dan filter topik untuk navigasi yang lebih cepat.
                    </p>
                </div>

                <div class="flex flex-wrap gap-2">
                    <div class="rounded-xl border bg-slate-50 px-3 py-2 text-sm">
                        {{ $this->completedTopicsCount }} completed
                    </div>
                    <div class="rounded-xl border bg-slate-50 px-3 py-2 text-sm">
                        {{ $this->inProgressTopicsCount }} in progress
                    </div>
                    <div class="rounded-xl border bg-slate-50 px-3 py-2 text-sm">
                        {{ $this->notStartedTopicsCount }} not started
                    </div>
                </div>
            </div>

            @if($isStudent)
                <div class="grid grid-cols-1 lg:grid-cols-[1.4fr_0.7fr_0.7fr_0.7fr_auto] gap-3">
                    <input wire:model.live.debounce.300ms="topicSearch"
                           type="search"
                           class="w-full border rounded-xl px-4 py-3"
                           placeholder="Search topics...">

                    <select wire:model.live="topicStatusFilter" class="border rounded-xl px-4 py-3">
                        <option value="all">All status</option>
                        <option value="not_started">Not started</option>
                        <option value="in_progress">In progress</option>
                        <option value="completed">Completed</option>
                    </select>

                    <select wire:model.live="topicSort" class="border rounded-xl px-4 py-3">
                        <option value="sort_asc">Sort order ↑</option>
                        <option value="sort_desc">Sort order ↓</option>
                        <option value="name_asc">Name A-Z</option>
                        <option value="name_desc">Name Z-A</option>
                        <option value="progress_desc">Progress high-low</option>
                        <option value="progress_asc">Progress low-high</option>
                    </select>

                    <button wire:click="clearTopicFilters"
                            class="px-4 py-3 rounded-xl border text-sm bg-white hover:bg-slate-50">
                        Reset
                    </button>

                    <div class="px-4 py-3 rounded-xl bg-slate-900 text-white text-sm text-center">
                        {{ $filteredTopics->count() }} shown
                    </div>
                </div>
            @else
                <div class="rounded-2xl border bg-slate-50 p-4 text-sm text-slate-600">
                    Mentor view tidak menggunakan progress belajar. Konten tetap bisa dibaca, tetapi interaksi student seperti enrollment, claim certificate, dan assessment submission tidak ditampilkan di sini.
                </div>
            @endif
        </section>

        <section class="space-y-4">
            @if($filteredTopics->isEmpty())
                <x-ui.empty-state
                    title="No topics match your filter"
                    description="Coba ganti kata pencarian, status, atau urutan topik."
                />
            @else
                <div class="grid md:grid-cols-2 xl:grid-cols-3 gap-4">
                    @foreach($filteredTopics as $topic)
                        <div class="rounded-3xl bg-white border shadow-sm p-5 space-y-4">
                            <div class="flex items-start justify-between gap-3">
                                <div class="space-y-1">
                                    <h3 class="font-semibold text-lg leading-tight">
                                        {{ $topic->name }}
                                    </h3>
                                    <p class="text-sm text-slate-500 leading-6 line-clamp-3">
                                        {{ $topic->description }}
                                    </p>
                                </div>

                                @if($isStudent)
                                    @php
                                        $status = $topic->progress_status ?? 'not_started';
                                        $color = $statusColors[$status] ?? $statusColors['not_started'];
                                    @endphp
                                    <span class="text-[11px] px-2.5 py-1 rounded-full whitespace-nowrap {{ $color['badge'] }}">
                                        {{ strtoupper($status) }}
                                    </span>
                                @else
                                    <span class="text-[11px] px-2.5 py-1 rounded-full whitespace-nowrap bg-indigo-100 text-indigo-700">
                                        MANAGE
                                    </span>
                                @endif
                            </div>

                            @if($isStudent)
                                @php
                                    $status = $topic->progress_status ?? 'not_started';
                                    $color = $statusColors[$status] ?? $statusColors['not_started'];
                                @endphp

                                <div class="space-y-2">
                                    <div class="flex items-center justify-between text-xs text-slate-500">
                                        <span>Progress</span>
                                        <span>{{ $topic->progress_percent }}%</span>
                                    </div>
                                    <div class="h-2 bg-slate-100 rounded-full overflow-hidden">
                                        <div class="h-2 rounded-full {{ $color['bar'] }}"
                                             style="width: {{ $topic->progress_percent }}%">
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="grid grid-cols-2 gap-2 text-xs text-slate-500">
                                    <div class="rounded-xl border bg-slate-50 p-3">
                                        Materials: {{ $topic->materials_count }}
                                    </div>
                                    <div class="rounded-xl border bg-slate-50 p-3">
                                        Sessions: {{ $topic->video_sessions_count }}
                                    </div>
                                </div>
                            @endif

                            <div class="flex flex-wrap gap-2 pt-1">
                                @if($isStudent)
                                    <a href="{{ route('topics.show', $topic->slug) }}"
                                       class="px-4 py-2 rounded-xl bg-slate-900 text-white text-sm">
                                        Open
                                    </a>
                                @else
                                    <a href="{{ route('mentor.materials.index', $topic->slug) }}"
                                       class="px-4 py-2 rounded-xl bg-slate-900 text-white text-sm">
                                        Manage
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>
    @endif

    @if($isStudent)
        <section class="grid xl:grid-cols-2 gap-6">
            <div class="rounded-3xl border shadow-sm overflow-hidden bg-gradient-to-br from-slate-950 via-slate-900 to-slate-800 text-white">
                <div class="p-6 sm:p-8 space-y-6">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <div class="text-xs uppercase tracking-[0.25em] text-slate-400">
                                Course Assessment
                            </div>
                            <h2 class="text-2xl font-bold mt-2">
                                {{ $assessmentMeta['title'] ?? 'No assessment published yet' }}
                            </h2>
                            <p class="text-sm text-slate-300 mt-2 leading-6 max-w-xl">
                                {{ $assessment
                                    ? 'Assessment ini akan terbuka setelah seluruh topic selesai.'
                                    : 'Belum ada assessment aktif untuk course ini.' }}
                            </p>
                        </div>

                        @if($assessmentMeta)
                            @if($this->assessmentUnlocked)
                                <span class="px-3 py-1 rounded-full text-xs bg-emerald-500/15 text-emerald-300 border border-emerald-400/20">
                                    Unlocked
                                </span>
                            @else
                                <span class="px-3 py-1 rounded-full text-xs bg-amber-500/15 text-amber-300 border border-amber-400/20">
                                    Locked
                                </span>
                            @endif
                        @endif
                    </div>

                    @if($assessmentMeta)
                        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
                            <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                                <div class="text-xs text-slate-400">Questions</div>
                                <div class="text-2xl font-bold mt-1">{{ $assessmentMeta['question_count'] }}</div>
                            </div>

                            <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                                <div class="text-xs text-slate-400">Passing Grade</div>
                                <div class="text-2xl font-bold mt-1">{{ $assessmentMeta['passing_grade'] }}</div>
                            </div>

                            <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                                <div class="text-xs text-slate-400">Time Limit</div>
                                <div class="text-2xl font-bold mt-1">
                                    {{ $assessmentMeta['time_limit_minutes'] ? $assessmentMeta['time_limit_minutes'].' min' : 'No limit' }}
                                </div>
                            </div>

                            <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                                <div class="text-xs text-slate-400">Estimated</div>
                                <div class="text-2xl font-bold mt-1">{{ $assessmentMeta['estimated_minutes'] }} min</div>
                            </div>
                        </div>

                        <div class="rounded-2xl border border-white/10 bg-white/5 p-5 space-y-3">
                            <div class="text-sm font-semibold">Why is it locked?</div>

                            @if($this->assessmentUnlocked)
                                <div class="text-sm text-emerald-300">
                                    All topics are completed. You can start the assessment now.
                                </div>
                            @else
                                <ul class="space-y-2 text-sm text-slate-300 list-disc pl-5">
                                    <li>Finish all topics in this course.</li>
                                    <li>Ensure your progress is marked as completed.</li>
                                    <li>Then return here to open the assessment.</li>
                                </ul>
                            @endif
                        </div>

                        <div class="flex flex-wrap gap-3">
                            <button wire:click="openAssessmentModal"
                                    class="px-4 py-2 rounded-xl bg-white text-slate-950 text-sm font-medium">
                                View Assessment Details
                            </button>

                            @if($this->assessmentUnlocked)
                                @if($this->activeAttempt)
                                    <a href="{{ route('assessments.take', $assessment->id) }}"
                                       class="px-4 py-2 rounded-xl bg-amber-400 text-slate-950 text-sm font-semibold">
                                        Resume Test
                                    </a>
                                @else
                                    <a href="{{ route('assessments.take', $assessment->id) }}"
                                       class="px-4 py-2 rounded-xl bg-emerald-400 text-slate-950 text-sm font-semibold">
                                        Start Test
                                    </a>
                                @endif
                            @else
                                <span class="px-4 py-2 rounded-xl border border-white/10 text-sm text-slate-300">
                                    Locked until all topics are completed
                                </span>
                            @endif
                        </div>
                    @else
                        <div class="rounded-2xl border border-white/10 bg-white/5 p-5 text-sm text-slate-300">
                            Assessment belum dipublikasikan untuk course ini.
                        </div>
                    @endif
                </div>
            </div>

            <div class="rounded-3xl bg-white border shadow-sm p-6 sm:p-8 space-y-5">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <div class="text-xs uppercase tracking-[0.2em] text-slate-400">
                            Course Certificate
                        </div>
                        <h2 class="text-2xl font-bold mt-2 text-slate-900">
                            Certificate Access
                        </h2>
                        <p class="text-sm text-slate-500 mt-2 leading-6">
                            Sertifikat hanya bisa diakses jika prasyarat course telah terpenuhi.
                        </p>
                    </div>

                    @if($courseCertificate)
                        <span class="px-3 py-1 rounded-full text-xs bg-emerald-50 text-emerald-700">
                            Issued
                        </span>
                    @elseif($certificateEligibility['eligible'])
                        <span class="px-3 py-1 rounded-full text-xs bg-emerald-50 text-emerald-700">
                            Eligible
                        </span>
                    @else
                        <span class="px-3 py-1 rounded-full text-xs bg-amber-50 text-amber-700">
                            Locked
                        </span>
                    @endif
                </div>

                @if($courseCertificate)
                    <div class="rounded-2xl border bg-emerald-50/40 p-5 space-y-3">
                        <div class="text-sm font-semibold text-emerald-700">Certificate already issued</div>
                        <p class="text-sm text-slate-600 leading-6">
                            Sertifikat untuk course ini sudah tersedia dan siap diunduh.
                        </p>

                        <a href="{{ route('certificates.download', $courseCertificate->id) }}"
                           class="inline-flex px-4 py-2 rounded-xl bg-emerald-600 text-white text-sm font-medium">
                            View Certificate
                        </a>
                    </div>
                @else
                    <div class="rounded-2xl border bg-slate-50 p-5 space-y-4">
                        <div class="text-sm font-semibold text-slate-900">Prerequisite checklist</div>

                        <div class="space-y-3">
                            @foreach($certificateEligibility['checks'] as $check)
                                <div class="flex items-start gap-3">
                                    <div class="mt-0.5 h-5 w-5 rounded-full flex items-center justify-center
                                        {{ $check['done'] ? 'bg-emerald-500 text-white' : 'bg-slate-200 text-slate-500' }}">
                                        {{ $check['done'] ? '✓' : '•' }}
                                    </div>

                                    <div>
                                        <div class="text-sm font-medium text-slate-900">{{ $check['label'] }}</div>
                                        <div class="text-xs text-slate-500">{{ $check['note'] }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        @if($certificateEligibility['eligible'])
                            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-700">
                                Semua prasyarat terpenuhi. Sertifikat dapat diminta sekarang.
                            </div>

                            <a href="{{ route('certificates.course.claim', $course->id) }}"
                               class="inline-flex px-4 py-2 rounded-xl bg-slate-900 text-white text-sm font-medium">
                                Claim Certificate
                            </a>
                        @else
                            <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4 space-y-2">
                                <div class="text-sm font-semibold text-amber-800">
                                    Certificate locked
                                </div>

                                <ul class="space-y-1 text-sm text-amber-800 list-disc pl-5">
                                    @foreach($certificateEligibility['reasons'] as $reason)
                                        <li>{{ $reason }}</li>
                                    @endforeach
                                </ul>
                            </div>

                            <div class="flex flex-wrap gap-3">
                                <span class="inline-flex px-4 py-2 rounded-xl border text-sm text-slate-500">
                                    Claim disabled
                                </span>

                                @if($assessment && $this->assessmentUnlocked)
                                    <a href="{{ route('assessments.take', $assessment->id) }}"
                                       class="inline-flex px-4 py-2 rounded-xl bg-slate-900 text-white text-sm">
                                        Go to assessment
                                    </a>
                                @endif
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </section>
    @elseif($isMentor)
        <section class="grid xl:grid-cols-2 gap-6">
            <div class="rounded-3xl border shadow-sm overflow-hidden bg-slate-950 text-white">
                <div class="p-6 sm:p-8 space-y-6">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <div class="text-xs uppercase tracking-[0.25em] text-slate-400">
                                Course Assessment
                            </div>
                            <h2 class="text-2xl font-bold mt-2">
                                {{ $assessmentMeta['title'] ?? 'No assessment published yet' }}
                            </h2>
                            <p class="text-sm text-slate-300 mt-2 leading-6 max-w-xl">
                                Mentor mode bersifat netral terhadap isi. Assessment hanya ditampilkan sebagai referensi konten.
                            </p>
                        </div>

                        <span class="px-3 py-1 rounded-full text-xs bg-white/10 border border-white/10 text-slate-200">
                            Reference only
                        </span>
                    </div>

                    @if($assessmentMeta)
                        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
                            <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                                <div class="text-xs text-slate-400">Questions</div>
                                <div class="text-2xl font-bold mt-1">{{ $assessmentMeta['question_count'] }}</div>
                            </div>
                            <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                                <div class="text-xs text-slate-400">Passing Grade</div>
                                <div class="text-2xl font-bold mt-1">{{ $assessmentMeta['passing_grade'] }}</div>
                            </div>
                            <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                                <div class="text-xs text-slate-400">Time Limit</div>
                                <div class="text-2xl font-bold mt-1">
                                    {{ $assessmentMeta['time_limit_minutes'] ? $assessmentMeta['time_limit_minutes'].' min' : 'No limit' }}
                                </div>
                            </div>
                            <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                                <div class="text-xs text-slate-400">Estimated</div>
                                <div class="text-2xl font-bold mt-1">{{ $assessmentMeta['estimated_minutes'] }} min</div>
                            </div>
                        </div>

                        <div class="rounded-2xl border border-white/10 bg-white/5 p-5 text-sm text-slate-300">
                            Gunakan halaman admin assessment untuk pengelolaan soal dan attempt.
                        </div>
                    @else
                        <div class="rounded-2xl border border-white/10 bg-white/5 p-5 text-sm text-slate-300">
                            Assessment belum dipublikasikan untuk course ini.
                        </div>
                    @endif
                </div>
            </div>

            <div class="rounded-3xl bg-white border shadow-sm p-6 sm:p-8 space-y-5">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <div class="text-xs uppercase tracking-[0.2em] text-slate-400">
                            Course Certificate
                        </div>
                        <h2 class="text-2xl font-bold mt-2 text-slate-900">
                            Management View
                        </h2>
                        <p class="text-sm text-slate-500 mt-2 leading-6">
                            Sertifikat tetap terlihat, namun penerbitan dan klaim dikelola melalui alur admin/student.
                        </p>
                    </div>

                    <span class="px-3 py-1 rounded-full text-xs bg-indigo-100 text-indigo-700">
                        Mentor
                    </span>
                </div>

                <div class="rounded-2xl border bg-slate-50 p-5 space-y-3">
                    <div class="text-sm font-semibold text-slate-900">Operational note</div>
                    <p class="text-sm text-slate-600 leading-6">
                        Mentor dapat membaca struktur course, memantau topic, dan mengelola konten. Aksi belajar seperti enroll, claim certificate, dan start assessment disembunyikan agar tampilan tetap netral.
                    </p>
                </div>
            </div>
        </section>
    @endif

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

                            <div class="rounded-xl border p-4 bg-slate-50">
                                <div class="text-xs text-slate-500">Time Limit</div>
                                <div class="font-semibold mt-1">
                                    {{ $assessmentMeta['time_limit_minutes'] ? $assessmentMeta['time_limit_minutes'].' minutes' : 'No limit' }}
                                </div>
                            </div>

                            <div class="rounded-xl border p-4 bg-slate-50">
                                <div class="text-xs text-slate-500">Estimated Completion</div>
                                <div class="font-semibold mt-1">{{ $assessmentMeta['estimated_minutes'] }} minutes</div>
                            </div>
                        </div>

                        <div class="rounded-2xl border p-5">
                            <div class="text-sm font-semibold mb-2">Readiness</div>

                            @if($this->assessmentUnlocked)
                                <div class="text-sm text-emerald-700">
                                    Assessment is unlocked and ready to start.
                                </div>
                            @else
                                <div class="text-sm text-amber-700">
                                    Complete all topics before starting the assessment.
                                </div>
                            @endif
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
                                       class="px-4 py-2 rounded-xl bg-amber-500 text-white text-sm">
                                        Resume Test
                                    </a>
                                @else
                                    <a href="{{ route('assessments.take', $assessment->id) }}"
                                       class="px-4 py-2 rounded-xl bg-black text-white text-sm">
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