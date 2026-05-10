<div class="space-y-6 lg:px-36">
    <x-ui.page-header
        title="Mentored Topics"
        subtitle="Daftar topic yang bisa kamu kelola."
    >
        <a href="{{ route('mentor.dashboard') }}" class="rounded-xl border px-4 py-2 text-sm">
            Back
        </a>
    </x-ui.page-header>

    <div class="rounded-2xl border bg-white p-4">
        <input
            wire:model.live="search"
            class="w-full rounded-xl border px-4 py-2 md:w-1/2"
            placeholder="Search topic..."
        >
    </div>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
        @forelse($topics as $topic)
            <div class="overflow-hidden rounded-2xl border bg-white">
                <div class="aspect-[16/9] bg-slate-100">
                    <img
                        src="{{ asset('storage/' . $topic->poster) }}"
                        alt="{{ $topic->name }}"
                        class="h-full w-full object-cover"
                    >
                </div>

                <div class="space-y-4 p-5">
                    <div>
                        <div class="text-xs uppercase tracking-wide text-slate-400">
                            {{ $topic->course?->title }}
                        </div>

                        <h3 class="mt-1 text-lg font-semibold text-slate-900">
                            {{ $topic->name }}
                        </h3>

                        <p class="mt-2 text-sm text-slate-600">
                            {{ \Illuminate\Support\Str::limit($topic->description, 100) }}
                        </p>
                    </div>

                    <div class="grid grid-cols-3 gap-2 text-sm">
                        <div class="rounded-xl border p-3 text-center">
                            <div class="font-semibold">{{ $topic->materials_count }}</div>
                            <div class="text-xs text-slate-500">Materials</div>
                        </div>

                        <div class="rounded-xl border p-3 text-center">
                            <div class="font-semibold">{{ $studentCounts[$topic->id] ?? 0 }}</div>
                            <div class="text-xs text-slate-500">Students</div>
                        </div>

                        <div class="rounded-xl border p-3 text-center">
                            <div class="font-semibold">{{ $topic->course->assessment->status == "active" ? "ACTIVE" : "INACTIVE" }}</div>
                            <div class="text-xs text-slate-500">Assessment</div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between gap-3">
                        <span class="rounded-full border px-2 py-1 text-xs text-slate-600">
                            {{ ucfirst($topic->status) }}
                        </span>

                        <a
                            href="{{ route('mentor.topics.show', $topic->slug) }}"
                            class="rounded-xl bg-slate-900 px-4 py-2 text-sm text-white hover:bg-slate-700"
                        >
                            Open
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

    <div>
        {{ $topics->links() }}
    </div>
</div>