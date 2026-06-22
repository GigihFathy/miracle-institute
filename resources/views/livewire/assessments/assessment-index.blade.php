<div class="space-y-6 lg:px-36">
    <section class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8">
        <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <div class="text-xs uppercase tracking-[0.18em] text-[color:color-mix(in_oklab,#004777_48%,white)]">
                    Assessment Center
                </div>
                <h1 class="mt-2 text-2xl font-bold tracking-tight text-mentor-primary sm:text-3xl">
                    Assessment untuk topik pembelajaran yang sedang kamu pelajari
                </h1>
                <p class="mt-3 max-w-3xl text-sm leading-6 text-[color:color-mix(in_oklab,#004777_72%,white)]">
                    Lihat assessment yang tersedia, cek attempt terakhir, lalu lanjutkan saat kamu sudah siap.
                </p>
            </div>

            <div class="w-full lg:max-w-md">
                <input
                    type="search"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Cari assessment..."
                    class="h-12 w-full rounded-2xl border border-slate-200 bg-slate-50 px-5 text-sm text-mentor-primary outline-none transition focus:border-mentor-primary focus:bg-white focus:ring-2 focus:ring-mentor-secondary-solid"
                >
            </div>
        </div>
    </section>

    <section class="grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-3">
        @forelse($assessments as $assessment)
            @php
                $latest = $latestAttempts[$assessment->id] ?? null;
            @endphp

            <article class="flex h-full flex-col rounded-3xl border border-slate-200 bg-white p-5">
                <div class="flex-1 space-y-4">
                    <div>
                        <div class="text-xs uppercase tracking-wide text-[color:color-mix(in_oklab,#004777_48%,white)]">
                            {{ $assessment->course?->title ?? '-' }}
                        </div>
                        <h2 class="mt-2 text-lg font-semibold text-mentor-primary">
                            {{ $assessment->title }}
                        </h2>
                        @if($assessment->topic?->name)
                            <p class="mt-1 text-sm text-slate-500">
                                {{ $assessment->topic->name }}
                            </p>
                        @endif
                    </div>

                    <div class="grid grid-cols-2 gap-3 text-sm">
                        <div class="rounded-2xl border border-slate-200 bg-mentor-primary-soft-2 p-4">
                            <div class="text-xs uppercase tracking-wide text-[color:color-mix(in_oklab,#004777_48%,white)]">Passing Grade</div>
                            <div class="mt-1 font-semibold text-mentor-primary">{{ $assessment->passing_grade }}%</div>
                        </div>

                        <div class="rounded-2xl border border-slate-200 bg-mentor-primary-soft-2 p-4">
                            <div class="text-xs uppercase tracking-wide text-[color:color-mix(in_oklab,#004777_48%,white)]">Questions</div>
                            <div class="mt-1 font-semibold text-mentor-primary">{{ $assessment->question_limit ?? 'All' }}</div>
                        </div>
                    </div>

                    @if($latest)
                        <div class="rounded-2xl border {{ $latest->passed ? 'border-emerald-200 bg-emerald-50' : 'border-rose-200 bg-rose-50' }} p-4 text-sm">
                            <div class="text-xs uppercase tracking-wide {{ $latest->passed ? 'text-emerald-700' : 'text-rose-700' }}">
                                Latest Attempt
                            </div>
                            <div class="mt-2 flex items-center justify-between gap-3">
                                <div class="font-semibold {{ $latest->passed ? 'text-emerald-800' : 'text-rose-800' }}">
                                    Score {{ $latest->score }}
                                </div>
                                <span class="rounded-full px-2.5 py-1 text-[11px] font-medium {{ $latest->passed ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                                    {{ $latest->passed ? 'Passed' : 'Failed' }}
                                </span>
                            </div>
                        </div>
                    @else
                        <div class="rounded-2xl border border-dashed border-slate-200 bg-slate-50 p-4 text-sm text-slate-600">
                            Belum ada attempt untuk assessment ini.
                        </div>
                    @endif
                </div>

                <div class="mt-5">
                    <a href="{{ localized_route('assessments.take', $assessment->id) }}"
                       class="admin-primary-button inline-flex w-full items-center justify-center rounded-xl px-4 py-2.5 text-sm">
                        {{ $latest ? 'Lihat / lanjutkan assessment' : 'Mulai assessment' }}
                    </a>
                </div>
            </article>
        @empty
            <div class="col-span-full rounded-3xl border border-dashed border-slate-300 bg-white px-8 py-20 text-center">
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-mentor-primary-soft-2">
                    <svg class="h-8 w-8 text-mentor-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.75 9.75h4.5m-4.5 4.5h4.5M6.75 3.75h10.5A2.25 2.25 0 0119.5 6v12a2.25 2.25 0 01-2.25 2.25H6.75A2.25 2.25 0 014.5 18V6a2.25 2.25 0 012.25-2.25z"/>
                    </svg>
                </div>

                <h3 class="mt-5 text-lg font-bold text-mentor-primary">
                    Assessment tidak ditemukan
                </h3>

                <p class="mt-2 text-sm text-[color:color-mix(in_oklab,#004777_72%,white)]">
                    Tidak ada assessment yang cocok dengan topik pembelajaran yang kamu ikuti.
                </p>
            </div>
        @endforelse
    </section>

    <div>{{ $assessments->links() }}</div>
</div>
