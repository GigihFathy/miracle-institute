<div class="space-y-6 lg:px-36">
    <section class="rounded-2xl border bg-white p-6">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
            <div class="space-y-2">
                <div class="text-xs uppercase tracking-wide text-slate-400">
                    Mentor Workspace · {{ $topic->course?->title }}
                </div>
                <h1 class="text-2xl font-bold text-slate-900">
                    {{ $topic->name }}
                </h1>
                <p class="max-w-3xl text-sm text-slate-600">
                    Workspace ringkas untuk mengelola materi, session, attendance, collaborator, dan assessment.
                </p>
            </div>

            <div class="flex gap-3">
                <a href="{{ route('topics.show', $topic->slug) }}"
                   class="rounded-xl border px-4 py-2 text-sm">
                    Visit Topic
                </a>
                <a href="{{ route('mentor.topics.index') }}"
                   class="rounded-xl border px-4 py-2 text-sm">
                    Back
                </a>
            </div>
        </div>

        <div class="mt-6 grid grid-cols-2 gap-3 sm:grid-cols-3 text-sm">
            <div class="rounded-xl border bg-slate-50 p-4">
                <div class="text-xs text-slate-500">Topic</div>
                <div class="mt-1 font-semibold">{{ strtoupper($topic->status) }}</div>
            </div>
            <div class="rounded-xl border bg-slate-50 p-4">
                <div class="text-xs text-slate-500">Course</div>
                <div class="mt-1 font-semibold">{{ $topic->course?->title ?? '-' }}</div>
            </div>
            <div class="rounded-xl border bg-slate-50 p-4">
                <div class="text-xs text-slate-500">Program</div>
                <div class="mt-1 font-semibold">{{ $topic->course?->studyProgram?->title ?? '-' }}</div>
            </div>
        </div>

        <div class="mt-6 border-b">
            <div class="-mb-px flex gap-2 overflow-x-auto">
                @foreach($tabs as $key => $label)
                    <button type="button"
                            wire:click="setTab('{{ $key }}')"
                            class="whitespace-nowrap rounded-t-xl border-b-2 px-4 py-3 text-sm font-medium transition
                                {{ $tab === $key
                                    ? 'border-slate-900 bg-slate-900 text-white shadow-sm'
                                    : 'border-transparent text-slate-500 hover:border-slate-300 hover:text-slate-800 hover:bg-slate-50' }}">
                        {{ $label }}
                    </button>
                @endforeach
            </div>
        </div>
    </section>

    @livewire($activeComponent, ['topicId' => $topic->id], key($activeComponent . '-' . $topic->id))
</div>