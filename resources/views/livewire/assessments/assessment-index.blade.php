<div class="space-y-5 lg:px-36">
    <x-ui.page-header
        title="Assessment"
        subtitle="Lihat post-test yang tersedia untuk course kamu."
    />

    <div class="rounded-2xl bg-white border p-4">
        <input type="search"
               wire:model.debounce.300ms="search"
               placeholder="Cari assessment..."
               class="w-full md:w-1/2 border rounded-xl px-4 py-2">
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        @forelse($assessments as $assessment)
            @php
                $latest = $latestAttempts[$assessment->id] ?? null;
            @endphp

            <div class="rounded-2xl bg-white border p-5 space-y-4">
                <div>
                    <h3 class="font-semibold text-lg">{{ $assessment->title }}</h3>
                    <p class="text-sm text-slate-500">
                        {{ $assessment->topic?->course?->title }} · {{ $assessment->topic?->name }}
                    </p>
                </div>

                <div class="grid grid-cols-2 gap-2 text-sm">
                    <div class="rounded-xl border p-3">Grade: {{ $assessment->passing_grade }}</div>
                    <div class="rounded-xl border p-3">Questions: {{ $assessment->question_limit ?? 'All' }}</div>
                </div>

                <div class="text-sm">
                    @if($latest)
                        <div class="rounded-xl border p-3">
                            Latest score: <strong>{{ $latest->score }}</strong>
                            <span class="{{ $latest->passed ? 'text-emerald-700' : 'text-rose-700' }}">
                                ({{ $latest->passed ? 'Passed' : 'Failed' }})
                            </span>
                        </div>
                    @else
                        <x-ui.empty-state
                            title="Belum ada attempt"
                            description="Kerjakan assessment ini untuk melihat hasil terbaru."
                        />
                    @endif
                </div>

                <a href="{{ route('assessments.take', $assessment->id) }}"
                   class="inline-flex px-4 py-2 rounded-xl bg-primary text-white text-sm">
                    Start
                </a>
            </div>
        @empty
            <x-ui.empty-state
                title="Assessment tidak ditemukan"
                description="Tidak ada assessment yang cocok dengan course yang kamu ikuti."
            />
        @endforelse
    </div>

    <div>{{ $assessments->links() }}</div>
</div>