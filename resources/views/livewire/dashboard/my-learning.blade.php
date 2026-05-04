<div class="space-y-6 lg:px-36">
    <x-ui.page-header
        title="My Learning"
        subtitle="Halaman khusus untuk progres kursus yang sedang kamu ikuti."
    >
        <a href="{{ route('courses.index') }}" class="px-4 py-2 rounded-xl bg-slate-900 text-white text-sm">
            Open Catalog
        </a>
    </x-ui.page-header>

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        <div class="rounded-2xl bg-white border p-5">
            <p class="text-sm text-slate-500">Courses Enrolled</p>
            <p class="text-3xl font-bold mt-2">{{ $summary['courses_enrolled'] ?? 0 }}</p>
        </div>

        <div class="rounded-2xl bg-white border p-5">
            <p class="text-sm text-slate-500">Topics Completed</p>
            <p class="text-3xl font-bold mt-2">{{ $summary['topics_completed'] ?? 0 }}</p>
        </div>

        <div class="rounded-2xl bg-white border p-5">
            <p class="text-sm text-slate-500">Certificates</p>
            <p class="text-3xl font-bold mt-2">{{ $summary['certificates'] ?? 0 }}</p>
        </div>

        <div class="rounded-2xl bg-slate-900 text-white p-5">
            <p class="text-sm text-slate-300">Role</p>
            <p class="text-2xl font-semibold mt-2">{{ ucfirst(session('active_role') ?? 'student') }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <section class="xl:col-span-2 space-y-4">
            <div class="flex items-end justify-between">
                <div>
                    <h2 class="text-lg font-semibold">Courses in progress</h2>
                    <p class="text-sm text-slate-500">Track each enrolled course.</p>
                </div>
            </div>

            <div class="space-y-4">
                @forelse($enrollments as $row)
                    <div class="rounded-2xl bg-white border p-5">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <h3 class="font-semibold">{{ $row['enrollment']->course?->title }}</h3>
                                <p class="text-sm text-slate-500">{{ $row['enrollment']->course?->studyProgram?->title }}</p>
                            </div>
                            <span class="text-sm font-semibold">{{ $row['percent'] }}%</span>
                        </div>

                        <div class="mt-4 h-2 rounded-full bg-slate-100 overflow-hidden">
                            <div class="h-2 rounded-full bg-slate-900" style="width: {{ $row['percent'] }}%"></div>
                        </div>

                        <div class="mt-3 text-sm text-slate-500">
                            {{ $row['completedTopics'] }} / {{ $row['totalTopics'] }} topics completed
                        </div>

                        <div class="mt-4 flex flex-wrap gap-2">
                            <a href="{{ route('courses.show', $row['enrollment']->course?->slug) }}"
                               class="inline-flex px-4 py-2 rounded-xl bg-slate-900 text-white text-sm">
                                Continue
                            </a>
                            <a href="{{ route('topics.show', $row['enrollment']->course?->topics->first()?->slug) }}"
                               class="inline-flex px-4 py-2 rounded-xl border text-sm">
                                Open first topic
                            </a>
                        </div>
                    </div>
                @empty
                    <x-ui.empty-state
                        title="Belum ada course"
                        description="Kamu belum mengikuti course apa pun."
                        button-label="Browse Courses"
                        button-href="{{ route('courses.index') }}"
                    />
                @endforelse
            </div>
        </section>

        <aside class="space-y-4">
            <div class="rounded-2xl bg-white border p-5">
                <h2 class="font-semibold mb-3">Upcoming Sessions</h2>
                <div class="space-y-3">
                    @forelse($upcomingSessions as $session)
                        <div class="rounded-xl border p-3">
                            <div class="font-medium text-sm">{{ $session->topic?->name }}</div>
                            <div class="text-xs text-slate-500">{{ $session->start_at->format('d M Y, H:i') }}</div>
                        </div>
                    @empty
                        <div class="text-sm text-slate-500">Tidak ada sesi terjadwal.</div>
                    @endforelse
                </div>
            </div>

            <div class="rounded-2xl bg-white border p-5">
                <h2 class="font-semibold mb-3">Latest Certificates</h2>
                <div class="space-y-3">
                    @forelse($latestCertificates as $certificate)
                        <div class="rounded-xl border p-3">
                            <div class="text-sm font-medium">{{ $certificate->certificate_number }}</div>
                            <div class="text-xs text-slate-500">{{ ucfirst($certificate->type) }}</div>
                        </div>
                    @empty
                        <div class="text-sm text-slate-500">Belum ada sertifikat.</div>
                    @endforelse
                </div>
            </div>
        </aside>
    </div>
</div>