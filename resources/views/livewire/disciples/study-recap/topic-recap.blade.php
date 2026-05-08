<div class="mx-auto max-w-7xl space-y-6 px-4 pb-10 sm:px-6 lg:px-8">
    <section class="overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-sm">
        <div class="space-y-6 p-6 lg:p-10">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div class="max-w-4xl space-y-3">
                    <div class="text-[11px] uppercase tracking-[0.35em] text-slate-400">
                        Topic Recap
                    </div>

                    <div class="flex flex-wrap items-center gap-3">
                        <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl lg:text-4xl">
                            {{ $course->title }}
                        </h1>

                        <a href="{{ route('mentor.study-recap.index') }}"
                           class="inline-flex h-10 items-center rounded-xl border border-slate-200 bg-white px-4 text-sm font-medium text-slate-700 transition hover:bg-slate-50">
                            Back to Study Recap
                        </a>
                    </div>

                    <p class="max-w-3xl text-sm leading-7 text-slate-600 lg:text-[15px]">
                        Fokus pada topic completion, material progress, dan attendance per topic agar evaluasi lebih detail dan mudah ditindaklanjuti.
                    </p>
                </div>

                <div class="flex flex-wrap gap-2 text-xs">
                    <span class="rounded-full bg-slate-100 px-3 py-1 text-slate-600">
                        {{ $topicRows->count() }} topics
                    </span>
                    <span class="rounded-full bg-slate-100 px-3 py-1 text-slate-600">
                        {{ $summary['students_count'] ?? 0 }} students
                    </span>
                    <span class="rounded-full bg-slate-100 px-3 py-1 text-slate-600">
                        Assessment: {{ $summary['assessment_status'] ?? 'Unknown' }}
                    </span>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
                <div class="rounded-2xl border bg-slate-50/70 p-4">
                    <div class="text-xs text-slate-500">Topic completion</div>
                    <div class="mt-1 text-2xl font-bold text-slate-900">{{ $summary['topic_completion_rate'] ?? 0 }}%</div>
                </div>

                <div class="rounded-2xl border bg-slate-50/70 p-4">
                    <div class="text-xs text-slate-500">Material completion</div>
                    <div class="mt-1 text-2xl font-bold text-slate-900">{{ $summary['material_completion_rate'] ?? 0 }}%</div>
                </div>

                <div class="rounded-2xl border bg-slate-50/70 p-4">
                    <div class="text-xs text-slate-500">Attendance</div>
                    <div class="mt-1 text-2xl font-bold text-slate-900">{{ $summary['attendance_rate'] ?? 0 }}%</div>
                </div>

                <div class="rounded-2xl border bg-slate-50/70 p-4">
                    <div class="text-xs text-slate-500">Assessment Avg</div>
                    <div class="mt-1 text-2xl font-bold text-slate-900">
                        {{ $summary['assessment_avg_score'] !== null ? rtrim(rtrim(number_format($summary['assessment_avg_score'], 1, '.', ''), '0'), '.') : '-' }}
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
        @forelse($topicRows as $topic)
            <div wire:key="topic-recap-{{ $topic['id'] }}" class="space-y-4 rounded-[24px] border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-start justify-between gap-4">
                    <div class="min-w-0">
                        <h3 class="break-words text-lg font-semibold text-slate-900">
                            {{ $topic['name'] }}
                        </h3>
                        <p class="mt-1 break-words text-sm leading-6 text-slate-500">
                            {{ $topic['description'] }}
                        </p>
                    </div>

                    <span class="shrink-0 rounded-full border bg-slate-50 px-3 py-1 text-[11px] text-slate-700">
                        {{ $topic['course_assessment_status'] }}
                    </span>
                </div>

                <div class="grid grid-cols-2 gap-3 text-sm">
                    <div class="rounded-2xl border bg-slate-50/70 p-4">
                        <div class="text-xs text-slate-500">Topic completion</div>
                        <div class="mt-1 text-xl font-bold text-slate-900">{{ $topic['topic_completion_rate'] }}%</div>
                    </div>

                    <div class="rounded-2xl border bg-slate-50/70 p-4">
                        <div class="text-xs text-slate-500">Material completion</div>
                        <div class="mt-1 text-xl font-bold text-slate-900">{{ $topic['material_completion_rate'] }}%</div>
                    </div>

                    <div class="rounded-2xl border bg-slate-50/70 p-4">
                        <div class="text-xs text-slate-500">Attendance</div>
                        <div class="mt-1 text-xl font-bold text-slate-900">{{ $topic['attendance_rate'] }}%</div>
                    </div>

                    <div class="rounded-2xl border bg-slate-50/70 p-4">
                        <div class="text-xs text-slate-500">Completed Students</div>
                        <div class="mt-1 text-xl font-bold text-slate-900">{{ $topic['completed_students'] }}</div>
                    </div>
                </div>

                <div class="space-y-2">
                    <div class="flex items-center justify-between text-xs text-slate-500">
                        <span>Topic completion</span>
                        <span>{{ $topic['topic_completion_rate'] }}%</span>
                    </div>
                    <div class="h-2 overflow-hidden rounded-full bg-slate-100">
                        <div class="h-2 rounded-full bg-slate-900" style="width: {{ $topic['topic_completion_rate'] }}%"></div>
                    </div>
                </div>

                <div class="text-xs leading-6 text-slate-500">
                    Materials: {{ $topic['materials_count'] }} · Sessions: {{ $topic['sessions_count'] }}
                </div>
            </div>
        @empty
            <x-ui.empty-state
                title="No topic data"
                description="Course ini belum memiliki topic yang bisa direkap."
            />
        @endforelse
    </section>
</div>