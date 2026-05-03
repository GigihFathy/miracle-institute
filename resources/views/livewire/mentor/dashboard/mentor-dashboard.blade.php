<div class="space-y-6">
    <x-ui.page-header
        title="Mentor Dashboard"
        subtitle="Mode Disciples: kamu tetap bisa belajar sebagai student, sambil mengelola pembelajaran untuk topic yang kamu mentori."
    >
        <a href="{{ route('mentor.topics.index') }}"
           class="px-4 py-2 rounded-xl bg-slate-900 text-white text-sm">
            Open Mentor Topics
        </a>
        <a href="{{ route('explore.dashboard') }}"
           class="px-4 py-2 rounded-xl border text-sm">
            Go to Student View
        </a>
    </x-ui.page-header>

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-6 gap-4">
        <div class="rounded-2xl bg-white border p-5">
            <div class="text-sm text-slate-500">Mentored Topics</div>
            <div class="text-3xl font-bold mt-2">{{ $mentorTopicsCount }}</div>
        </div>
        <div class="rounded-2xl bg-white border p-5">
            <div class="text-sm text-slate-500">Materials Created</div>
            <div class="text-3xl font-bold mt-2">{{ $mentorMaterialsCount }}</div>
        </div>
        <div class="rounded-2xl bg-white border p-5">
            <div class="text-sm text-slate-500">Students Reached</div>
            <div class="text-3xl font-bold mt-2">{{ $mentorStudentsCount }}</div>
        </div>
        <div class="rounded-2xl bg-white border p-5">
            <div class="text-sm text-slate-500">Assessments</div>
            <div class="text-3xl font-bold mt-2">{{ $mentorAssessmentsCount }}</div>
        </div>
        <div class="rounded-2xl bg-slate-900 text-white p-5">
            <div class="text-sm text-slate-300">My Courses</div>
            <div class="text-3xl font-bold mt-2">{{ $myCoursesCount }}</div>
        </div>
        <div class="rounded-2xl bg-slate-900 text-white p-5">
            <div class="text-sm text-slate-300">Certificates</div>
            <div class="text-3xl font-bold mt-2">{{ $myCertificatesCount }}</div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        <section class="rounded-2xl bg-white border p-5">
            <div class="flex items-end justify-between mb-4">
                <div>
                    <h2 class="font-semibold text-lg">Mentored Topics</h2>
                    <p class="text-sm text-slate-500">Topik yang bisa kamu kelola.</p>
                </div>
            </div>

            <div class="space-y-3">
                @forelse($latestTopics as $topic)
                    <div class="rounded-xl border p-4 flex items-start justify-between gap-4">
                        <div>
                            <div class="font-medium">{{ $topic->name }}</div>
                            <div class="text-xs text-slate-500">{{ $topic->course?->title }}</div>
                        </div>

                        <div class="flex gap-2">
                            <a href="{{ route('mentor.topics.show', $topic->slug) }}"
                               class="text-sm underline">
                                Workspace
                            </a>
                            <a href="{{ route('topics.show', $topic->slug) }}"
                               class="text-sm underline">
                                Student View
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="text-sm text-slate-500">Belum ada topic yang kamu mentor.</div>
                @endforelse
            </div>
        </section>

        <section class="rounded-2xl bg-white border p-5">
            <div class="flex items-end justify-between mb-4">
                <div>
                    <h2 class="font-semibold text-lg">Recent Materials</h2>
                    <p class="text-sm text-slate-500">Materi terbaru yang kamu tambahkan.</p>
                </div>
            </div>

            <div class="space-y-3">
                @forelse($latestMaterials as $material)
                    <div class="rounded-xl border p-4 flex items-start justify-between gap-4">
                        <div>
                            <div class="font-medium">{{ $material->name }}</div>
                            <div class="text-xs text-slate-500">
                                {{ $material->topic?->name }} · {{ strtoupper($material->type) }}
                            </div>
                        </div>
                        <span class="text-xs px-2 py-1 rounded-full bg-slate-100">
                            {{ $material->status }}
                        </span>
                    </div>
                @empty
                    <div class="text-sm text-slate-500">Belum ada materi yang kamu tambahkan.</div>
                @endforelse
            </div>
        </section>
    </div>

    @php
        $hasStudentRole = auth()->user()->roles->contains('name', 'student');
    @endphp

    @if($hasStudentRole)
    <section class="rounded-2xl bg-white border p-5">
        <div class="flex items-end justify-between mb-4">
            <div>
                <h2 class="font-semibold text-lg">My Learning Summary</h2>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="rounded-xl border p-4 bg-slate-50">
                <div class="text-xs text-slate-500">Enrolled Courses</div>
                <div class="text-2xl font-bold mt-2">{{ $myCoursesCount }}</div>
            </div>
            <div class="rounded-xl border p-4 bg-slate-50">
                <div class="text-xs text-slate-500">Topics Completed</div>
                <div class="text-2xl font-bold mt-2">{{ $myTopicsCompleted }}</div>
            </div>
            <div class="rounded-xl border p-4 bg-slate-50">
                <div class="text-xs text-slate-500">Certificates</div>
                <div class="text-2xl font-bold mt-2">{{ $myCertificatesCount }}</div>
            </div>
        </div>
    </section>
    @endif
</div>