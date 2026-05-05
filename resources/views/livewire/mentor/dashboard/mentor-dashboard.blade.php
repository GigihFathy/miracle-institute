<div class="space-y-6 lg:px-36">
    <x-ui.page-header
        title="Mentor Dashboard"
        subtitle="Mode Disciples: kamu tetap bisa belajar sebagai student, sambil mengelola pembelajaran untuk topic yang kamu mentori."
    />

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 overflow-hidden rounded-2xl border border-slate-200 bg-white">
        <div class="p-5">
            <div class="text-xs uppercase tracking-wide text-slate-500">Mentored Topics</div>
            <div class="mt-2 flex items-end gap-2">
                <div class="text-3xl font-bold text-slate-900">{{ $mentorTopicsCount }}</div>
                <div class="text-xs text-slate-500 pb-1">topics</div>
            </div>
        </div>
        <div class="border-t border-slate-200 p-5 sm:border-t-0 sm:border-l xl:border-l border-l-slate-200">
            <div class="text-xs uppercase tracking-wide text-slate-500">Materials Created</div>
            <div class="mt-2 flex items-end gap-2">
                <div class="text-3xl font-bold text-slate-900">{{ $mentorMaterialsCount }}</div>
                <div class="text-xs text-slate-500 pb-1">materials</div>
            </div>
        </div>
        <div class="border-t border-slate-200 p-5 sm:border-t-0 xl:border-l border-l-slate-200">
            <div class="text-xs uppercase tracking-wide text-slate-500">Students Reached</div>
            <div class="mt-2 flex items-end gap-2">
                <div class="text-3xl font-bold text-slate-900">{{ $mentorStudentsCount }}</div>
                <div class="text-xs text-slate-500 pb-1">students</div>
            </div>
        </div>
        <div class="border-t border-slate-200 p-5 sm:border-t-0 sm:border-l xl:border-l border-l-slate-200">
            <div class="text-xs uppercase tracking-wide text-slate-500">Assessments</div>
            <div class="mt-2 flex items-end gap-2">
                <div class="text-3xl font-bold text-slate-900">{{ $mentorAssessmentsCount }}</div>
                <div class="text-xs text-slate-500 pb-1">active</div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-[1.35fr_0.65fr] gap-6">
        <section class="rounded-2xl bg-white border p-5">
            <div class="flex items-end justify-between mb-4">
                <div>
                    <h2 class="font-semibold text-lg">Mentored Courses</h2>
                    <p class="text-sm text-slate-500">Buka course untuk melihat topic yang bisa kamu kelola.</p>
                </div>
            </div>

            <div class="space-y-4">
                @forelse($mentoredTopicsByCourse as $courseTopics)
                    @php
                        $course = $courseTopics->first()->course;
                        $courseTitle = $course?->title ?? 'No Course';
                        $courseImage = $course?->image;
                    @endphp

                    <div x-data="{ open: false }" class="rounded-2xl bg-white">
                        <button type="button"
                                @click="open = !open"
                                class="flex w-full cursor-pointer list-none items-center justify-between gap-4 px-0 py-2 text-left">
                            <div class="flex min-w-0 items-center gap-3">
                                <div class="aspect-video w-24 shrink-0 overflow-hidden rounded-xl bg-slate-100 ring-1 ring-inset ring-slate-200 sm:w-28">
                                    @if(!empty($courseImage))
                                        <img src="{{ asset('storage/' . $courseImage) }}" alt="{{ $courseTitle }}" class="h-full w-full object-cover">
                                    @else
                                        <div class="flex h-full w-full items-center justify-center bg-slate-200 text-xs font-semibold text-slate-500">
                                            {{ strtoupper(mb_substr($courseTitle, 0, 1)) }}
                                        </div>
                                    @endif
                                </div>

                                <div class="min-w-0">
                                    <div class="font-medium text-slate-900 truncate">{{ $courseTitle }}</div>
                                    <div class="text-xs text-slate-500 mt-1">
                                        {{ $course?->studyProgram?->title ?? 'No Study Program' }} · {{ $course?->credit ?? 0 }} credits
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center gap-3 text-xs text-slate-500">
                                <span class="hidden sm:inline">{{ $courseTopics->count() }} topic</span>
                                <svg :class="open ? 'rotate-180' : ''"
                                     class="h-4 w-4 transition-transform duration-300 ease-out"
                                     fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </button>

                        <div class="grid transition-all duration-300 ease-out" :class="open ? 'grid-rows-[1fr] opacity-100' : 'grid-rows-[0fr] opacity-0'">
                            <div class="overflow-hidden">
                                <div class="pt-2 pl-0">
                                    <div class="divide-y divide-slate-100">
                                        @foreach($courseTopics as $topic)
                                            <div class="flex items-center justify-between gap-4 px-0 py-3 hover:bg-slate-50 transition rounded-xl">
                                                <div class="min-w-0">
                                                    <div class="text-sm font-medium text-slate-900 truncate">{{ $topic->name }}</div>
                                                </div>

                                                <a href="{{ route('mentor.topics.show', $topic->slug) }}"
                                                   class="shrink-0 rounded-full bg-slate-900 px-3 py-1.5 text-xs font-medium text-white transition hover:bg-slate-700">
                                                    Manage
                                                </a>
                                            </div>
                                        @endforeach
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
        </section>

        <section class="rounded-2xl bg-white border overflow-hidden">
            <div class="px-5 pt-5 pb-3 border-b border-slate-100">
                <h2 class="font-semibold text-lg">History</h2>
                <p class="text-sm text-slate-500">Riwayat materi yang kamu tambahkan.</p>
            </div>

            <div class="divide-y divide-slate-100">
                @forelse($latestMaterials as $material)
                    <div class="px-5 py-4 flex items-start justify-between gap-3 hover:bg-slate-50 transition">
                        <div class="min-w-0">
                            <div class="font-medium text-sm text-slate-900 truncate">{{ $material->name }}</div>
                            <div class="text-xs text-slate-500 mt-1 truncate">
                                {{ $material->topic?->name }} · {{ strtoupper($material->type) }}
                            </div>
                        </div>
                        <span class="shrink-0 text-[11px] uppercase tracking-wide px-2 py-1 rounded-full border border-slate-200 bg-white text-slate-600">
                            {{ $material->status }}
                        </span>
                    </div>
                @empty
                    <div class="px-5 py-6 text-sm text-slate-500">Belum ada materi yang kamu tambahkan.</div>
                @endforelse
            </div>
        </section>
    </div>

    @php
        $hasStudentRole = auth()->user()->roles->contains('name', 'student');
    @endphp

    {{-- Tidak perlu ditampilkan --}}
    {{-- @if($hasStudentRole)
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
    @endif --}}
</div>