<div class="space-y-6 lg:px-36">
    <x-ui.page-header
        title="Mentored Topics"
        subtitle="Kelola topic yang kamu mentor, lalu buka workspace untuk menambah materi."
    >
        <a href="{{ route('mentor.dashboard') }}" class="px-4 py-2 rounded-xl border text-sm">Back to Dashboard</a>
    </x-ui.page-header>

    <div class="rounded-2xl bg-white border p-4">
        <input wire:model.live="search"
               class="w-full md:w-1/2 border rounded-xl px-4 py-2"
               placeholder="Search mentored topic...">
    </div>

    <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4">
        @forelse($topics as $topic)
            <div class="rounded-2xl bg-white border overflow-hidden flex flex-col">
                <div class="aspect-[16/9] bg-slate-100">
                    <img src="{{ asset('storage/' . $topic->poster) }}"
                         class="w-full h-full object-cover"
                         alt="{{ $topic->name }}">
                </div>

                <div class="p-5 space-y-4 flex-1 flex flex-col">
                    <div>
                        <div class="text-xs uppercase tracking-wide text-slate-400">
                            {{ $topic->course?->title }}
                        </div>
                        <h3 class="text-lg font-semibold mt-1">{{ $topic->name }}</h3>
                        <p class="text-sm text-slate-600 mt-2">
                            {{ \Illuminate\Support\Str::limit($topic->description, 110) }}
                        </p>
                    </div>

                    <div class="grid grid-cols-3 gap-2 text-sm">
                        <div class="rounded-xl border p-3 text-center">
                            <div class="font-semibold">{{ $topic->materials->count() }}</div>
                            <div class="text-xs text-slate-500">Materials</div>
                        </div>
                        <div class="rounded-xl border p-3 text-center">
                            <div class="font-semibold">{{ $studentCounts[$topic->id] ?? 0 }}</div>
                            <div class="text-xs text-slate-500">Students</div>
                        </div>
                        <div class="rounded-xl border p-3 text-center">
                            <div class="font-semibold">{{ $topic->course->assessment->status }}</div>
                            <div class="text-xs text-slate-500">Tests</div>
                        </div>
                    </div>

                    <div class="mt-auto flex items-center justify-between gap-3">
                        <a href="{{ route('mentor.materials.index', $topic->slug) }}"
                           class="inline-flex px-4 py-2 rounded-xl bg-slate-900 text-white text-sm">
                            Open Workspace
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <x-ui.empty-state
                title="Belum ada topic"
                description="Kamu belum menjadi mentor di topik mana pun."
            />
        @endforelse
    </div>

    <div>{{ $topics->links() }}</div>
</div>