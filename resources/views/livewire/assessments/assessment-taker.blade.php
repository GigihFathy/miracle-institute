<div wire:key="assessment-{{ $attempt->id }}-{{ $currentIndex }}" class="max-w-6xl mx-auto space-y-6 p-4 sm:p-6">

    <section class="rounded-3xl bg-white border p-6 shadow-sm space-y-4">
        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
            <div>
                <div class="text-xs uppercase tracking-wide text-slate-400">
                    {{ $assessment->course?->title ?? 'Course Assessment' }}
                </div>

                <h1 class="text-2xl font-bold mt-1">
                    {{ $assessment->title }}
                </h1>

                <p class="text-sm text-slate-500 mt-2 max-w-3xl leading-6">
                    Assessment ini menjadi syarat kelulusan course setelah seluruh topik selesai dipelajari.
                    Anda dapat mengulang sampai mencapai passing grade.
                </p>
            </div>

            <div class="flex flex-wrap gap-2 justify-end">
                <span class="px-3 py-1 rounded-full text-xs border bg-slate-50 text-slate-600">
                    Attempt #{{ $attempt->attempt_no }}
                </span>
                <span class="px-3 py-1 rounded-full text-xs border bg-slate-50 text-slate-600">
                    Passing {{ $assessment->passing_grade }}%
                </span>
                <span class="px-3 py-1 rounded-full text-xs border bg-slate-50 text-slate-600">
                    {{ $assessment->randomize_questions ? 'Randomized' : 'Fixed Order' }}
                </span>
            </div>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-sm">
            <div class="rounded-xl border p-3 bg-slate-50">
                <div class="text-xs text-slate-500">Started</div>
                <div class="font-semibold mt-1">{{ $attempt->started_at?->format('d M Y, H:i') ?? '-' }}</div>
            </div>

            <div class="rounded-xl border p-3 bg-slate-50">
                <div class="text-xs text-slate-500">Questions</div>
                <div class="font-semibold mt-1">{{ count($questions) }}</div>
            </div>

            <div class="rounded-xl border p-3 bg-slate-50">
                <div class="text-xs text-slate-500">Answered</div>
                <div class="font-semibold mt-1">{{ collect($answers)->filter()->count() }} / {{ count($questions) }}</div>
            </div>

            <div class="rounded-xl border p-3 bg-slate-50">
                <div class="text-xs text-slate-500">State</div>
                <div class="font-semibold mt-1">{{ strtoupper($attempt->status) }}</div>
            </div>
        </div>
    </section>

    <section class="grid grid-cols-1 xl:grid-cols-[260px_1fr] gap-6">

        <aside class="bg-white border rounded-3xl p-5 h-fit sticky top-24 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <h2 class="font-semibold">Navigator</h2>
                <span class="text-xs text-slate-500">
                    {{ collect($answers)->filter()->count() }} / {{ count($questions) }}
                </span>
            </div>

            <div class="grid grid-cols-5 gap-2">
                @foreach($questions as $i => $question)
                    @php
                        $isActive = $currentIndex === $i;
                        $isAnswered = !empty($answers[$question['id']] ?? null);
                    @endphp

                    <button
                        wire:click="goTo({{ $i }})"
                        class="h-10 rounded-xl text-sm border font-medium transition
                        {{ $isActive
                            ? 'bg-black text-white border-black'
                            : ($isAnswered
                                ? 'bg-emerald-100 text-emerald-700 border-emerald-300'
                                : 'bg-white hover:bg-slate-50') }}">
                        {{ $i + 1 }}
                    </button>
                @endforeach
            </div>

            <div class="mt-4 rounded-2xl border bg-slate-50 p-4 text-sm text-slate-600 leading-6">
                Semua jawaban disimpan otomatis. Jika attempt belum lulus, Anda bisa mengulang dari awal.
            </div>
        </aside>

        <main class="space-y-5">
            @php $q = $questions[$currentIndex] ?? null; @endphp

            @if($q)
                <section wire:key="question-{{ $q['id'] }}" class="bg-white border rounded-3xl p-6 shadow-sm space-y-5">
                    <div class="flex justify-between items-start gap-4">
                        <div>
                            <div class="text-xs uppercase tracking-wide text-slate-400">
                                Question {{ $currentIndex + 1 }} of {{ count($questions) }}
                            </div>

                            <h2 class="text-xl font-semibold mt-2">
                                {{ $q['question'] }}
                            </h2>
                        </div>

                        <span class="text-xs px-2 py-1 rounded-full bg-slate-100 border">
                            MCQ
                        </span>
                    </div>

                    <div class="space-y-3">
                        @foreach($q['options'] as $opt)
                            <button
                                type="button"
                                wire:click="selectOption('{{ $q['id'] }}', '{{ $opt['id'] }}')"
                                class="w-full text-left flex items-start gap-3 rounded-2xl border p-4 transition
                                {{ ($answers[$q['id']] ?? null) == $opt['id']
                                    ? 'bg-black text-white border-black'
                                    : 'hover:bg-slate-50' }}">
                                <div class="mt-1 h-4 w-4 rounded-full border flex items-center justify-center shrink-0
                                    {{ ($answers[$q['id']] ?? null) == $opt['id'] ? 'border-white' : 'border-slate-300' }}">
                                    @if(($answers[$q['id']] ?? null) == $opt['id'])
                                        <div class="h-2 w-2 rounded-full bg-white"></div>
                                    @endif
                                </div>
                                <span>{{ $opt['option_text'] }}</span>
                            </button>
                        @endforeach
                    </div>
                </section>

                <section class="flex items-center justify-between gap-3">
                    <button
                        wire:click="prev"
                        @disabled($currentIndex === 0)
                        class="px-4 py-2 rounded-xl border text-sm font-medium
                        {{ $currentIndex === 0 ? 'opacity-40 cursor-not-allowed' : 'hover:bg-slate-50' }}">
                        Previous
                    </button>

                    <button
                        wire:click="next"
                        @disabled($currentIndex === count($questions) - 1)
                        class="px-4 py-2 rounded-xl border text-sm font-medium
                        {{ $currentIndex === count($questions) - 1 ? 'opacity-40 cursor-not-allowed' : 'hover:bg-slate-50' }}">
                        Next
                    </button>
                </section>

                <section class="flex justify-end">
                    <button
                        type="button"
                        wire:click="$set('openSubmit', true)"
                        class="px-6 py-3 rounded-xl bg-black text-white text-sm font-medium hover:bg-slate-800"
                    >
                        Submit Answers
                    </button>
                </section>
            @endif
        </main>
    </section>

    @if($openSubmit)
        <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
            <div class="bg-white p-6 rounded-3xl w-full max-w-lg space-y-4 shadow-2xl">
                <div class="space-y-2">
                    <div class="text-xs uppercase tracking-wide text-slate-400">Confirm Submission</div>
                    <h3 class="text-lg font-semibold">Kirim jawaban sekarang?</h3>
                    <p class="text-sm text-slate-600 leading-6">
                        Jawaban akan dinilai otomatis. Jika belum lulus, Anda dapat mengulang attempt berikutnya sampai mencapai passing grade.
                    </p>
                </div>

                <div class="grid grid-cols-2 gap-3 text-sm">
                    <div class="rounded-2xl border bg-slate-50 p-4">
                        <div class="text-xs text-slate-500">Answered</div>
                        <div class="mt-1 font-semibold">{{ collect($answers)->filter()->count() }} / {{ count($questions) }}</div>
                    </div>
                    <div class="rounded-2xl border bg-slate-50 p-4">
                        <div class="text-xs text-slate-500">Passing</div>
                        <div class="mt-1 font-semibold">{{ $assessment->passing_grade }}%</div>
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <button
                        type="button"
                        wire:click="$set('openSubmit', false)"
                        class="px-4 py-2 border rounded-xl text-sm hover:bg-slate-50"
                    >
                        Cancel
                    </button>

                    <button
                        type="button"
                        wire:click="submit"
                        class="px-4 py-2 bg-black text-white rounded-xl text-sm hover:bg-slate-800"
                    >
                        Submit
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>