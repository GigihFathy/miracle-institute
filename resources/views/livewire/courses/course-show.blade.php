@php
    $isMentor = auth()->check() && session('active_role') === 'disciples';
    $isStudent = auth()->check() && session('active_role') === 'student';
    $isGuest = !auth()->check();

    // $statusColors = [
    //     'not_started' => [
    //         'badge' => 'bg-red-100 text-red-700',
    //         'bar'   => 'bg-red-500',
    //     ],
    //     'in_progress' => [
    //         'badge' => 'bg-blue-100 text-blue-700',
    //         'bar'   => 'bg-blue-500',
    //     ],
    //     'completed' => [
    //         'badge' => 'bg-green-100 text-green-700',
    //         'bar'   => 'bg-green-500',
    //     ],
    // ];
@endphp

<div class="space-y-6 lg:px-36">

    <!-- HEADER -->
    <section class="rounded-3xl bg-white border overflow-hidden">
        <div class="grid xl:grid-cols-2">

            <!-- LEFT -->
            <div class="p-8 space-y-5">

                <div class="flex justify-between items-center">
                    <div class="text-xs text-slate-400 uppercase">
                        {{ $course->studyProgram?->title }}
                    </div>

                    <!-- ROLE BASED ACTION -->
                    @if($isMentor)
                        <div class="flex gap-2">
                            <a href="{{ route('mentor.topics.index') }}"
                               class="px-4 py-2 bg-slate-900 text-white rounded-xl text-sm">
                                Manage Topics
                            </a>
                        </div>
                    @endif
                </div>

                <h1 class="text-3xl font-bold">{{ $course->title }}</h1>

                <p class="text-slate-600">{{ $course->description }}</p>

                <!-- STATS -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    <div class="border p-4 rounded-xl bg-slate-50">
                        <div class="text-xs text-slate-500">Topics</div>
                        <div class="font-semibold">{{ $course->topics->count() }}</div>
                    </div>

                    <div class="border p-4 rounded-xl bg-slate-50">
                        <div class="text-xs text-slate-500">Credit</div>
                        <div class="font-semibold">{{ $course->credit }}</div>
                    </div>

                    <div class="border p-4 rounded-xl bg-slate-50">
                        <div class="text-xs text-slate-500">Quota</div>
                        <div class="font-semibold">{{ $course->quota }}</div>
                    </div>

                    <div class="border p-4 rounded-xl bg-slate-50">
                        <div class="text-xs text-slate-500">Status</div>
                        <div class="font-semibold">{{ ucfirst($course->status) }}</div>
                    </div>
                </div>

                <!-- ACTION -->
                <div class="flex gap-3">
                    @if(!$isMentor)
                        @if(!auth()->check())
                            <a href="{{ route('login') }}"
                            class="px-5 py-3 bg-slate-900 text-white rounded-xl text-sm">
                                Login to enroll
                            </a>
                        @else
                            @if($enrolled)
                                <span class="px-4 py-2 bg-emerald-50 text-emerald-700 rounded-xl text-sm">
                                    Enrolled
                                </span>
                            @else
                                <button wire:click="enroll"
                                        class="px-5 py-3 bg-slate-900 text-white rounded-xl text-sm">
                                    Enroll
                                </button>
                            @endif
                        @endif
                    @else
                        <span class="px-4 py-2 bg-slate-100 rounded-xl text-sm">
                            Mentor Mode
                        </span>
                    @endif
                </div>
            </div>

            <!-- IMAGE -->
            <div class="bg-slate-100">
                <img src="{{ $course->poster }}" alt="{{ $course->title }}"
                     class="w-full h-full object-cover">
            </div>
        </div>
    </section>


    {{-- Assessment & Certificate --}}
    @if($isStudent)
        <section class="space-y-4">

            <details class="group rounded-2xl border shadow-sm overflow-hidden bg-gradient-to-br from-slate-950 via-slate-900 to-slate-800 text-white">
                <summary class="list-none cursor-pointer px-5 py-4 flex items-center justify-between">
                    <div>
                        <div class="text-[10px] uppercase tracking-[0.22em] text-slate-400">
                            Course Assessment
                        </div>

                        <h2 class="text-lg font-bold mt-1">
                            {{ $assessmentMeta['title'] ?? 'No assessment published yet' }}
                        </h2>
                    </div>

                    <div class="flex items-center gap-2">
                        @if($assessmentMeta)
                            @if($this->assessmentUnlocked)
                                <span class="px-2.5 py-1 rounded-full text-[11px] bg-emerald-500/15 text-emerald-300 border border-emerald-400/20">
                                    Unlocked
                                </span>
                            @else
                                <span class="px-2.5 py-1 rounded-full text-[11px] bg-amber-500/15 text-amber-300 border border-amber-400/20">
                                    Locked
                                </span>
                            @endif
                        @endif

                        <svg class="w-4 h-4 transition group-open:rotate-180"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M19 9l-7 7-7-7"/>
                        </svg>
                    </div>
                </summary>

                <div class="px-5 pb-5 space-y-4 border-t border-white/10">
                    <p class="text-xs text-slate-300 leading-5 pt-4">
                        {{ $assessment
                            ? 'Assessment ini akan terbuka setelah seluruh topic selesai.'
                            : 'Belum ada assessment aktif untuk course ini.' }}
                    </p>

                    @if($assessmentMeta)

                        <div class="grid grid-cols-2 lg:grid-cols-4 gap-2">
                            <div class="rounded-xl border border-white/10 bg-white/5 p-3">
                                <div class="text-[11px] text-slate-400">Questions</div>
                                <div class="text-lg font-bold mt-1">{{ $assessmentMeta['question_count'] }}</div>
                            </div>

                            <div class="rounded-xl border border-white/10 bg-white/5 p-3">
                                <div class="text-[11px] text-slate-400">Passing Grade</div>
                                <div class="text-lg font-bold mt-1">{{ $assessmentMeta['passing_grade'] }}</div>
                            </div>

                            <div class="rounded-xl border border-white/10 bg-white/5 p-3">
                                <div class="text-[11px] text-slate-400">Time Limit</div>
                                <div class="text-lg font-bold mt-1">
                                    {{ $assessmentMeta['time_limit_minutes'] ? $assessmentMeta['time_limit_minutes'].' min' : 'No limit' }}
                                </div>
                            </div>

                            <div class="rounded-xl border border-white/10 bg-white/5 p-3">
                                <div class="text-[11px] text-slate-400">Estimated</div>
                                <div class="text-lg font-bold mt-1">
                                    {{ $assessmentMeta['estimated_minutes'] }} min
                                </div>
                            </div>
                        </div>

                        <div class="rounded-xl border border-white/10 bg-white/5 p-4 space-y-2">
                            <div class="text-xs font-semibold">Why is it locked?</div>

                            @if($this->assessmentUnlocked)
                                <div class="text-xs text-emerald-300">
                                    All topics are completed. You can start the assessment now.
                                </div>
                            @else
                                <ul class="space-y-1 text-xs text-slate-300 list-disc pl-4">
                                    <li>Finish all topics in this course.</li>
                                    <li>Ensure your progress is marked as completed.</li>
                                    <li>Then return here to open the assessment.</li>
                                </ul>
                            @endif
                        </div>

                        <div class="flex flex-wrap gap-2">
                            <button wire:click="openAssessmentModal"
                                    class="px-3 py-2 rounded-lg bg-white text-slate-950 text-xs font-medium">
                                View Details
                            </button>

                            @if($this->assessmentUnlocked)
                                @if($this->activeAttempt)
                                    <a href="{{ route('assessments.take', $assessment->id) }}"
                                    class="px-3 py-2 rounded-lg bg-amber-400 text-slate-950 text-xs font-semibold">
                                        Resume Test
                                    </a>
                                @else
                                    <a href="{{ route('assessments.take', $assessment->id) }}"
                                    class="px-3 py-2 rounded-lg bg-emerald-400 text-slate-950 text-xs font-semibold">
                                        Start Test
                                    </a>
                                @endif
                            @else
                                <span class="px-3 py-2 rounded-lg border border-white/10 text-xs text-slate-300">
                                    Locked
                                </span>
                            @endif
                        </div>

                    @else
                        <div class="rounded-xl border border-white/10 bg-white/5 p-4 text-xs text-slate-300">
                            Assessment belum dipublikasikan untuk course ini.
                        </div>
                    @endif
                </div>
            </details>

            <details class="group rounded-2xl bg-white border shadow-sm overflow-hidden">
                <summary class="list-none cursor-pointer px-5 py-4 flex items-center justify-between">
                    <div>
                        <div class="text-[10px] uppercase tracking-[0.2em] text-slate-400">
                            Course Certificate
                        </div>

                        <h2 class="text-lg font-bold mt-1 text-slate-900">
                            Certificate Access
                        </h2>
                    </div>

                    <div class="flex items-center gap-2">
                        @if($courseCertificate)
                            <span class="px-2.5 py-1 rounded-full text-[11px] bg-emerald-50 text-emerald-700">
                                Issued
                            </span>
                        @elseif($certificateEligibility['eligible'])
                            <span class="px-2.5 py-1 rounded-full text-[11px] bg-emerald-50 text-emerald-700">
                                Eligible
                            </span>
                        @else
                            <span class="px-2.5 py-1 rounded-full text-[11px] bg-amber-50 text-amber-700">
                                Locked
                            </span>
                        @endif

                        <svg class="w-4 h-4 transition group-open:rotate-180"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M19 9l-7 7-7-7"/>
                        </svg>
                    </div>
                </summary>

                <div class="px-5 pb-5 border-t space-y-4">
                    <p class="text-xs text-slate-500 leading-5 pt-4">
                        Sertifikat hanya bisa diakses jika prasyarat course telah terpenuhi.
                    </p>

                    @if($courseCertificate)

                        <div class="rounded-xl border bg-emerald-50/40 p-4 space-y-2">
                            <div class="text-xs font-semibold text-emerald-700">
                                Certificate already issued
                            </div>

                            <p class="text-xs text-slate-600 leading-5">
                                Sertifikat untuk course ini sudah tersedia dan siap diunduh.
                            </p>

                            @if($courseCertificate && $isStudent && $enrolled)
                                <a href="{{ route('certificates.download', $courseCertificate->id) }}"
                                class="inline-flex px-3 py-2 rounded-lg bg-emerald-600 text-white text-xs font-medium">
                                    Download Certificate
                                </a>
                            @elseif($isStudent && $certificateEligibility['eligible'])
                                <a href="{{ route('certificates.course.claim', $courseCertificate->id) }}"
                                class="inline-flex px-3 py-2 rounded-lg bg-emerald-600 text-white text-xs font-medium">
                                    Claim Certificate
                                </a>
                            @endif
                        </div>

                    @else

                        <div class="rounded-xl border bg-slate-50 p-4 space-y-3">
                            <div class="text-xs font-semibold text-slate-900">
                                Prerequisite checklist
                            </div>

                            <div class="space-y-2">
                                @foreach($certificateEligibility['checks'] as $check)
                                    <div class="flex items-start gap-2">
                                        <div class="mt-0.5 h-4 w-4 rounded-full flex items-center justify-center text-[10px]
                                            {{ $check['done'] ? 'bg-emerald-500 text-white' : 'bg-slate-200 text-slate-500' }}">
                                            {{ $check['done'] ? '✓' : '•' }}
                                        </div>

                                        <div>
                                            <div class="text-xs font-medium text-slate-900">
                                                {{ $check['label'] }}
                                            </div>

                                            <div class="text-[11px] text-slate-500">
                                                {{ $check['note'] }}
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            @if($certificateEligibility['eligible'])

                                <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-3 text-xs text-emerald-700">
                                    Semua prasyarat terpenuhi. Sertifikat dapat diminta sekarang.
                                </div>

                                <a href="{{ route('certificates.course.claim', $course->id) }}"
                                class="inline-flex px-3 py-2 rounded-lg bg-slate-900 text-white text-xs font-medium">
                                    Claim Certificate
                                </a>

                            @else

                                <div class="rounded-xl border border-amber-200 bg-amber-50 p-3 space-y-2">
                                    <div class="text-xs font-semibold text-amber-800">
                                        Certificate locked
                                    </div>

                                    <ul class="space-y-1 text-xs text-amber-800 list-disc pl-4">
                                        @foreach($certificateEligibility['reasons'] as $reason)
                                            <li>{{ $reason }}</li>
                                        @endforeach
                                    </ul>
                                </div>

                                <div class="flex flex-wrap gap-2">
                                    <span class="inline-flex px-3 py-2 rounded-lg border text-xs text-slate-500">
                                        Claim disabled
                                    </span>

                                    @if($assessment && $this->assessmentUnlocked)
                                        <a href="{{ route('assessments.take', $assessment->id) }}"
                                        class="inline-flex px-3 py-2 rounded-lg bg-slate-900 text-white text-xs">
                                            Go to assessment
                                        </a>
                                    @endif
                                </div>

                            @endif
                        </div>

                    @endif
                </div>
            </details>

        </section>
    @endif
    
    {{-- Modal Assessment --}}
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

    <!-- TOPICS -->
    <section class="space-y-4">
        <h2 class="text-xl font-semibold">Topics</h2>

        <div class="grid md:grid-cols-2 gap-4">
            @foreach($course->topics as $topic)

                @php
                    $status = $topicStatusMap[$topic->id] ?? 'not_started';
                    $percent = $status === 'completed' ? 100 : ($status === 'in_progress' ? 50 : 0);
                @endphp
                

                <div class="border p-5 rounded-2xl bg-white space-y-4">

                    <div class="flex justify-between">
                        <div>
                            <h3 class="font-semibold">{{ $topic->name }}</h3>
                            <p class="text-sm text-slate-500">{{ $topic->description }}</p>
                        </div>

                        <span class="text-xs bg-slate-100 px-2 py-1 rounded">
                            {{ strtoupper($status) }}
                        </span>
                    </div>

                    <div class="h-2 bg-slate-100 rounded">
                        <div class="bg-slate-900 h-2 rounded"
                             style="width: {{ $percent }}%">
                        </div>
                    </div>

                    <!-- ACTION -->
                    <div class="flex gap-2 flex-wrap">
                        <a href="{{ route('topics.show', $topic->slug) }}"
                           class="px-4 py-2 bg-slate-900 text-white rounded-xl text-sm">
                            Open
                        </a>

                        @if($isMentor)
                            <a href="{{ route('mentor.topics.show', $topic->slug) }}"
                               class="px-4 py-2 border rounded-xl text-sm">
                                Manage
                            </a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </section>
</div>