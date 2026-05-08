<div wire:key="assessment-{{ $attempt->id }}-{{ $currentIndex }}" class="max-w-6xl mx-auto space-y-6 p-4 sm:p-6">

    {{-- HEADER --}}
    <section class="rounded-3xl bg-white border p-6 shadow-sm space-y-4">
        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">

            <div>
                <h1 class="text-2xl font-bold">
                    {{ $assessment->title }}
                </h1>

                <p class="text-sm text-slate-500 mt-1">
                    {{ $assessment->topic?->course?->title }} · {{ $assessment->topic?->name }}
                </p>
            </div>

            {{-- TIMER --}}
            @if($timeLeft !== null)
                <div wire:poll.1s="tick"
                     class="text-right text-red-600 font-semibold text-lg">
                    {{ $this->formattedTime }}
                </div>
            @endif
        </div>

        {{-- META --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-sm">
            <div class="rounded-xl border p-3 bg-slate-50">
                <div class="text-xs text-slate-500">Time Limit</div>
                <div class="font-semibold mt-1">
                    {{ $timeLimit ? $timeLimit . ' min' : 'No limit' }}
                </div>
            </div>

            <div class="rounded-xl border p-3 bg-slate-50">
                <div class="text-xs text-slate-500">Start</div>
                <div class="font-semibold mt-1">
                    {{ $attempt->started_at?->format('d M Y, H:i') }}
                </div>
            </div>

            <div class="rounded-xl border p-3 bg-slate-50">
                <div class="text-xs text-slate-500">Questions</div>
                <div class="font-semibold mt-1">
                    {{ count($questions) }}
                </div>
            </div>

            <div class="rounded-xl border p-3 bg-slate-50">
                <div class="text-xs text-slate-500">Passing</div>
                <div class="font-semibold mt-1">
                    {{ $assessment->passing_grade }}
                </div>
            </div>
        </div>
    </section>

    <section class="grid grid-cols-1 xl:grid-cols-[260px_1fr] gap-6">

        {{-- NAVIGATOR --}}
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
                        $isAnswered = !empty($answers[$question->id]);
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
        </aside>

        {{-- MAIN --}}
        <main class="space-y-5">

            @php $q = $questions[$currentIndex] ?? null; @endphp

            @if($q)
                <section wire:key="question-{{ $q->id }}" class="bg-white border rounded-3xl p-6 shadow-sm space-y-5">

                    {{-- QUESTION --}}
                    <div class="flex justify-between items-start gap-4">
                        <div>
                            <div class="text-xs uppercase tracking-wide text-slate-400">
                                Question {{ $currentIndex + 1 }} of {{ count($questions) }}
                            </div>

                            <h2 class="text-xl font-semibold mt-2">
                                {{ $q->question }}
                            </h2>
                        </div>

                        <span class="text-xs px-2 py-1 rounded-full bg-slate-100">
                            {{ strtoupper($q->question_type) }}
                        </span>
                    </div>

                    {{-- MCQ --}}
                    @if($q->question_type === 'mcq')
                        <div class="space-y-3">
                            @foreach($q->options as $opt)
                                <label class="flex items-start gap-3 rounded-2xl border p-4 cursor-pointer transition
                                    {{ ($answers[$q->id] ?? null) == $opt->id 
                                        ? 'bg-black text-white border-black' 
                                        : 'hover:bg-slate-50' }}">

                                    <input type="radio"
                                           name="q_{{ $attempt->id }}_{{ $q->id }}"
                                           value="{{ $opt->id }}"
                                           wire:change="selectOption('{{ $q->id }}','{{ $opt->id }}')"
                                           {{ ($answers[$q->id] ?? null) == $opt->id ? 'checked' : '' }}
                                           class="mt-1">
                                    <span>{{ $opt->option_text }}</span>
                                </label>
                            @endforeach
                        </div>
                    @endif

                    {{-- TEXT --}}
                    @if($q->question_type !== 'mcq')
                        <div>
                            <textarea
                                class="w-full rounded-2xl border px-4 py-3 min-h-32 focus:ring-2 focus:ring-black/10"
                                wire:change="saveTextAnswer('{{ $q->id }}', $event.target.value)"
                            >{{ $answers[$q->id] ?? '' }}</textarea>
                        </div>
                    @endif

                </section>

                {{-- NAV BUTTON --}}
                <section class="flex items-center justify-between">

                    <button wire:click="prev"
                        class="px-4 py-2 rounded-xl border text-sm font-medium
                        {{ $currentIndex === 0 ? 'opacity-40 cursor-not-allowed' : 'hover:bg-slate-50' }}">
                        Previous
                    </button>

                    <button wire:click="next"
                        class="px-4 py-2 rounded-xl border text-sm font-medium
                        {{ $currentIndex === count($questions)-1 ? 'opacity-40 cursor-not-allowed' : 'hover:bg-slate-50' }}">
                        Next
                    </button>

                </section>

                {{-- SUBMIT --}}
                <section class="flex justify-end">
                    <button wire:click="$set('openSubmit', true)"
                            class="px-6 py-3 rounded-xl bg-primary text-white text-sm font-medium">
                        Submit Answers
                    </button>
                </section>

            @endif

        </main>
    </section>

    {{-- MODAL --}}
    @if($openSubmit)
        <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
            <div class="bg-white p-6 rounded-2xl w-full max-w-md space-y-4 shadow-xl">

                <h3 class="text-lg font-semibold">
                    Konfirmasi Submit
                </h3>

                <p class="text-sm text-slate-600">
                    Jawaban tidak bisa diubah setelah dikirim.
                </p>

                <div class="flex justify-end gap-2">
                    <button wire:click="$set('openSubmit', false)"
                            class="px-4 py-2 border rounded-xl">
                        Cancel
                    </button>

                    <button wire:click="submit"
                            class="px-4 py-2 bg-primary text-white rounded-xl">
                        Submit
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>