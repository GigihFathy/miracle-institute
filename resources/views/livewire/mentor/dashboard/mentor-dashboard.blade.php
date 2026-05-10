<div class="space-y-6 lg:px-36">
    <x-ui.page-header
        title="Mentor Dashboard"
        subtitle="Ringkasan singkat untuk mengelola topic, material, dan progres pembelajaran."
    />

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-2xl border bg-white p-5">
            <div class="text-xs uppercase tracking-wide text-slate-500">Topics</div>
            <div class="mt-2 text-3xl font-bold text-slate-900">{{ $mentorTopicsCount }}</div>
            <div class="mt-1 text-sm text-slate-500">Topik yang kamu kelola</div>
        </div>

        <div class="rounded-2xl border bg-white p-5">
            <div class="text-xs uppercase tracking-wide text-slate-500">Materials</div>
            <div class="mt-2 text-3xl font-bold text-slate-900">{{ $mentorMaterialsCount }}</div>
            <div class="mt-1 text-sm text-slate-500">Materi yang kamu upload</div>
        </div>

        <div class="rounded-2xl border bg-white p-5">
            <div class="text-xs uppercase tracking-wide text-slate-500">Students</div>
            <div class="mt-2 text-3xl font-bold text-slate-900">{{ $mentorStudentsCount }}</div>
            <div class="mt-1 text-sm text-slate-500">Siswa yang terhubung</div>
        </div>

        <div class="rounded-2xl border bg-white p-5">
            <div class="text-xs uppercase tracking-wide text-slate-500">Assessments</div>
            <div class="mt-2 text-3xl font-bold text-slate-900">{{ $mentorAssessmentsCount }}</div>
            <div class="mt-1 text-sm text-slate-500">Assessment pada course</div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-[1.2fr_0.8fr]">
        <section class="rounded-2xl border bg-white p-5">
            <div class="mb-4 flex items-end justify-between gap-4">
                <div>
                    <h2 class="text-lg font-semibold">Managed Topics</h2>
                    <p class="text-sm text-slate-500">Daftar topic yang bisa kamu kelola.</p>
                </div>

                <a href="{{ route('mentor.topics.index') }}" class="text-sm font-medium text-slate-900 hover:underline">
                    View all
                </a>
            </div>

            <div class="space-y-4">
                @forelse($topicsByCourse as $courseTopics)
                    @php
                        $course = $courseTopics->first()->course;
                    @endphp

                    <div class="rounded-2xl border p-4">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <div class="text-sm font-semibold text-slate-900">{{ $course?->title ?? 'No Course' }}</div>
                                <div class="mt-1 text-xs text-slate-500">
                                    {{ $course?->studyProgram?->title ?? '-' }} · {{ $courseTopics->count() }} topic
                                </div>
                            </div>

                            <span class="rounded-full border px-2 py-1 text-xs text-slate-600">
                                Active
                            </span>
                        </div>

                        <div class="mt-4 space-y-2">
                            @foreach($courseTopics->take(3) as $topic)
                                <div class="flex items-center justify-between gap-3 rounded-xl bg-slate-50 px-4 py-3">
                                    <div class="min-w-0">
                                        <div class="truncate text-sm font-medium text-slate-900">{{ $topic->name }}</div>
                                        <div class="text-xs text-slate-500">{{ ucfirst($topic->status) }}</div>
                                    </div>

                                    <a href="{{ route('mentor.topics.show', $topic->slug) }}"
                                       class="shrink-0 rounded-xl bg-slate-900 px-3 py-2 text-xs font-medium text-white hover:bg-slate-700">
                                        Manage
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <div class="rounded-2xl border border-dashed p-6 text-sm text-slate-500">
                        Belum ada topic yang kamu kelola.
                    </div>
                @endforelse
            </div>
        </section>

        <section class="rounded-2xl border bg-white p-5">
            <div class="mb-4">
                <h2 class="text-lg font-semibold">Recent Materials</h2>
                <p class="text-sm text-slate-500">Materi terakhir yang kamu tambahkan.</p>
            </div>

            <div class="divide-y">
                @forelse($latestMaterials as $material)
                    <div class="py-4">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <div class="truncate text-sm font-medium text-slate-900">{{ $material->name }}</div>
                                <div class="mt-1 truncate text-xs text-slate-500">
                                    {{ $material->topic?->course?->title }} · {{ $material->topic?->name }} · {{ strtoupper($material->type) }}
                                </div>
                            </div>

                            <span class="shrink-0 rounded-full border px-2 py-1 text-[11px] uppercase tracking-wide text-slate-600">
                                {{ $material->status }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="py-6 text-sm text-slate-500">Belum ada materi.</div>
                @endforelse
            </div>
        </section>
    </div>
</div>