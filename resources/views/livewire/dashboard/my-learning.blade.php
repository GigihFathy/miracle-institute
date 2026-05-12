<div class="space-y-6 px-4 py-6 sm:px-6 lg:px-12 xl:px-36">
    <x-ui.page-header
        title="My Learning"
    >
    </x-ui.page-header>

    <div class="rounded-[28px] border border-[#d7dcef] bg-white px-6 py-6 shadow-sm sm:px-8">
        <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
            <div class="max-w-xl">
                <p class="text-lg font-semibold tracking-tight text-[#2c314b]">My Learning Overview</p>
                <p class="mt-1 text-sm leading-6 text-[#5f6785]">Ringkasan singkat progres belajar kamu, termasuk course, topik, dan sertifikat yang sudah didapat.</p>
            </div>

            <div class="grid gap-3 sm:grid-cols-3 lg:min-w-[420px] lg:flex-1">
                <div class="rounded-2xl border border-[#d7dcef] bg-[#f4faff] px-4 py-4">
                    <p class="text-xs font-medium tracking-wide text-[#5f6785]">Courses Enrolled</p>
                    <p class="mt-2 text-2xl font-bold text-[#004777]">{{ $summary['courses_enrolled'] ?? 0 }}</p>
                    <p class="mt-1 text-sm text-[#5f6785]">Yang sedang anda ikuti</p>
                </div>

                <div class="rounded-2xl border border-[#d7dcef] bg-[#f4faff] px-4 py-4">
                    <p class="text-xs font-medium tracking-wide text-[#5f6785]">Topics Completed</p>
                    <p class="mt-2 text-2xl font-bold text-[#004777]">{{ $summary['topics_completed'] ?? 0 }}</p>
                    <p class="mt-1 text-sm text-[#5f6785]">Topik yang telah diselesaikan</p>
                </div>

                <div class="rounded-2xl border border-[#d7dcef] bg-[#f4faff] px-4 py-4">
                    <p class="text-xs font-medium tracking-wide text-[#5f6785]">Certificates</p>
                    <p class="mt-2 text-2xl font-bold text-[#004777]">{{ $summary['certificates'] ?? 0 }}</p>
                    <p class="mt-1 text-sm text-[#5f6785]">Yang sudah anda dapatkan</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
        <section class="xl:col-span-2 space-y-4">
            <div class="flex items-end justify-between">
                <div>
                    <h2 class="text-lg font-bold text-[#004777]">Courses in progress</h2>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                @forelse($enrollments as $row)
                    @php
                        $course = $row['enrollment']->course;
                    @endphp

                    <article class="group flex h-full flex-col overflow-hidden rounded-2xl border border-[#004777]/10 bg-white transition hover:bg-[#35A7FF]/8">
                        <div class="p-2.5">
                            <div class="relative overflow-hidden rounded-lg thumb">
                                @php
                                    $poster = $course?->poster ?? null;
                                @endphp

                                @if(!empty($poster))
                                    <img src="{{ $poster }}"
                                         alt="{{ $course?->title }}"
                                         class="h-32 w-full object-cover sm:h-36 transition duration-500 group-hover:scale-105">
                                @elseif(!empty($course?->image))
                                    <img src="{{ asset('storage/' . $course->image) }}"
                                         alt="{{ $course?->title }}"
                                         class="h-32 w-full object-cover sm:h-36 transition duration-500 group-hover:scale-105">
                                @else
                                    <div class="flex h-32 w-full items-center justify-center bg-slate-200 sm:h-36">
                                        <svg width="100" height="56" viewBox="0 0 280 158" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <rect width="280" height="158" fill="#e6e9ee"/>
                                        </svg>
                                    </div>
                                @endif

                                <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-black/5 to-transparent"></div>

                                <div class="absolute left-3 top-3 flex flex-wrap gap-2">
                                    <span class="inline-flex rounded-full border border-white/20 bg-white/15 px-2.5 py-1 text-[10px] font-medium text-white backdrop-blur">
                                        {{ $row['percent'] }}% Completed
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-1 flex-col p-4">
                            <div class="flex-1 space-y-3">
                                <div class="space-y-1.5 min-h-[76px]">
                                    <h3 class="line-clamp-2 text-[15px] font-bold leading-snug text-[#004777]">
                                        {{ $course?->title }}
                                    </h3>

                                    <p class="line-clamp-2 text-xs leading-5 text-[#004777]/70">
                                        {{ $course?->description ?: 'No description available for this course.' }}
                                    </p>
                                </div>

                                <div class="space-y-2">
                                    <div class="flex items-center justify-between text-xs text-[#004777]/70">
                                        <span>{{ $row['completedTopics'] }} / {{ $row['totalTopics'] }} topics completed</span>
                                        <span class="font-semibold text-[#004777]">{{ $row['percent'] }}%</span>
                                    </div>

                                    <div class="h-2 overflow-hidden rounded-full bg-[#35A7FF]/15">
                                        <div class="h-2 rounded-full bg-[#004777]" style="width: {{ $row['percent'] }}%"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4 flex items-center gap-2">
                                <a href="{{ route('courses.show', $course?->slug) }}"
                                   class="inline-flex flex-1 items-center justify-center rounded-xl bg-[#004777] px-4 py-2.5 text-xs font-semibold text-white transition hover:bg-[#004777]/90">
                                    Open
                                </a>
                            </div>
                        </div>
                    </article>
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
            <div class="rounded-2xl border border-[#004777]/10 bg-white p-5 shadow-sm">
                <h2 class="mb-3 font-bold text-[#004777]">Upcoming Sessions</h2>
                <div class="space-y-3">
                    @forelse($upcomingSessions as $session)
                        <div class="rounded-xl border border-[#004777]/10 bg-[#f4faff] p-3">
                            <div class="text-sm font-medium text-[#004777]">{{ $session->topic?->name }}</div>
                            <div class="text-xs text-[#004777]/70">{{ $session->start_at->format('d M Y, H:i') }}</div>
                        </div>
                    @empty
                        <div class="text-sm text-[#004777]/70">Tidak ada sesi terjadwal.</div>
                    @endforelse
                </div>
            </div>
        </aside>
    </div>
</div>