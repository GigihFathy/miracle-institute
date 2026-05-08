<div class="space-y-6 lg:px-5 2xl:px-8 pb-10 scale-[0.90] origin-top">

    {{-- HERO --}}
    <section class="rounded-[28px] border bg-white shadow-sm overflow-hidden">
        <div class="p-7 lg:p-10 space-y-6">

            <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-5">
                <div class="space-y-3 max-w-4xl min-w-0">
                    <div class="text-[11px] uppercase tracking-[0.35em] text-slate-400">
                        Disciples Studio
                    </div>

                    <div class="space-y-3 min-w-0">
                        <div class="flex flex-wrap items-center gap-3 min-w-0">
                            <h1 class="text-3xl lg:text-4xl font-bold tracking-tight text-slate-900">
                                Assessment Studio
                            </h1>

                            <a href="{{ route('mentor.courses.index') }}"
                               class="h-11 px-4 rounded-xl border border-slate-200 bg-white text-sm font-medium text-slate-700 hover:bg-slate-50 transition inline-flex items-center">
                                Kembali
                            </a>
                        </div>

                        <p class="text-sm lg:text-[15px] leading-7 text-slate-600 max-w-3xl">
                            Assessment berbasis course, dikelola secara visual dan terhubung langsung ke question manager dengan data yang relevan untuk monitoring operasional.
                        </p>
                    </div>
                </div>

                <div class="flex gap-3 shrink-0">
                    <button wire:click="create"
                            class="h-11 px-5 rounded-xl bg-slate-900 text-white text-sm font-medium hover:bg-slate-800 transition">
                        + New Assessment
                    </button>
                </div>
            </div>

            {{-- STATS --}}
            @php
                $statRows = collect($statsCards)->chunk(3);
            @endphp

            <div class="space-y-3">
                @foreach($statRows as $row)
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 {{ $row->count() === 3 ? 'xl:grid-cols-3' : ($row->count() === 2 ? 'xl:grid-cols-2' : 'xl:grid-cols-1') }}">
                        @foreach($row as $card)
                            <div class="rounded-2xl border border-slate-200 bg-slate-50/70 p-5 min-w-0">
                                <div class="text-xs text-slate-500">
                                    {{ $card['label'] }}
                                </div>

                                <div class="mt-2 text-3xl font-bold tracking-tight text-slate-900 break-words">
                                    {{ $card['value'] }}
                                </div>

                                <div class="mt-1 text-xs leading-5 text-slate-500 break-words">
                                    {{ $card['note'] }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- FILTER --}}
    <div class="rounded-2xl border border-slate-200 bg-slate-50/60 p-4">
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-3">
            <input wire:model.live.debounce.300ms="search"
                   class="w-full border rounded-xl px-4 py-3"
                   placeholder="Search assessment...">

            <select wire:model.live="courseFilter" class="border rounded-xl px-4 py-3">
                <option value="">All courses</option>
                @foreach($courses as $course)
                    <option value="{{ $course->id }}">{{ $course->title }}</option>
                @endforeach
            </select>

            <select wire:model.live="statusFilter" class="border rounded-xl px-4 py-3">
                <option value="">All status</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
                <option value="draft">Draft</option>
            </select>
        </div>
    </div>

    {{-- CONTENT --}}
    <section class="grid xl:grid-cols-[minmax(0,1fr)_380px] gap-6 items-start">

        {{-- ASSESSMENT GRID --}}
        <div class="space-y-5 min-w-0">
            @if($rows->isEmpty())
                <x-ui.empty-state
                    title="No assessment found"
                    description="Belum ada assessment yang cocok dengan filter saat ini."
                />
            @else
                <div class="grid md:grid-cols-2 2xl:grid-cols-3 gap-5">
                    @foreach($rows as $row)
                        <div wire:key="assessment-{{ $row->id }}"
                             class="rounded-[26px] border bg-white shadow-sm hover:shadow-lg transition-all duration-300 overflow-hidden min-w-0">

                            <button type="button"
                                    wire:click="selectAssessment('{{ $row->id }}')"
                                    class="w-full text-left block">
                                <div class="p-5 space-y-4 min-w-0">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="min-w-0">
                                            <div class="text-[11px] uppercase tracking-[0.2em] text-slate-400 truncate">
                                                {{ $row->course?->studyProgram?->title }}
                                            </div>

                                            <div class="text-xl font-semibold leading-tight break-words line-clamp-2">
                                                {{ $row->title }}
                                            </div>
                                        </div>

                                        <span class="shrink-0 px-2.5 py-1 rounded-full text-[11px] border {{ $row->status_badge_class }}">
                                            {{ ucfirst($row->status) }}
                                        </span>
                                    </div>

                                    <div class="grid grid-cols-2 gap-2 text-xs">
                                        <div class="rounded-xl border bg-slate-50 p-3">
                                            <div class="text-slate-500">Questions</div>
                                            <div class="mt-1 font-bold text-slate-900">
                                                {{ $row->questions_count }}
                                            </div>
                                        </div>

                                        <div class="rounded-xl border bg-slate-50 p-3">
                                            <div class="text-slate-500">Attempts</div>
                                            <div class="mt-1 font-bold text-slate-900">
                                                {{ $row->attempts_count }}
                                            </div>
                                        </div>

                                        <div class="rounded-xl border bg-slate-50 p-3">
                                            <div class="text-slate-500">Passing</div>
                                            <div class="mt-1 font-bold text-slate-900">
                                                {{ $row->passing_grade }}%
                                            </div>
                                        </div>

                                        <div class="rounded-xl border bg-slate-50 p-3">
                                            <div class="text-slate-500">Timer</div>
                                            <div class="mt-1 font-bold text-slate-900">
                                                {{ $row->time_label }}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="rounded-xl border bg-slate-50 p-3 text-xs text-slate-500 break-words">
                                        {{ $row->course?->title }}
                                    </div>
                                </div>
                            </button>

                            <div class="border-t p-5 bg-slate-50 space-y-3">
                                <div class="flex flex-wrap gap-2">
                                    <a href="{{ route('mentor.assessments.questions', $row->id) }}"
                                       class="px-3 py-2 rounded-xl bg-slate-900 text-white text-xs font-medium">
                                        Questions
                                    </a>

                                    <button wire:click.stop="edit('{{ $row->id }}')"
                                            class="px-3 py-2 rounded-xl bg-blue-50 text-blue-700 text-xs font-medium">
                                        Edit
                                    </button>

                                    <button wire:click.stop="delete('{{ $row->id }}')"
                                            class="px-3 py-2 rounded-xl bg-rose-50 text-rose-700 text-xs font-medium">
                                        Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="pt-2">
                    {{ $rows->links() }}
                </div>
            @endif
        </div>

        {{-- SIDEBAR --}}
        <aside class="space-y-4 sticky top-24 h-fit min-w-0">
            <div class="rounded-[28px] bg-white border shadow-sm overflow-hidden">
                @if($selectedAssessment)
                    <div class="p-6 space-y-5 min-w-0">
                        <div class="space-y-2">
                            <div class="text-[11px] uppercase tracking-[0.25em] text-slate-400">
                                Selected Assessment
                            </div>

                            <h2 class="text-2xl font-bold tracking-tight text-slate-900 leading-tight break-words">
                                {{ $selectedAssessment->title }}
                            </h2>

                            <div class="flex flex-wrap gap-2">
                                <span class="px-3 py-1 rounded-full text-xs bg-slate-100">
                                    {{ $selectedAssessment->course?->title }}
                                </span>

                                <span class="px-3 py-1 rounded-full text-xs bg-slate-100">
                                    {{ $selectedAssessment->course?->studyProgram?->title }}
                                </span>

                                <span class="px-3 py-1 rounded-full text-xs border {{ $selectedAssessment->status_badge_class }}">
                                    {{ ucfirst($selectedAssessment->status) }}
                                </span>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3 text-sm">
                            <div class="rounded-2xl border bg-slate-50 p-4 min-w-0">
                                <div class="text-xs text-slate-500">Course</div>
                                <div class="font-semibold mt-1 break-words">
                                    {{ $selectedAssessment->course?->title }}
                                </div>
                            </div>

                            <div class="rounded-2xl border bg-slate-50 p-4 min-w-0">
                                <div class="text-xs text-slate-500">Passing Grade</div>
                                <div class="font-semibold mt-1">
                                    {{ $selectedAssessment->passing_grade }}%
                                </div>
                            </div>

                            <div class="rounded-2xl border bg-slate-50 p-4 min-w-0">
                                <div class="text-xs text-slate-500">Timer</div>
                                <div class="font-semibold mt-1">
                                    {{ $selectedAssessment->time_label }}
                                </div>
                            </div>

                            <div class="rounded-2xl border bg-slate-50 p-4 min-w-0">
                                <div class="text-xs text-slate-500">Questions</div>
                                <div class="font-semibold mt-1">
                                    {{ $selectedAssessment->questions_count }}
                                </div>
                            </div>
                        </div>

                        <div class="rounded-2xl border bg-slate-50 p-4 space-y-3">
                            <div class="text-sm font-semibold">Question mix</div>
                            <div class="flex flex-wrap gap-2">
                                <span class="px-3 py-1 rounded-full text-xs bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    MCQ: {{ $selectedAssessment->questions_count }}
                                </span>
                            </div>
                            <p class="text-xs text-slate-500 leading-6">
                                Assessment saat ini dikelola sebagai kumpulan soal pilihan ganda.
                            </p>
                        </div>

                        <div class="rounded-2xl border bg-slate-50 p-4 space-y-3">
                            <div class="text-sm font-semibold">Quick actions</div>

                            <div class="grid grid-cols-2 gap-2">
                                <a href="{{ route('mentor.assessments.questions', $selectedAssessment->id) }}"
                                   class="px-3 py-2 rounded-xl bg-slate-900 text-white text-xs text-center">
                                    Open questions
                                </a>

                                <a href="{{ route('courses.show', $selectedAssessment->course?->slug) }}"
                                   class="px-3 py-2 rounded-xl border text-xs text-center">
                                    Course view
                                </a>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="p-10">
                        <x-ui.empty-state
                            title="Select an assessment"
                            description="Klik assessment card untuk melihat detail dan shortcut manajemen."
                        />
                    </div>
                @endif
            </div>
        </aside>
    </section>

    {{-- MODAL --}}
    <x-ui.studio-modal
        show="showModal"
        :title="$editingId ? 'Edit Assessment' : 'New Assessment'"
        description="Studio form untuk assessment berbasis course."
        maxWidth="max-w-3xl"
    >
        <form wire:submit.prevent="save" class="space-y-4">
            <select wire:model="course_id" class="w-full border rounded-xl px-4 py-3">
                <option value="">Select course</option>
                @foreach($courses as $course)
                    <option value="{{ $course->id }}">{{ $course->title }}</option>
                @endforeach
            </select>
            @error('course_id') <div class="text-xs text-red-600">{{ $message }}</div> @enderror

            <input wire:model="title" class="w-full border rounded-xl px-4 py-3" placeholder="Title">
            @error('title') <div class="text-xs text-red-600">{{ $message }}</div> @enderror

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <input wire:model="passing_grade" type="number" class="w-full border rounded-xl px-4 py-3" placeholder="Passing grade">
                <input wire:model="question_limit" type="number" class="w-full border rounded-xl px-4 py-3" placeholder="Question limit">
            </div>
            @error('passing_grade') <div class="text-xs text-red-600">{{ $message }}</div> @enderror
            @error('question_limit') <div class="text-xs text-red-600">{{ $message }}</div> @enderror

            <input wire:model="time_limit_minutes" type="number" class="w-full border rounded-xl px-4 py-3" placeholder="Time limit (minutes)">
            @error('time_limit_minutes') <div class="text-xs text-red-600">{{ $message }}</div> @enderror

            <label class="flex items-center gap-2 text-sm">
                <input type="checkbox" wire:model="randomize_questions">
                Randomize questions
            </label>

            <select wire:model="status" class="w-full border rounded-xl px-4 py-3">
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
                <option value="draft">Draft</option>
            </select>
            @error('status') <div class="text-xs text-red-600">{{ $message }}</div> @enderror
        </form>

        <x-slot:footer>
            <div class="flex items-center justify-between">
                <button type="button"
                        wire:click="$set('showModal', false)"
                        class="px-4 py-2 rounded-xl border">
                    Cancel
                </button>

                <button wire:click="save"
                        wire:loading.attr="disabled"
                        wire:target="save"
                        class="px-4 py-2 rounded-xl bg-slate-900 text-white disabled:opacity-60">
                    <span wire:loading.remove wire:target="save">Save</span>
                    <span wire:loading wire:target="save">Saving...</span>
                </button>
            </div>
        </x-slot:footer>
    </x-ui.studio-modal>
</div>