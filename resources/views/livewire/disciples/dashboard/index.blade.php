<div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 pb-10 space-y-6">

    {{-- HERO --}}
    <section class="overflow-hidden rounded-3xl border bg-white shadow-sm">
        <div>
            <div class="p-6 sm:p-8 xl:p-10 space-y-6">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div class="space-y-3">
                        <div class="text-xs uppercase tracking-[0.3em] text-slate-400">
                            Disciples Studio
                        </div>
                        <div class="space-y-2">
                            <h1 class="text-3xl sm:text-4xl font-bold tracking-tight text-slate-900">
                                Mentor Dashboard
                            </h1>
                            <p class="max-w-3xl leading-7 text-slate-600">
                                Ringkasan operasional untuk memantau kursus yang kamu mentori, konten yang aktif, dan area yang perlu perhatian.
                            </p>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route('mentor.courses.index') }}"
                           class="inline-flex h-10 items-center justify-center rounded-xl bg-slate-900 px-4 text-sm text-white transition hover:bg-slate-800">
                            Open Studio
                        </a>
                        <a href="{{ route('courses.index') }}"
                           class="inline-flex h-10 items-center justify-center rounded-xl border border-slate-200 bg-white px-4 text-sm text-slate-700 transition hover:bg-slate-50">
                            Public Site
                        </a>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 xl:grid-cols-6">
                    @php
                        $cards = [
                            ['label' => 'Mentored Topics', 'value' => $mentorTopicsCount],
                            ['label' => 'Mentored Courses', 'value' => $mentorCoursesCount],
                            ['label' => 'Materials Created', 'value' => $mentorMaterialsCount],
                            ['label' => 'Sessions', 'value' => $mentorSessionsCount],
                            ['label' => 'Assessments', 'value' => $mentorAssessmentsCount],
                            ['label' => 'Students Reached', 'value' => $mentorStudentsCount],
                        ];
                    @endphp

                    @foreach($cards as $card)
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                            <div class="text-xs text-slate-500">{{ $card['label'] }}</div>
                            <div class="mt-1 text-2xl font-bold text-slate-900">
                                {{ number_format($card['value']) }}
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <div class="text-xs uppercase tracking-[0.2em] text-slate-400">
                                Studio Health
                            </div>
                            <div class="mt-1 text-lg font-semibold text-slate-900">
                                {{ $studioHealthPct }}% content-ready topics
                            </div>
                        </div>

                        <span class="rounded-full border border-slate-200 bg-white px-3 py-1 text-xs text-slate-600">
                            {{ $mentorTopicsCount }} topic(s) tracked
                        </span>
                    </div>

                    <div class="mt-4 h-2 overflow-hidden rounded-full bg-slate-200">
                        <div class="h-2 rounded-full bg-slate-900" style="width: {{ $studioHealthPct }}%"></div>
                    </div>

                    <div class="mt-3 flex items-center justify-between text-sm text-slate-600">
                        <span>Topics with materials and sessions</span>
                        <span>{{ $studioHealthPct }}%</span>
                    </div>
                </div>

                @if($hasStudentRole)
                    <div class="rounded-3xl border border-slate-800 bg-slate-950 p-6 text-white shadow-sm">
                        <div class="flex items-start justify-between gap-4">
                            <div class="space-y-2">
                                <div class="text-xs uppercase tracking-[0.25em] text-slate-400">
                                    Personal learning snapshot
                                </div>
                                <h2 class="text-2xl font-bold">
                                    Student Mode
                                </h2>
                                <p class="max-w-2xl leading-6 text-sm text-slate-300">
                                    Karena akun ini juga memiliki role student, ringkasan belajar pribadi tetap ditampilkan di bawah dashboard mentor.
                                </p>
                            </div>

                            <span class="rounded-full border border-white/10 bg-white/10 px-3 py-1 text-xs text-slate-200">
                                Mixed role
                            </span>
                        </div>

                        <div class="mt-5 grid grid-cols-2 gap-3 sm:grid-cols-4">
                            <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                                <div class="text-xs text-slate-400">Courses</div>
                                <div class="mt-1 text-2xl font-bold">{{ number_format($studentSnapshot['courses']) }}</div>
                            </div>
                            <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                                <div class="text-xs text-slate-400">Completed</div>
                                <div class="mt-1 text-2xl font-bold">{{ number_format($studentSnapshot['topics_completed']) }}</div>
                            </div>
                            <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                                <div class="text-xs text-slate-400">Certificates</div>
                                <div class="mt-1 text-2xl font-bold">{{ number_format($studentSnapshot['certificates']) }}</div>
                            </div>
                            <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                                <div class="text-xs text-slate-400">Progress</div>
                                <div class="mt-1 text-2xl font-bold">{{ $studentSnapshot['progress_pct'] }}%</div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            {{-- <div class="bg-slate-100 p-5 sm:p-6 xl:p-8 flex items-center justify-center">
                <div class="w-full overflow-hidden rounded-3xl border bg-white shadow-sm">
                    <img src="{{ asset('images/logo.png') }}"
                         class="h-full w-full min-h-[320px] object-cover"
                         alt="Mentor dashboard">
                </div>
            </div> --}}
        </div>
    </section>

    {{-- MAIN CONTENT --}}
    <section class="grid gap-6 xl:grid-cols-[1.35fr_0.65fr]">
        <div class="space-y-6">
            {{-- MENTORED COURSES --}}
            <div class="rounded-3xl border bg-white p-6 shadow-sm">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-semibold text-slate-900">Mentored Courses</h2>
                        <p class="mt-1 text-sm text-slate-500">
                            Buka course untuk melihat topic, kesehatan konten, dan shortcut pengelolaan.
                        </p>
                    </div>
                </div>

                <div class="mt-5 space-y-3">
                    @forelse($mentoredTopicsByCourse as $courseTopics)
                        @php
                            $course = $courseTopics->course;
                            $courseTitle = $course?->title ?? 'No Course';
                            $courseImage = $course?->poster;
                            $topics = $courseTopics->topics;
                            $readiness = $topics->count()
                                ? round(($topics->filter(fn ($t) => $t->materials_count > 0 && $t->video_sessions_count > 0)->count() / $topics->count()) * 100)
                                : 0;
                        @endphp

                        <div wire:key="course-{{ $course?->id }}" x-data="{ open: false }" class="overflow-hidden rounded-3xl border border-slate-200 bg-slate-50">
                            <button type="button"
                                    @click="open = !open"
                                    class="flex w-full items-center justify-between gap-4 p-5 text-left transition hover:bg-slate-100/70">
                                <div class="flex min-w-0 items-center gap-3">
                                    <div class="aspect-video w-24 shrink-0 overflow-hidden rounded-xl bg-slate-100 ring-1 ring-inset ring-slate-200 sm:w-28">
                                        @if(!empty($courseImage))
                                            <img src="{{ asset('storage/' . $courseImage) }}"
                                                 alt="{{ $courseTitle }}"
                                                 class="h-full w-full object-cover">
                                        @else
                                            <div class="flex h-full w-full items-center justify-center bg-slate-200 text-xs font-semibold text-slate-500">
                                                {{ strtoupper(mb_substr($courseTitle, 0, 1)) }}
                                            </div>
                                        @endif
                                    </div>

                                    <div class="min-w-0">
                                        <div class="truncate font-medium text-slate-900">{{ $courseTitle }}</div>
                                        <div class="mt-1 text-xs text-slate-500">
                                            {{ $course?->studyProgram?->title ?? 'No Study Program' }} · {{ $course?->credit ?? 0 }} credits
                                        </div>
                                    </div>
                                </div>

                                <div class="flex items-center gap-3 text-xs text-slate-500">
                                    <span class="hidden sm:inline">{{ $courseTopics->totalTopics }} topic(s)</span>
                                    <svg :class="open ? 'rotate-180' : ''"
                                         class="h-4 w-4 transition-transform duration-300 ease-out"
                                         fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                            </button>

                            <div class="grid transition-all duration-300 ease-out"
                                 :class="open ? 'grid-rows-[1fr] opacity-100' : 'grid-rows-[0fr] opacity-0'">
                                <div class="overflow-hidden">
                                    <div class="px-5 pb-5">
                                        <div class="grid gap-3 lg:grid-cols-3">
                                            <div class="rounded-2xl border bg-white p-4">
                                                <div class="text-xs text-slate-500">Topics</div>
                                                <div class="mt-1 text-2xl font-bold text-slate-900">{{ $courseTopics->totalTopics }}</div>
                                            </div>
                                            <div class="rounded-2xl border bg-white p-4">
                                                <div class="text-xs text-slate-500">Materials</div>
                                                <div class="mt-1 text-2xl font-bold text-slate-900">{{ $courseTopics->totalMaterials }}</div>
                                            </div>
                                            <div class="rounded-2xl border bg-white p-4">
                                                <div class="text-xs text-slate-500">Sessions</div>
                                                <div class="mt-1 text-2xl font-bold text-slate-900">{{ $courseTopics->totalSessions }}</div>
                                            </div>
                                        </div>

                                        <div class="mt-4 space-y-2">
                                            <div class="flex items-center justify-between text-xs text-slate-500">
                                                <span>Content readiness</span>
                                                <span>{{ $readiness }}%</span>
                                            </div>
                                            <div class="h-2 overflow-hidden rounded-full bg-slate-200">
                                                <div class="h-2 rounded-full bg-slate-900" style="width: {{ $readiness }}%"></div>
                                            </div>
                                        </div>

                                        <div class="mt-5 overflow-hidden rounded-2xl border bg-white">
                                            <div class="divide-y divide-slate-100">
                                                @foreach($topics as $topic)
                                                    <div wire:key="topic-{{ $topic->id }}" class="flex items-center justify-between gap-4 px-4 py-3 transition hover:bg-slate-50">
                                                        <div class="min-w-0">
                                                            <div class="truncate text-sm font-medium text-slate-900">{{ $topic->name }}</div>
                                                            <div class="mt-1 text-xs text-slate-500">
                                                                {{ $topic->materials_count }} materials · {{ $topic->video_sessions_count }} sessions
                                                            </div>
                                                        </div>

                                                        <div class="flex shrink-0 gap-2">
                                                            <a href="{{ route('mentor.materials.index', ['topicFilter' => $topic->id]) }}"
                                                               class="rounded-full bg-slate-900 px-3 py-1.5 text-xs font-medium text-white transition hover:bg-slate-800">
                                                                Materials
                                                            </a>
                                                            <a href="{{ route('mentor.sessions.index', ['topicFilter' => $topic->id]) }}"
                                                               class="rounded-full border border-slate-200 bg-white px-3 py-1.5 text-xs font-medium text-slate-700 transition hover:bg-slate-50">
                                                                Sessions
                                                            </a>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="rounded-2xl border border-dashed border-slate-200 p-5 text-sm text-slate-500">
                            Belum ada topic yang kamu mentor.
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- ATTENTION --}}
            <div class="rounded-3xl border bg-white p-6 shadow-sm">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-semibold text-slate-900">Topics That Need Attention</h2>
                        <p class="mt-1 text-sm text-slate-500">
                            Topic yang belum lengkap material atau session-nya.
                        </p>
                    </div>
                </div>

                <div class="mt-5 space-y-3">
                    @forelse($attentionTopics as $topic)
                        <div class="flex items-start justify-between gap-4 rounded-2xl border bg-slate-50 p-4">
                            <div>
                                <div class="font-medium text-slate-900">{{ $topic->name }}</div>
                                <div class="mt-1 text-xs text-slate-500">
                                    {{ $topic->course?->title }} · {{ $topic->materials_count }} materials · {{ $topic->video_sessions_count }} sessions
                                </div>
                            </div>

                            <a href="{{ route('mentor.materials.index', ['topicFilter' => $topic->id]) }}"
                               class="rounded-xl bg-slate-900 px-3 py-2 text-xs text-white transition hover:bg-slate-800">
                                Fix
                            </a>
                        </div>
                    @empty
                        <div class="rounded-2xl border bg-emerald-50 p-4 text-sm text-emerald-700">
                            Semua topic yang kamu mentori sudah punya material dan session.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- SIDEBAR --}}
        <aside class="space-y-6 xl:sticky xl:top-6 self-start">
            <div class="overflow-hidden rounded-3xl border bg-white shadow-sm">
                <div class="border-b border-slate-100 px-5 pb-3 pt-5">
                    <h2 class="text-lg font-semibold">Recent Materials</h2>
                    <p class="text-sm text-slate-500">Riwayat materi yang kamu tambah.</p>
                </div>

                <div class="divide-y divide-slate-100">
                    @forelse($latestMaterials as $material)
                        <div class="flex items-start justify-between gap-3 px-5 py-4 transition hover:bg-slate-50">
                            <div class="min-w-0">
                                <div class="truncate text-sm font-medium text-slate-900">{{ $material->name }}</div>
                                <div class="mt-1 truncate text-xs text-slate-500">
                                    {{ $material->topic?->course?->title }} · {{ $material->topic?->name }} · {{ strtoupper($material->type) }}
                                </div>
                            </div>
                            <span class="shrink-0 rounded-full border border-slate-200 bg-white px-2 py-1 text-[11px] uppercase tracking-wide text-slate-600">
                                {{ $material->status }}
                            </span>
                        </div>
                    @empty
                        <div class="px-5 py-6 text-sm text-slate-500">
                            Belum ada materi yang kamu tambahkan.
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="overflow-hidden rounded-3xl border bg-white shadow-sm">
                <div class="border-b border-slate-100 px-5 pb-3 pt-5">
                    <h2 class="text-lg font-semibold">Upcoming Sessions</h2>
                    <p class="text-sm text-slate-500">Sesi terdekat yang perlu dipantau.</p>
                </div>

                <div class="divide-y divide-slate-100">
                    @forelse($upcomingSessions as $session)
                        <div class="px-5 py-4 transition hover:bg-slate-50">
                            <div class="text-sm font-medium text-slate-900">{{ $session->title }}</div>
                            <div class="mt-1 text-xs text-slate-500">
                                {{ $session->topic?->course?->title }} · {{ $session->topic?->name }}
                            </div>
                            <div class="mt-2 text-xs text-slate-400">
                                {{ $session->start_at?->format('d M Y, H:i') }} - {{ $session->end_at?->format('H:i') }}
                            </div>
                        </div>
                    @empty
                        <div class="px-5 py-6 text-sm text-slate-500">
                            Tidak ada sesi terjadwal.
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="overflow-hidden rounded-3xl border bg-white shadow-sm">
                <div class="border-b border-slate-100 px-5 pb-3 pt-5">
                    <h2 class="text-lg font-semibold">Assessment Highlights</h2>
                    <p class="text-sm text-slate-500">Assessment yang tersedia di course mentoran.</p>
                </div>

                <div class="divide-y divide-slate-100">
                    @forelse($latestAssessments as $assessment)
                        <div class="px-5 py-4 transition hover:bg-slate-50">
                            <div class="text-sm font-medium text-slate-900">{{ $assessment->title }}</div>
                            <div class="mt-1 text-xs text-slate-500">
                                {{ $assessment->course?->title }} · {{ $assessment->course?->studyProgram?->title }}
                            </div>
                            <div class="mt-2 text-xs text-slate-400">
                                {{ $assessment->status }} · {{ $assessment->passing_grade }} pass grade
                            </div>
                        </div>
                    @empty
                        <div class="px-5 py-6 text-sm text-slate-500">
                            Belum ada assessment untuk course yang kamu mentori.
                        </div>
                    @endforelse
                </div>
            </div>
        </aside>
    </section>
</div>