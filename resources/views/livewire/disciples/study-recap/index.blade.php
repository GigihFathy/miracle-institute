<div class="mx-auto max-w-7xl space-y-6 px-4 pb-10 sm:px-6 lg:px-8">
    <section class="overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-sm">
        <div class="space-y-6 p-6 lg:p-10">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                <div class="max-w-4xl space-y-3">
                    <div class="text-[11px] uppercase tracking-[0.35em] text-slate-400">
                        Study Recap
                    </div>

                    <div class="flex flex-wrap items-center gap-3">
                        <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl lg:text-4xl">
                            Student Study Recap
                        </h1>

                        <a href="{{ url()->previous() }}"
                           class="inline-flex h-10 items-center rounded-xl border border-slate-200 bg-white px-4 text-sm font-medium text-slate-700 transition hover:bg-slate-50">
                            Back
                        </a>
                    </div>

                    <p class="max-w-3xl text-sm leading-7 text-slate-600 lg:text-[15px]">
                        Dashboard ringkasan untuk memantau progres student pada level course.
                        Data kursus, topic, dan student telah dipisah agar tiap halaman bisa tampil lebih fokus dan mudah dibaca.
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 lg:grid-cols-3 2xl:grid-cols-6">
                @php
                    $overallCards = [
                        [
                            'label' => 'Courses Monitored',
                            'value' => number_format($overall['courses_count'] ?? 0),
                            'note' => 'Course yang memiliki topic di bawah pengawasan mentor.',
                        ],
                        [
                            'label' => 'Topics Monitored',
                            'value' => number_format($overall['topics_count'] ?? 0),
                            'note' => 'Total topic aktif yang terhubung ke course mentor.',
                        ],
                        [
                            'label' => 'Students',
                            'value' => number_format($overall['students_count'] ?? 0),
                            'note' => 'Peserta yang terdaftar pada course terkait.',
                        ],
                        [
                            'label' => 'Topic Completion',
                            'value' => ($overall['topic_completion_rate'] ?? 0) . '%',
                            'note' => 'Completed topic progress dibanding total progress yang seharusnya ada.',
                        ],
                        [
                            'label' => 'Material Completion',
                            'value' => ($overall['material_completion_rate'] ?? 0) . '%',
                            'note' => 'Persentase material completed pada seluruh siswa terkait.',
                        ],
                        [
                            'label' => 'Attendance Rate',
                            'value' => ($overall['attendance_rate'] ?? 0) . '%',
                            'note' => 'Present atau late dibanding total attendance record yang diharapkan.',
                        ],
                    ];
                @endphp

                @foreach($overallCards as $card)
                    <div class="min-w-0 rounded-2xl border border-slate-200 bg-slate-50/70 p-4">
                        <div class="text-xs text-slate-500">
                            {{ $card['label'] }}
                        </div>

                        <div class="mt-2 break-words text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">
                            {{ $card['value'] }}
                        </div>

                        <div class="mt-1 break-words text-xs leading-5 text-slate-500">
                            {{ $card['note'] }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <div class="rounded-2xl border border-slate-200 bg-slate-50/70 p-4">
        <div class="grid grid-cols-1 gap-3 md:grid-cols-2 xl:grid-cols-4">
            <input
                wire:model.live.debounce.300ms="search"
                type="text"
                placeholder="Search course or topic..."
                class="h-11 rounded-xl border-slate-200 text-sm focus:border-slate-900 focus:ring-slate-900"
            >

            <select
                wire:model.live="courseFilter"
                class="h-11 rounded-xl border-slate-200 text-sm focus:border-slate-900 focus:ring-slate-900">
                <option value="">All courses</option>
                @foreach($courseOptions as $courseOption)
                    <option value="{{ $courseOption->id }}">
                        {{ $courseOption->title }}
                    </option>
                @endforeach
            </select>

            <select
                wire:model.live="statusFilter"
                class="h-11 rounded-xl border-slate-200 text-sm focus:border-slate-900 focus:ring-slate-900">
                <option value="">All status</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>

            <select
                wire:model.live="perPage"
                class="h-11 rounded-xl border-slate-200 text-sm focus:border-slate-900 focus:ring-slate-900">
                <option value="6">6 / page</option>
                <option value="12">12 / page</option>
                <option value="24">24 / page</option>
            </select>
        </div>
    </div>

    <section class="min-w-0 space-y-5">
        @if($rows->count() === 0)
            <x-ui.empty-state
                title="No course found"
                description="Tidak ada course yang cocok dengan filter saat ini."
            />
        @else
            <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
                @foreach($rows as $row)
                    <div
                        wire:key="recap-course-{{ $row->id }}"
                        class="group overflow-hidden rounded-[26px] border border-slate-200 bg-white shadow-sm transition-all duration-300 hover:-translate-y-0.5 hover:shadow-lg {{ $selectedCourseId === (string) $row->id ? 'ring-2 ring-slate-900' : '' }}">
                        <button
                            type="button"
                            wire:click="selectCourse('{{ $row->id }}')"
                            aria-pressed="{{ $selectedCourseId === (string) $row->id ? 'true' : 'false' }}"
                            class="block w-full text-left">
                            <div class="space-y-5 p-5">
                                <div class="flex min-w-0 items-start justify-between gap-4">
                                    <div class="min-w-0">
                                        <div class="truncate text-[11px] uppercase tracking-[0.25em] text-slate-400">
                                            {{ $row->studyProgram?->title }}
                                        </div>

                                        <h3 class="mt-1 line-clamp-2 text-lg font-semibold leading-tight text-slate-900 sm:text-xl">
                                            {{ $row->title }}
                                        </h3>

                                        <p class="mt-2 line-clamp-3 text-sm leading-6 text-slate-600">
                                            {{ $row->description }}
                                        </p>
                                    </div>

                                    <span class="shrink-0 rounded-full border border-slate-200 bg-slate-100 px-3 py-1 text-[11px] text-slate-700">
                                        {{ ucfirst($row->status) }}
                                    </span>
                                </div>

                                <div class="grid grid-cols-2 gap-2 text-xs">
                                    <div class="rounded-xl border bg-slate-50 p-3">
                                        <div class="text-slate-500">Topics</div>
                                        <div class="mt-1 text-lg font-bold text-slate-900">{{ $row->topics_count }}</div>
                                    </div>

                                    <div class="rounded-xl border bg-slate-50 p-3">
                                        <div class="text-slate-500">Students</div>
                                        <div class="mt-1 text-lg font-bold text-slate-900">{{ $row->enrollments_count }}</div>
                                    </div>

                                    <div class="rounded-xl border bg-slate-50 p-3">
                                        <div class="text-slate-500">Assessment</div>
                                        <div class="mt-1 text-lg font-bold text-slate-900">
                                            {{ $row->assessment_avg_score !== null ? rtrim(rtrim(number_format($row->assessment_avg_score, 1, '.', ''), '0'), '.') : '-' }}
                                        </div>
                                    </div>

                                    <div class="rounded-xl border bg-slate-50 p-3">
                                        <div class="text-slate-500">Attendance</div>
                                        <div class="mt-1 text-lg font-bold text-slate-900">{{ $row->attendance_rate }}%</div>
                                    </div>
                                </div>

                                <div class="space-y-3">
                                    <div>
                                        <div class="mb-1 flex items-center justify-between text-xs text-slate-500">
                                            <span>Topic completion</span>
                                            <span>{{ $row->topic_completion_rate }}%</span>
                                        </div>
                                        <div class="h-2 overflow-hidden rounded-full bg-slate-100">
                                            <div class="h-2 rounded-full bg-slate-900" style="width: {{ $row->topic_completion_rate }}%"></div>
                                        </div>
                                    </div>

                                    <div>
                                        <div class="mb-1 flex items-center justify-between text-xs text-slate-500">
                                            <span>Material completion</span>
                                            <span>{{ $row->material_completion_rate }}%</span>
                                        </div>
                                        <div class="h-2 overflow-hidden rounded-full bg-slate-100">
                                            <div class="h-2 rounded-full bg-blue-500" style="width: {{ $row->material_completion_rate }}%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </button>

                        <div class="flex items-center justify-between gap-3 border-t bg-slate-50/70 px-5 py-4">
                            <div class="text-xs text-slate-500">
                                {{ $row->assessment_status }} · {{ $row->enrollments_count }} students
                            </div>

                            <div class="text-xs font-medium text-slate-700">
                                Open recap →
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="pt-2">
                {{ $rows->links() }}
            </div>
        @endif
    </section>

    <x-ui.studio-modal
        show="showModal"
        title="Selected Course"
        description="Rekap course terpilih. Topic Recap dan Student Recap sudah dipisahkan ke halaman masing-masing."
        maxWidth="max-w-4xl"
    >
        @if($selectedCourse)
            <div class="space-y-6">
                <div class="grid gap-4 lg:grid-cols-[minmax(0,1.4fr)_minmax(0,0.9fr)]">
                    <div class="rounded-2xl border border-slate-200 bg-slate-50/70 p-5">
                        <div class="text-[11px] uppercase tracking-[0.25em] text-slate-400">
                            Selected Course
                        </div>

                        <h2 class="mt-2 break-words text-2xl font-bold tracking-tight text-slate-900">
                            {{ $selectedCourse->title }}
                        </h2>

                        <p class="mt-2 text-sm leading-6 text-slate-500">
                            {{ $selectedCourse->studyProgram?->title }}
                        </p>

                        <p class="mt-4 text-sm leading-7 text-slate-600">
                            {{ $selectedCourse->description }}
                        </p>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div class="rounded-2xl border bg-white p-4">
                            <div class="text-xs text-slate-500">Topics</div>
                            <div class="mt-1 text-2xl font-bold text-slate-900">
                                {{ $selectedCourseSummary['topics_count'] ?? $selectedCourse->topics_count }}
                            </div>
                        </div>

                        <div class="rounded-2xl border bg-white p-4">
                            <div class="text-xs text-slate-500">Students</div>
                            <div class="mt-1 text-2xl font-bold text-slate-900">
                                {{ $selectedCourseSummary['students_count'] ?? $selectedCourse->enrollments_count }}
                            </div>
                        </div>

                        <div class="rounded-2xl border bg-white p-4">
                            <div class="text-xs text-slate-500">Assessment Avg</div>
                            <div class="mt-1 text-2xl font-bold text-slate-900">
                                {{ $selectedCourseSummary['assessment_avg_score'] !== null ? rtrim(rtrim(number_format($selectedCourseSummary['assessment_avg_score'], 1, '.', ''), '0'), '.') : '-' }}
                            </div>
                        </div>

                        <div class="rounded-2xl border bg-white p-4">
                            <div class="text-xs text-slate-500">Attendance</div>
                            <div class="mt-1 text-2xl font-bold text-slate-900">
                                {{ $selectedCourseSummary['attendance_rate'] ?? 0 }}%
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid gap-3 md:grid-cols-3">
                    <div class="rounded-2xl border bg-white p-4">
                        <div class="mb-1 flex items-center justify-between text-xs text-slate-500">
                            <span>Topic completion</span>
                            <span>{{ $selectedCourseSummary['topic_completion_rate'] ?? 0 }}%</span>
                        </div>
                        <div class="h-2 overflow-hidden rounded-full bg-slate-100">
                            <div class="h-2 rounded-full bg-slate-900" style="width: {{ $selectedCourseSummary['topic_completion_rate'] ?? 0 }}%"></div>
                        </div>
                    </div>

                    <div class="rounded-2xl border bg-white p-4">
                        <div class="mb-1 flex items-center justify-between text-xs text-slate-500">
                            <span>Material completion</span>
                            <span>{{ $selectedCourseSummary['material_completion_rate'] ?? 0 }}%</span>
                        </div>
                        <div class="h-2 overflow-hidden rounded-full bg-slate-100">
                            <div class="h-2 rounded-full bg-blue-500" style="width: {{ $selectedCourseSummary['material_completion_rate'] ?? 0 }}%"></div>
                        </div>
                    </div>

                    <div class="rounded-2xl border bg-white p-4">
                        <div class="mb-1 flex items-center justify-between text-xs text-slate-500">
                            <span>Assessment pass rate</span>
                            <span>{{ $selectedCourseSummary['assessment_passed_rate'] !== null ? $selectedCourseSummary['assessment_passed_rate'] . '%' : '-' }}</span>
                        </div>
                        <div class="h-2 overflow-hidden rounded-full bg-slate-100">
                            <div class="h-2 rounded-full bg-emerald-500" style="width: {{ $selectedCourseSummary['assessment_passed_rate'] ?? 0 }}%"></div>
                        </div>
                    </div>
                </div>

                <div class="space-y-2 rounded-2xl border bg-slate-50 p-4">
                    <div class="text-sm font-semibold text-slate-900">Interpretation</div>
                    <div class="text-sm leading-6 text-slate-600">
                        Topic completion menunjukkan proporsi progress topic yang sudah selesai.
                        Material completion memakai status <span class="font-medium">completed</span>.
                        Attendance dihitung dari status <span class="font-medium">present</span> dan <span class="font-medium">late</span>.
                    </div>
                </div>

                <div class="rounded-2xl border bg-white p-4">
                    <div class="text-sm font-semibold text-slate-900">Open detailed recap</div>
                    <p class="mt-1 text-xs leading-6 text-slate-500">
                        Topic Recap dan Student Recap dipisahkan ke halaman masing-masing agar analisis lebih fokus.
                    </p>

                    <div class="mt-4 grid grid-cols-1 gap-2 sm:grid-cols-2">
                        <a href="{{ route('mentor.study-recap.topics', ['course' => $selectedCourse->id]) }}"
                           class="flex h-11 items-center justify-center rounded-xl bg-slate-900 px-4 text-sm font-medium text-white transition hover:bg-slate-800">
                            Open Topic Recap
                        </a>

                        <a href="{{ route('mentor.study-recap.students', ['course' => $selectedCourse->id]) }}"
                           class="flex h-11 items-center justify-center rounded-xl border border-slate-200 px-4 text-sm font-medium text-slate-700 transition hover:bg-slate-50">
                            Open Student Recap
                        </a>

                        <a href="{{ route('mentor.topics.index', ['courseFilter' => $selectedCourse->id]) }}"
                           class="flex h-11 items-center justify-center rounded-xl border border-slate-200 px-4 text-sm font-medium text-slate-700 transition hover:bg-slate-50">
                            Topics
                        </a>

                        <a href="{{ route('mentor.assessments.index', ['courseFilter' => $selectedCourse->id]) }}"
                           class="flex h-11 items-center justify-center rounded-xl border border-slate-200 px-4 text-sm font-medium text-slate-700 transition hover:bg-slate-50">
                            Assessment
                        </a>
                    </div>
                </div>
            </div>
        @else
            <x-ui.empty-state
                title="Select a course"
                description="Klik salah satu course card untuk melihat rekap yang lebih detail."
            />
        @endif

        <x-slot:footer>
            <div class="flex items-center justify-between gap-3">
                <button type="button"
                        wire:click="closeModal"
                        class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50">
                    Close
                </button>

                @if($selectedCourse)
                    <a href="{{ route('mentor.study-recap.topics', ['course' => $selectedCourse->id]) }}"
                       class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-slate-800">
                        Continue
                    </a>
                @endif
            </div>
        </x-slot:footer>
    </x-ui.studio-modal>
</div>