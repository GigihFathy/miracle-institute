<div class="space-y-6 lg:px-36">
    <section class="rounded-2xl border bg-white p-6">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
            <div class="space-y-2">
                <a href="{{ url()->previous() }}"
                   onclick="if (window.history.length > 1) { event.preventDefault(); window.history.back(); }"
                   class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-3 py-1.5 text-sm font-medium text-slate-600 transition hover:border-slate-300 hover:bg-slate-50 hover:text-slate-900">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4" aria-hidden="true">
                        <path fill-rule="evenodd" d="M11.78 4.22a.75.75 0 0 1 0 1.06L7.06 10l4.72 4.72a.75.75 0 1 1-1.06 1.06l-5.25-5.25a.75.75 0 0 1 0-1.06l5.25-5.25a.75.75 0 0 1 1.06 0Z" clip-rule="evenodd" />
                    </svg>
                    <span>Back</span>
                </a>
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