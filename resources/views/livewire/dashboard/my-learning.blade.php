<div class="space-y-8 lg:px-36 pb-10">

    <section class="rounded-3xl bg-white border overflow-hidden shadow-sm">
        <div>
            <div class="p-8 sm:p-10 space-y-6">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <div class="text-xs uppercase tracking-[0.3em] text-slate-400">
                            My Learning
                        </div>
                        <h1 class="text-3xl sm:text-4xl font-bold mt-2 tracking-tight">
                            Learning Dashboard
                        </h1>
                        <p class="text-slate-600 mt-3 max-w-3xl leading-7">
                            Rekap progres belajar, aktivitas terbaru, dan agenda sesi dalam satu tampilan yang terstruktur.
                        </p>
                    </div>

                    <a href="{{ route('courses.index') }}"
                       class="px-4 py-2 rounded-xl bg-slate-900 text-white text-sm">
                        Open Catalog
                    </a>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    <div class="rounded-2xl border bg-slate-50 p-4">
                        <div class="text-xs text-slate-500">Enrolled</div>
                        <div class="text-2xl font-bold mt-1">{{ $summary['courses_enrolled'] ?? 0 }}</div>
                    </div>
                    <div class="rounded-2xl border bg-slate-50 p-4">
                        <div class="text-xs text-slate-500">Topics Done</div>
                        <div class="text-2xl font-bold mt-1">{{ $summary['topics_completed'] ?? 0 }}</div>
                    </div>
                    <div class="rounded-2xl border bg-slate-50 p-4">
                        <div class="text-xs text-slate-500">Certificates</div>
                        <div class="text-2xl font-bold mt-1">{{ $summary['certificates'] ?? 0 }}</div>
                    </div>
                    <div class="rounded-2xl border bg-slate-50 p-4">
                        <div class="text-xs text-slate-500">Progress</div>
                        <div class="text-2xl font-bold mt-1">{{ $overallProgress }}%</div>
                    </div>
                </div>

                <div class="rounded-2xl border bg-slate-50 p-5 space-y-3">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <div class="text-xs uppercase tracking-[0.2em] text-slate-400">
                                Time-relevant snapshot
                            </div>
                            <div class="text-lg font-semibold mt-1">
                                {{ $nowLabel }}
                            </div>
                        </div>

                        <div class="rounded-xl border bg-white px-4 py-2 text-sm text-slate-600">
                            {{ $todaySessionsCount }} session(s) today
                        </div>
                    </div>

                    <div class="h-2 bg-slate-200 rounded-full overflow-hidden">
                        <div class="h-2 bg-slate-900 rounded-full" style="width: {{ $overallProgress }}%"></div>
                    </div>

                    <div class="flex items-center justify-between text-sm text-slate-600">
                        <span>{{ $overallProgress }}% overall progress</span>
                        <span>{{ $summary['topics_completed'] ?? 0 }} completed topic(s)</span>
                    </div>
                </div>

                @if($recommendedCourse)
                    <div class="rounded-3xl border bg-slate-950 text-white p-6 space-y-4">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <div class="text-xs uppercase tracking-[0.2em] text-slate-400">
                                    Continue learning
                                </div>
                                <h2 class="text-2xl font-bold mt-2">
                                    {{ $recommendedCourse['enrollment']->course?->title }}
                                </h2>
                                <p class="text-sm text-slate-300 mt-2 max-w-2xl leading-6">
                                    {{ $recommendedCourse['enrollment']->course?->studyProgram?->title }}
                                </p>
                            </div>

                            <span class="px-3 py-1 rounded-full text-xs bg-white/10 border border-white/10 whitespace-nowrap">
                                {{ $recommendedCourse['statusLabel'] }}
                            </span>
                        </div>

                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                            <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                                <div class="text-xs text-slate-400">Progress</div>
                                <div class="text-2xl font-bold mt-1">{{ $recommendedCourse['percent'] }}%</div>
                            </div>

                            <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                                <div class="text-xs text-slate-400">Completed</div>
                                <div class="text-2xl font-bold mt-1">{{ $recommendedCourse['completedTopics'] }}</div>
                            </div>

                            <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                                <div class="text-xs text-slate-400">Total Topics</div>
                                <div class="text-2xl font-bold mt-1">{{ $recommendedCourse['totalTopics'] }}</div>
                            </div>

                            <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                                <div class="text-xs text-slate-400">Last Activity</div>
                                <div class="text-sm font-semibold mt-1">
                                    {{ $recommendedCourse['lastActivityAt']?->diffForHumans() ?? 'No activity yet' }}
                                </div>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <div class="flex items-center justify-between text-xs text-slate-400">
                                <span>Progress</span>
                                <span>{{ $recommendedCourse['percent'] }}%</span>
                            </div>
                            <div class="h-2 bg-white/10 rounded-full overflow-hidden">
                                <div class="h-2 bg-emerald-400 rounded-full" style="width: {{ $recommendedCourse['percent'] }}%"></div>
                            </div>
                        </div>

                        <div class="flex flex-wrap gap-3">
                            <a href="{{ $recommendedCourse['continueUrl'] }}"
                               class="px-5 py-3 rounded-xl bg-white text-slate-950 text-sm font-semibold">
                                Continue
                            </a>

                            @if($recommendedCourse['nextTopic'])
                                <a href="{{ route('topics.show', $recommendedCourse['nextTopic']->slug) }}"
                                   class="px-5 py-3 rounded-xl border border-white/15 text-white text-sm">
                                    Open next topic
                                </a>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            {{-- <div class="bg-slate-100 p-8 flex items-center justify-center">
                <div class="w-full rounded-3xl overflow-hidden shadow-sm border bg-white">
                    <img src="{{ asset('images/logo.png') }}"
                        class="w-full h-auto object-contain"
                        alt="Learning dashboard illustration">
                </div>
            </div> --}}
        </div>
    </section>

    <section class="grid xl:grid-cols-[1.35fr_0.65fr] gap-6">
        <div class="space-y-6">
            <div class="rounded-3xl bg-white border p-6 shadow-sm">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-semibold">Courses in Progress</h2>
                        <p class="text-sm text-slate-500 mt-1">
                            Pantau semua course yang sedang berjalan dan lanjutkan dari titik terakhir.
                        </p>
                    </div>
                    <div class="rounded-xl border bg-slate-50 px-3 py-2 text-sm text-slate-600">
                        {{ $enrollmentCards->count() }} course(s)
                    </div>
                </div>

                <div class="mt-6 space-y-4">
                    @forelse($enrollmentCards as $row)
                        <div class="rounded-3xl border bg-slate-50 p-5 space-y-4">
                            <div class="flex flex-wrap items-start justify-between gap-4">
                                <div>
                                    <h3 class="font-semibold text-lg leading-tight">
                                        {{ $row['enrollment']->course?->title }}
                                    </h3>
                                    <p class="text-sm text-slate-500 mt-1">
                                        {{ $row['enrollment']->course?->studyProgram?->title }}
                                    </p>
                                </div>

                                <span class="px-3 py-1 rounded-full text-xs bg-white border">
                                    {{ $row['statusLabel'] }}
                                </span>
                            </div>

                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-sm">
                                <div class="rounded-2xl border bg-white p-4">
                                    <div class="text-xs text-slate-500">Progress</div>
                                    <div class="font-semibold mt-1">{{ $row['percent'] }}%</div>
                                </div>
                                <div class="rounded-2xl border bg-white p-4">
                                    <div class="text-xs text-slate-500">Completed</div>
                                    <div class="font-semibold mt-1">{{ $row['completedTopics'] }}</div>
                                </div>
                                <div class="rounded-2xl border bg-white p-4">
                                    <div class="text-xs text-slate-500">In Progress</div>
                                    <div class="font-semibold mt-1">{{ $row['inProgressTopics'] }}</div>
                                </div>
                                <div class="rounded-2xl border bg-white p-4">
                                    <div class="text-xs text-slate-500">Total</div>
                                    <div class="font-semibold mt-1">{{ $row['totalTopics'] }}</div>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <div class="flex items-center justify-between text-xs text-slate-500">
                                    <span>Course progress</span>
                                    <span>{{ $row['percent'] }}%</span>
                                </div>
                                <div class="h-2 bg-white rounded-full overflow-hidden">
                                    <div class="h-2 bg-slate-900 rounded-full" style="width: {{ $row['percent'] }}%"></div>
                                </div>
                            </div>

                            <div class="flex flex-wrap gap-2">
                                <a href="{{ route('courses.show', $row['enrollment']->course?->slug) }}"
                                   class="inline-flex px-4 py-2 rounded-xl bg-slate-900 text-white text-sm">
                                    Open course
                                </a>

                                <a href="{{ $row['continueUrl'] }}"
                                   class="inline-flex px-4 py-2 rounded-xl border text-sm">
                                    Continue learning
                                </a>

                                @if($row['nextTopic'])
                                    <span class="inline-flex px-4 py-2 rounded-xl border text-sm text-slate-500">
                                        Next: {{ $row['nextTopic']->name }}
                                    </span>
                                @endif
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
            </div>

            <div class="rounded-3xl bg-white border p-6 shadow-sm">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-semibold">Activity Timeline</h2>
                        <p class="text-sm text-slate-500 mt-1">
                            Rekap aktivitas terbaru yang disusun berdasarkan waktu.
                        </p>
                    </div>
                </div>

                <div class="mt-6 space-y-3">
                    @forelse($activityFeed as $item)
                        <a href="{{ $item['link'] }}"
                           class="block rounded-2xl border bg-slate-50 p-4 hover:bg-slate-100 transition">
                            <div class="flex items-start justify-between gap-4">
                                <div class="space-y-1">
                                    <div class="font-semibold">{{ $item['title'] }}</div>
                                    <div class="text-sm text-slate-500">{{ $item['subtitle'] }}</div>
                                </div>

                                <span class="px-2 py-1 rounded-full text-[11px]
                                    @if($item['tone'] === 'emerald') bg-emerald-100 text-emerald-700
                                    @elseif($item['tone'] === 'blue') bg-blue-100 text-blue-700
                                    @else bg-amber-100 text-amber-700
                                    @endif">
                                    {{ $item['time']?->diffForHumans() }}
                                </span>
                            </div>
                        </a>
                    @empty
                        <x-ui.empty-state
                            title="No recent activity"
                            description="Aktivitas terbaru akan muncul di sini setelah kamu mulai belajar."
                        />
                    @endforelse
                </div>
            </div>
        </div>

        <aside class="space-y-6">
            <div class="rounded-3xl bg-white border p-6 shadow-sm">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-semibold">Upcoming Sessions</h2>
                        <p class="text-sm text-slate-500 mt-1">
                            Sesi yang paling dekat dengan waktu sekarang.
                        </p>
                    </div>

                    <div class="rounded-xl border bg-slate-50 px-3 py-2 text-sm text-slate-600">
                        {{ $upcomingSessions->count() }} upcoming
                    </div>
                </div>

                <div class="mt-6 space-y-3">
                    @forelse($upcomingSessions as $session)
                        <a href="{{ route('topics.show', $session->topic?->slug) }}"
                           class="block rounded-2xl border bg-slate-50 p-4 hover:bg-slate-100 transition">
                            <div class="font-medium text-sm">{{ $session->title }}</div>
                            <div class="text-xs text-slate-500 mt-1">
                                {{ $session->topic?->course?->title }} · {{ $session->topic?->name }}
                            </div>
                            <div class="text-xs text-slate-400 mt-2">
                                {{ $session->start_at->format('d M Y, H:i') }} - {{ $session->end_at->format('H:i') }}
                            </div>
                        </a>
                    @empty
                        <x-ui.empty-state
                            title="Tidak ada sesi terjadwal"
                            description="Sesi yang relevan dengan course kamu akan tampil di sini."
                        />
                    @endforelse
                </div>
            </div>

            <div class="rounded-3xl bg-white border p-6 shadow-sm">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-semibold">Latest Certificates</h2>
                        <p class="text-sm text-slate-500 mt-1">
                            Rekap sertifikat terbaru yang kamu peroleh.
                        </p>
                    </div>
                </div>

                <div class="mt-6 space-y-3">
                    @forelse($latestCertificates as $certificate)
                        <a href="{{ route('certificates.download', $certificate->id) }}"
                           class="block rounded-2xl border bg-slate-50 p-4 hover:bg-slate-100 transition">
                            <div class="font-medium text-sm">{{ $certificate->certificate_number }}</div>
                            <div class="text-xs text-slate-500 mt-1">
                                {{ ucfirst($certificate->type) }}
                            </div>
                            <div class="text-xs text-slate-400 mt-2">
                                {{ $certificate->created_at?->format('d M Y, H:i') }}
                            </div>
                        </a>
                    @empty
                        <x-ui.empty-state
                            title="Belum ada sertifikat"
                            description="Sertifikat yang kamu terima akan muncul di sini."
                        />
                    @endforelse
                </div>
            </div>
        </aside>
    </section>

</div>