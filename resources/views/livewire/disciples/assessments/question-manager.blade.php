<div
    x-data="{
        open: @entangle('openModal').live,
        dirty: false,
        initLeaveGuard() {
            window.addEventListener('beforeunload', (e) => {
                if (this.dirty) {
                    e.preventDefault();
                    e.returnValue = '';
                }
            });
        },
        closeModal() {
            if (this.dirty && !confirm('Ada perubahan belum disimpan. Tutup tanpa menyimpan?')) {
                return;
            }
            this.open = false;
            $wire.set('openModal', false);
            this.dirty = false;
        }
    }"
    x-init="initLeaveGuard()"
    x-on:question-saved.window="dirty = false; open = false"
    class="space-y-8 lg:px-36 pb-10 scale-[0.90] origin-top"
>

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
                                Question Manager
                            </h1>

                            <a href="{{ route('mentor.assessments.index', ['courseFilter' => $assessment->course_id]) }}"
                               class="h-11 px-4 rounded-xl border border-slate-200 bg-white text-sm font-medium text-slate-700 hover:bg-slate-50 transition inline-flex items-center">
                                Kembali
                            </a>
                        </div>

                        <p class="text-sm lg:text-[15px] leading-7 text-slate-600 max-w-3xl">
                            Kelola soal assessment dengan tampilan visual yang lebih cepat dibaca, lebih nyaman dioperasikan, dan aman saat editor dibuka atau ditutup.
                        </p>
                    </div>
                </div>

                <div class="flex gap-3 shrink-0">
                    <button wire:click="create"
                            class="h-11 px-5 rounded-xl bg-slate-900 text-white text-sm font-medium hover:bg-slate-800 transition">
                        + New Question
                    </button>
                </div>
            </div>

            {{-- STATS --}}
            @php
                $statRows = collect([
                    ['label' => 'Questions', 'value' => $questionsCount, 'note' => 'Total soal pada assessment ini.'],
                    ['label' => 'Attempts', 'value' => $attemptsCount, 'note' => 'Seluruh attempt yang tercatat.'],
                    ['label' => 'Submitted', 'value' => $submittedAttemptsCount, 'note' => 'Attempt yang sudah dikirim.'],
                    ['label' => 'Passed', 'value' => $passedAttemptsCount, 'note' => 'Attempt yang lolos passing grade.'],
                    ['label' => 'Passing Grade', 'value' => $assessment->passing_grade . '%', 'note' => 'Ambang batas kelulusan.'],
                ])->chunk(3);
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
                   type="search"
                   class="w-full border rounded-xl px-4 py-3"
                   placeholder="Search question or option...">

            <a href="{{ route('mentor.assessments.index', ['courseFilter' => $assessment->course_id]) }}"
               class="h-11 px-4 rounded-xl border bg-white text-sm font-medium text-slate-700 hover:bg-slate-50 transition inline-flex items-center justify-center">
                Back to assessments
            </a>

            <div class="h-11 px-4 rounded-xl border bg-white text-sm text-slate-500 inline-flex items-center">
                Course: {{ $assessment->course?->title }}
            </div>
        </div>
    </div>

    {{-- CONTENT --}}
    <section class="grid xl:grid-cols-[minmax(0,1fr)_380px] gap-6 items-start">

        {{-- QUESTIONS --}}
        <div class="space-y-5 min-w-0">
            @if($questions->isEmpty())
                <x-ui.empty-state
                    title="No questions yet"
                    description="Assessment ini belum memiliki pertanyaan."
                />
            @else
                <div class="space-y-4">
                    @foreach($questions as $q)
                        @php
                            $correctOption = $q->options->firstWhere('is_correct', true);
                        @endphp

                        <div wire:key="question-{{ $q->id }}"
                             class="rounded-[26px] border bg-white shadow-sm p-5 space-y-4 min-w-0">
                            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                                <div class="space-y-1 min-w-0">
                                    <div class="text-xs uppercase tracking-[0.2em] text-slate-400">
                                        Question {{ $loop->iteration }}
                                    </div>

                                    <h3 class="text-lg font-semibold leading-7 break-words">
                                        {{ $q->question }}
                                    </h3>
                                </div>

                                <div class="flex flex-wrap gap-2 shrink-0">
                                    <span class="px-3 py-1 rounded-full text-xs bg-slate-100">
                                        Order {{ $q->sort_order }}
                                    </span>
                                    <span class="px-3 py-1 rounded-full text-xs bg-emerald-100 text-emerald-700">
                                        MCQ
                                    </span>
                                </div>
                            </div>

                            <div class="grid gap-3 sm:grid-cols-2">
                                @foreach($q->options as $opt)
                                    <div class="rounded-2xl border p-4 {{ $opt->is_correct ? 'bg-emerald-50 border-emerald-200' : 'bg-slate-50' }}">
                                        <div class="flex items-start justify-between gap-3">
                                            <div class="text-sm leading-6 {{ $opt->is_correct ? 'font-semibold text-emerald-800' : 'text-slate-700' }}">
                                                {{ $opt->option_text }}
                                            </div>

                                            @if($opt->is_correct)
                                                <span class="px-2 py-1 rounded-full text-[11px] bg-emerald-600 text-white">
                                                    Correct
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="flex flex-wrap items-center justify-between gap-3 pt-1">
                                <div class="text-sm text-slate-500">
                                    Correct answer:
                                    <span class="font-medium text-slate-900">
                                        {{ $correctOption?->option_text ?? '—' }}
                                    </span>
                                </div>

                                <div class="flex flex-wrap gap-2">
                                    <button wire:click="edit('{{ $q->id }}')"
                                            class="px-3 py-2 rounded-xl bg-blue-50 text-blue-700 text-xs font-medium">
                                        Edit
                                    </button>

                                    <button wire:click="delete('{{ $q->id }}')"
                                            class="px-3 py-2 rounded-xl bg-rose-50 text-rose-700 text-xs font-medium">
                                        Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- SIDEBAR --}}
        <aside class="space-y-6 sticky top-24 h-fit min-w-0">
            <div class="rounded-[28px] bg-white border shadow-sm p-6 space-y-4">
                <div>
                    <div class="text-xs uppercase tracking-[0.2em] text-slate-400">
                        Assessment Focus
                    </div>
                    <h2 class="text-2xl font-bold mt-2 break-words">
                        {{ $assessment->title }}
                    </h2>
                    <p class="text-sm text-slate-500 mt-2 leading-6 break-words">
                        {{ $assessment->course?->studyProgram?->title }} · {{ $assessment->course?->title }}
                    </p>
                </div>

                <div class="grid grid-cols-2 gap-3 text-sm">
                    <div class="rounded-2xl border bg-slate-50 p-4 min-w-0">
                        <div class="text-xs text-slate-500">Course</div>
                        <div class="font-semibold mt-1 break-words">{{ $assessment->course?->title }}</div>
                    </div>
                    <div class="rounded-2xl border bg-slate-50 p-4 min-w-0">
                        <div class="text-xs text-slate-500">Passing Grade</div>
                        <div class="font-semibold mt-1">{{ $assessment->passing_grade }}%</div>
                    </div>
                    <div class="rounded-2xl border bg-slate-50 p-4 min-w-0">
                        <div class="text-xs text-slate-500">Timer</div>
                        <div class="font-semibold mt-1">{{ $assessment->time_limit_minutes ?? '-' }}</div>
                    </div>
                    <div class="rounded-2xl border bg-slate-50 p-4 min-w-0">
                        <div class="text-xs text-slate-500">Questions</div>
                        <div class="font-semibold mt-1">{{ $questionsCount }}</div>
                    </div>
                </div>

                <div class="rounded-2xl border bg-slate-50 p-4 space-y-3">
                    <div class="text-sm font-semibold">Quick actions</div>
                    <div class="grid grid-cols-2 gap-2">
                        <button wire:click="create"
                                class="px-3 py-2 rounded-xl bg-slate-900 text-white text-xs font-medium">
                            Add question
                        </button>
                        <a href="{{ route('mentor.assessments.index', ['courseFilter' => $assessment->course_id]) }}"
                           class="px-3 py-2 rounded-xl border text-xs text-center">
                            Back to course
                        </a>
                    </div>
                </div>

                <div class="rounded-2xl border bg-slate-50 p-4">
                    <div class="text-sm font-semibold">Editor safety</div>
                    <p class="mt-2 text-xs text-slate-500 leading-6">
                        Perubahan soal akan diperingatkan sebelum keluar halaman jika masih ada draft yang belum disimpan.
                    </p>
                </div>
            </div>
        </aside>
    </section>

    {{-- MODAL --}}
    <x-ui.studio-modal
        show="openModal"
        :title="$editingId ? 'Edit Question' : 'New Question'"
        description="Visual question editor untuk assessment course ini."
        maxWidth="max-w-4xl"
    >
        <form wire:submit.prevent="save" class="space-y-5" x-on:input="dirty = true">
            <textarea
                wire:model="question"
                rows="4"
                class="w-full border rounded-2xl px-4 py-3"
                placeholder="Write the question here..."
            ></textarea>
            @error('question') <div class="text-xs text-red-600">{{ $message }}</div> @enderror

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                @foreach($options as $i => $opt)
                    <div class="rounded-2xl border p-4 space-y-3 {{ $correctIndex === $i ? 'bg-emerald-50 border-emerald-200' : 'bg-slate-50' }}">
                        <div class="flex items-center justify-between gap-3">
                            <div class="text-sm font-semibold">Option {{ $i + 1 }}</div>

                            <button type="button"
                                    wire:click="$set('correctIndex', {{ $i }})"
                                    class="px-3 py-1.5 rounded-full text-xs {{ $correctIndex === $i ? 'bg-emerald-600 text-white' : 'bg-white border' }}">
                                {{ $correctIndex === $i ? 'Correct' : 'Mark correct' }}
                            </button>
                        </div>

                        <input
                            type="text"
                            wire:model="options.{{ $i }}.option_text"
                            class="w-full border rounded-xl px-4 py-3"
                            placeholder="Option text"
                        >
                        @error('options.' . $i . '.option_text') <div class="text-xs text-red-600">{{ $message }}</div> @enderror
                    </div>
                @endforeach
            </div>

            @error('options') <div class="text-xs text-red-600">{{ $message }}</div> @enderror
            @error('correctIndex') <div class="text-xs text-red-600">{{ $message }}</div> @enderror

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <input wire:model="sort_order" type="number" class="w-full border rounded-xl px-4 py-3" placeholder="Sort order">
                <div class="rounded-xl border bg-slate-50 px-4 py-3 text-sm text-slate-500">
                    Assessment: {{ $assessment->title }}
                </div>
            </div>
            @error('sort_order') <div class="text-xs text-red-600">{{ $message }}</div> @enderror

            <div class="rounded-2xl border bg-slate-50 p-4 text-xs text-slate-500 leading-6">
                Input mendukung simbol matematika dan teks biasa. Saat disimpan, konten dibersihkan agar tidak merusak struktur data.
            </div>
        </form>

        <x-slot:footer>
            <div class="flex items-center justify-between">
                <button type="button"
                        wire:click="dirty ? null : $set('openModal', false)"
                        x-on:click.prevent="closeModal()"
                        class="px-4 py-2 rounded-xl border">
                    Cancel
                </button>

                <button wire:click="save"
                        wire:loading.attr="disabled"
                        wire:target="save"
                        class="px-4 py-2 rounded-xl bg-slate-900 text-white disabled:opacity-60">
                    <span wire:loading.remove wire:target="save">Save Question</span>
                    <span wire:loading wire:target="save">Saving...</span>
                </button>
            </div>
        </x-slot:footer>
    </x-ui.studio-modal>
</div>