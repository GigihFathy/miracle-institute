<div class="space-y-6 px-4 pt-6 pb-6 sm:pt-6 sm:px-6 lg:px-12 xl:px-36">
    <x-ui.page-header
        title="My Learning"
    >
    </x-ui.page-header>

    <div class="rounded-[28px] border border-[#d7dcef] bg-white px-6 pt-6 shadow-sm sm:px-8">
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

        <div class="border-[#d7dcef] pt-6 lg:pt-8">
            <div class="flex items-center gap-6 overflow-x-auto whitespace-nowrap text-sm font-medium text-[#5f6785]" role="tablist" aria-label="Learning sections">
                <button type="button" role="tab" wire:click="$set('tab','courses')" aria-selected="{{ $tab==='courses' ? 'true' : 'false' }}" class="{{ $tab==='courses' ? 'border-b-2 border-[#004777] pb-2 text-[#004777]' : 'border-b-2 border-transparent pb-2 hover:text-[#004777]' }}">
                    Courses
                </button>
                <button type="button" role="tab" wire:click="$set('tab','session')" aria-selected="{{ $tab==='session' ? 'true' : 'false' }}" class="{{ $tab==='session' ? 'border-b-2 border-[#004777] pb-2 text-[#004777]' : 'border-b-2 border-transparent pb-2 hover:text-[#004777]' }}">
                    Session
                </button>
                <button type="button" role="tab" wire:click="$set('tab','certificate')" aria-selected="{{ $tab==='certificate' ? 'true' : 'false' }}" class="{{ $tab==='certificate' ? 'border-b-2 border-[#004777] pb-2 text-[#004777]' : 'border-b-2 border-transparent pb-2 hover:text-[#004777]' }}">
                    Certificate
                </button>
            </div>
        </div>
    </div>

    <div class="w-full space-y-4">
        <section class="w-full space-y-4">
            {{-- Courses tab content --}}
            @if($tab === 'courses')
            <div class="space-y-4">
                <div class="flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
                    <div>
                        <h2 class="text-lg font-bold text-[#004777]">Courses in progress</h2>
                    </div>

                    <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                        <div class="relative">
                            <input type="search"
                                   wire:model.live.debounce.300ms="searchCourse"
                                   placeholder="Search course..."
                                   class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-slate-900 focus:bg-white sm:w-56" />
                        </div>

                        <select wire:model.live="filterCourse" class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none">
                            <option value="all">All</option>
                            <option value="in_progress">In Progress</option>
                            <option value="completed">Completed</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-4">
                    @forelse($enrollments as $row)
                        @php
                            $course = $row['enrollment']->course;
                        @endphp

                        <a href="{{ route('courses.show', $course?->slug) }}" class="group flex h-full flex-col overflow-hidden rounded-2xl border border-[#004777]/10 bg-white transition hover:bg-slate-100">
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
                            </div>
                        </a>
                    @empty
                        <div class="col-span-full">
                            @if($hasEnrollments)
                                <div class="rounded-2xl border border-dashed border-[#d7dcef] bg-white px-6 py-12 text-center">
                                    <h3 class="text-base font-semibold text-[#004777]">Tidak ada course yang cocok</h3>
                                    <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-[#5f6785]">
                                        Coba ubah kata kunci pencarian atau filter progress agar kursusnya muncul kembali.
                                    </p>

                                    <div class="mt-5 flex flex-wrap justify-center gap-3">
                                        <button type="button"
                                                wire:click="resetCourseFilters"
                                                class="inline-flex items-center rounded-xl border border-[#004777]/15 bg-white px-4 py-2 text-sm font-medium text-[#004777] transition hover:bg-[#f4faff]">
                                            Reset Filters
                                        </button>

                                        <a href="{{ route('courses.index') }}"
                                           class="inline-flex items-center rounded-xl bg-[#004777] px-4 py-2 text-sm font-medium text-white transition hover:bg-[#003a5f]">
                                            Browse Courses
                                        </a>
                                    </div>
                                </div>
                            @else
                                <x-ui.empty-state
                                    title="Belum ada course"
                                    description="Kamu belum mengikuti course apa pun."
                                    button-label="Browse Courses"
                                    button-href="{{ route('courses.index') }}"
                                />
                            @endif
                        </div>
                    @endforelse
                </div>
            </div>
            @endif

            {{-- Session tab content --}}
            @if($tab === 'session')
            <div class="space-y-4">
                <h2 class="text-lg font-bold text-[#004777]">Upcoming Sessions</h2>

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
            @endif

            {{-- Certificate tab content --}}
            @if($tab === 'certificate')
            <div class="space-y-4">
                <h2 class="text-lg font-bold text-[#004777]">Certificates</h2>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
                    @forelse($certificates as $certificate)
                        @php
                            $course = $certificate->course;
                            $poster = $course?->poster ?? null;
                        @endphp

                        <article class="group flex h-full flex-col overflow-hidden rounded-2xl border border-[#004777]/10 bg-white transition hover:bg-slate-100">
                            <div class="p-2.5">
                                <div class="relative overflow-hidden rounded-lg thumb">
                                    @if(!empty($poster))
                                        <img src="{{ $poster }}"
                                             alt="{{ $course?->title ?? 'Course Certificate' }}"
                                             class="h-32 w-full object-cover sm:h-36 transition duration-500 group-hover:scale-105">
                                    @elseif(!empty($course?->image))
                                        <img src="{{ asset('storage/' . $course->image) }}"
                                             alt="{{ $course?->title ?? 'Course Certificate' }}"
                                             class="h-32 w-full object-cover sm:h-36 transition duration-500 group-hover:scale-105">
                                    @else
                                        <div class="flex h-32 w-full items-center justify-center bg-slate-200 sm:h-36">
                                            <svg width="100" height="56" viewBox="0 0 280 158" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <rect width="280" height="158" fill="#e6e9ee"/>
                                            </svg>
                                        </div>
                                    @endif

                                    <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-black/5 to-transparent"></div>
                                </div>
                            </div>

                            <div class="flex flex-1 flex-col p-4">
                                <div class="flex-1 space-y-3">
                                    <div class="space-y-1.5 min-h-19">
                                        <h3 class="line-clamp-2 text-[15px] font-bold leading-snug text-[#004777]">
                                            {{ $course?->title ?? 'Course Certificate' }}
                                        </h3>

                                        <p class="line-clamp-2 text-xs leading-5 text-[#004777]/70">
                                            {{ $certificate->issued_at?->format('d M Y') ?? '-' }}
                                        </p>
                                    </div>

                                    <div class="space-y-2">
                                        <div class="flex items-center justify-between text-xs text-[#004777]/70">
                                            <span>Certificate Number</span>
                                            <span class="font-semibold text-[#004777]">Issued</span>
                                        </div>

                                        <div class="rounded-xl bg-[#f4faff] px-3 py-2 text-xs font-medium break-all text-[#004777]">
                                            {{ $certificate->certificate_number }}
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <a href="{{ route('certificates.download', $certificate->id) }}"
                                       class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-[#004777] px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-[#003a5f]">
                                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                            <path d="M10 3a1 1 0 0 1 1 1v6.586l1.293-1.293a1 1 0 1 1 1.414 1.414l-3 3a1 1 0 0 1-1.414 0l-3-3a1 1 0 1 1 1.414-1.414L9 10.586V4a1 1 0 0 1 1-1Z" />
                                            <path d="M4 13a1 1 0 0 1 1 1v1a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1v-1a1 1 0 1 1 2 0v1a3 3 0 0 1-3 3H6a3 3 0 0 1-3-3v-1a1 1 0 0 1 1-1Z" />
                                        </svg>
                                        Download Certificate
                                    </a>
                                </div>
                            </div>
                        </article>
                    @empty
                        <div class="col-span-full">
                            <div class="rounded-2xl border border-dashed border-[#d7dcef] bg-white px-6 py-12 text-center">
                                <h3 class="text-base font-semibold text-[#004777]">No Certificates Yet</h3>
                                <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-[#5f6785]">Sertifikat akan tersedia setelah kamu menyelesaikan course.</p>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
            @endif
        </section>
    </div>
</div>