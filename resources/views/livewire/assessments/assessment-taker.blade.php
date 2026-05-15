<div wire:key="assessment-{{ $attempt->id }}-{{ $currentIndex }}" class="mx-auto max-w-6xl space-y-6 p-4 sm:p-6">

    @if($showIntro)
        <section class="space-y-4 rounded-3xl border bg-white p-6 shadow-sm">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                <div>
                    <div class="text-xs uppercase tracking-wide text-slate-400">
                        {{ $assessment->course?->title ?? __('general.assessment_taker.defaults.course_assessment') }}
                    </div>

                    <h1 class="mt-1 text-2xl font-bold">
                        {{ $assessment->title }}
                    </h1>

                    <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-500">
                        {{ __('general.assessment_taker.intro.description') }}
                    </p>
                </div>

                <div class="flex flex-wrap justify-end gap-2">
                    <span class="rounded-full border bg-slate-50 px-3 py-1 text-xs text-slate-600">
                        {{ __('general.assessment_taker.meta.attempt', ['no' => $attempt->attempt_no]) }}
                    </span>
                    <span class="rounded-full border bg-slate-50 px-3 py-1 text-xs text-slate-600">
                        {{ __('general.assessment_taker.meta.passing', ['grade' => $assessment->passing_grade]) }}
                    </span>
                    <span class="rounded-full border bg-slate-50 px-3 py-1 text-xs text-slate-600">
                        {{ $assessment->randomize_questions ? __('general.assessment_taker.meta.randomized') : __('general.assessment_taker.meta.fixed_order') }}
                    </span>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3 text-sm sm:grid-cols-4">
                <div class="rounded-xl border bg-slate-50 p-3">
                    <div class="text-xs text-slate-500">{{ __('general.assessment_taker.metrics.started') }}</div>
                    <div class="mt-1 font-semibold">{{ $attempt->started_at?->format('d M Y, H:i') ?? '-' }}</div>
                </div>

                <div class="rounded-xl border bg-slate-50 p-3">
                    <div class="text-xs text-slate-500">{{ __('general.assessment_taker.metrics.questions') }}</div>
                    <div class="mt-1 font-semibold">{{ $attempt->total_questions }}</div>
                </div>

                <div class="rounded-xl border bg-slate-50 p-3">
                    <div class="text-xs text-slate-500">{{ __('general.assessment_taker.metrics.state') }}</div>
                    <div class="mt-1 font-semibold">{{ strtoupper($attempt->status) }}</div>
                </div>

                <div class="rounded-xl border bg-slate-50 p-3">
                    <div class="text-xs text-slate-500">{{ __('general.assessment_taker.metrics.progress') }}</div>
                    <div class="mt-1 font-semibold">{{ collect($answers)->filter()->count() }} {{ __('general.assessment_taker.metrics.saved') }}</div>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 lg:grid-cols-[1fr_auto] lg:items-start">
                <div class="rounded-2xl border bg-slate-50 p-5">
                    <div class="mb-3 text-xs uppercase tracking-wide text-slate-400">
                        {{ __('general.assessment_taker.instructions.title') }}
                    </div>

                    <ul class="list-disc space-y-2 pl-5 text-sm text-slate-700">
                        <li>{{ __('general.assessment_taker.instructions.read_carefully') }}</li>
                        <li>{{ __('general.assessment_taker.instructions.auto_save') }}</li>
                        <li>{{ __('general.assessment_taker.instructions.submit_when_ready') }}</li>
                    </ul>
                </div>

                <div class="flex flex-wrap gap-3">
                    <button
                        type="button"
                        wire:click="beginQuiz"
                        class="rounded-xl bg-[#004777] px-5 py-3 text-sm font-medium text-white transition hover:bg-[#003560]"
                    >
                        {{ $hasExistingAttempt ? __('general.assessment_taker.actions.resume_test') : __('general.assessment_taker.actions.start_test') }}
                    </button>
                </div>
            </div>
        </section>
    @else

        <section class="space-y-4 rounded-3xl border bg-white p-6 shadow-sm">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                <div>
                    <div class="text-xs uppercase tracking-wide text-slate-400">
                        {{ $assessment->course?->title ?? __('general.assessment_taker.defaults.course_assessment') }}
                    </div>

                    <h1 class="mt-1 text-2xl font-bold">
                        {{ $assessment->title }}
                    </h1>

                    <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-500">
                        {{ __('general.assessment_taker.intro.description') }}
                    </p>
                </div>

                <div class="flex flex-wrap justify-end gap-2">
                    <span class="rounded-full border bg-slate-50 px-3 py-1 text-xs text-slate-600">
                        {{ __('general.assessment_taker.meta.attempt', ['no' => $attempt->attempt_no]) }}
                    </span>
                    <span class="rounded-full border bg-slate-50 px-3 py-1 text-xs text-slate-600">
                        {{ __('general.assessment_taker.meta.passing', ['grade' => $assessment->passing_grade]) }}
                    </span>
                    <span class="rounded-full border bg-slate-50 px-3 py-1 text-xs text-slate-600">
                        {{ $assessment->randomize_questions ? __('general.assessment_taker.meta.randomized') : __('general.assessment_taker.meta.fixed_order') }}
                    </span>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3 text-sm sm:grid-cols-4">
                <div class="rounded-xl border bg-slate-50 p-3">
                    <div class="text-xs text-slate-500">{{ __('general.assessment_taker.metrics.started') }}</div>
                    <div class="mt-1 font-semibold">{{ $attempt->started_at?->format('d M Y, H:i') ?? '-' }}</div>
                </div>

                <div class="rounded-xl border bg-slate-50 p-3">
                    <div class="text-xs text-slate-500">{{ __('general.assessment_taker.metrics.questions') }}</div>
                    <div class="mt-1 font-semibold">{{ count($questions) }}</div>
                </div>

                <div class="rounded-xl border bg-slate-50 p-3">
                    <div class="text-xs text-slate-500">{{ __('general.assessment_taker.metrics.answered') }}</div>
                    <div class="mt-1 font-semibold">{{ collect($answers)->filter()->count() }} / {{ count($questions) }}</div>
                </div>

                <div class="rounded-xl border bg-slate-50 p-3">
                    <div class="text-xs text-slate-500">{{ __('general.assessment_taker.metrics.state') }}</div>
                    <div class="mt-1 font-semibold">{{ strtoupper($attempt->status) }}</div>
                </div>
            </div>
        </section>

        <section class="grid grid-cols-1 gap-6 xl:grid-cols-[260px_1fr]">
            <aside class="sticky top-24 h-fit rounded-3xl border bg-white p-5 shadow-sm">
                <div class="mb-3 flex items-center justify-between">
                    <h2 class="font-semibold">{{ __('general.assessment_taker.navigator.title') }}</h2>
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
                            class="h-10 rounded-xl border text-sm font-medium transition
                            {{ $isActive
                                ? 'border-black bg-black text-white'
                                : ($isAnswered
                                    ? 'border-emerald-300 bg-emerald-100 text-emerald-700'
                                    : 'bg-white hover:bg-slate-50') }}"
                        >
                            {{ $i + 1 }}
                        </button>
                    @endforeach
                </div>

                <div class="mt-4 rounded-2xl border bg-slate-50 p-4 text-sm leading-6 text-slate-600">
                    {{ __('general.assessment_taker.navigator.note') }}
                </div>
            </aside>

            <main class="space-y-5">
                @php $q = $questions[$currentIndex] ?? null; @endphp

                @if($q)
                    <section wire:key="question-{{ $q['id'] }}" class="space-y-5 rounded-3xl border bg-white p-6 shadow-sm">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <div class="text-xs uppercase tracking-wide text-slate-400">
                                    {{ __('general.assessment_taker.question.label', ['current' => $currentIndex + 1, 'total' => count($questions)]) }}
                                </div>

                                <h2 class="mt-2 text-xl font-semibold">
                                    {{ $q['question'] }}
                                </h2>
                            </div>

                            <span class="rounded-full border bg-slate-100 px-2 py-1 text-xs">
                                {{ __('general.assessment_taker.question.type') }}
                            </span>
                        </div>

                        <div class="space-y-3">
                            @foreach($q['options'] as $opt)
                                <button
                                    type="button"
                                    wire:click="selectOption('{{ $q['id'] }}', '{{ $opt['id'] }}')"
                                    class="flex w-full items-start gap-3 rounded-2xl border p-4 text-left transition
                                    {{ ($answers[$q['id']] ?? null) == $opt['id']
                                        ? 'border-black bg-black text-white'
                                        : 'hover:bg-slate-50' }}">
                                    <div class="mt-1 flex h-4 w-4 shrink-0 items-center justify-center rounded-full border
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
                            class="rounded-xl border px-4 py-2 text-sm font-medium transition
                            {{ $currentIndex === 0 ? 'cursor-not-allowed opacity-40' : 'hover:bg-slate-50' }}">
                            {{ __('general.assessment_taker.actions.previous') }}
                        </button>

                        <button
                            wire:click="next"
                            @disabled($currentIndex === count($questions) - 1)
                            class="rounded-xl border px-4 py-2 text-sm font-medium transition
                            {{ $currentIndex === count($questions) - 1 ? 'cursor-not-allowed opacity-40' : 'hover:bg-slate-50' }}">
                            {{ __('general.assessment_taker.actions.next') }}
                        </button>
                    </section>

                    <section class="flex justify-end">
                        <button
                            type="button"
                            wire:click="$set('openSubmit', true)"
                            class="rounded-xl bg-black px-6 py-3 text-sm font-medium text-white hover:bg-slate-800"
                        >
                            {{ __('general.assessment_taker.actions.submit_answers') }}
                        </button>
                    </section>
                @endif
            </main>
        </section>

        @if($openSubmit)
            <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
                <div class="w-full max-w-lg space-y-4 rounded-3xl bg-white p-6 shadow-2xl">
                    <div class="space-y-2">
                        <div class="text-xs uppercase tracking-wide text-slate-400">
                            {{ __('general.assessment_taker.submit_modal.label') }}
                        </div>
                        <h3 class="text-lg font-semibold">{{ __('general.assessment_taker.submit_modal.title') }}</h3>
                        <p class="text-sm leading-6 text-slate-600">
                            {{ __('general.assessment_taker.submit_modal.description') }}
                        </p>
                    </div>

                    <div class="grid grid-cols-2 gap-3 text-sm">
                        <div class="rounded-2xl border bg-slate-50 p-4">
                            <div class="text-xs text-slate-500">{{ __('general.assessment_taker.metrics.answered') }}</div>
                            <div class="mt-1 font-semibold">{{ collect($answers)->filter()->count() }} / {{ count($questions) }}</div>
                        </div>
                        <div class="rounded-2xl border bg-slate-50 p-4">
                            <div class="text-xs text-slate-500">{{ __('general.assessment_taker.meta.passing_label') }}</div>
                            <div class="mt-1 font-semibold">{{ $assessment->passing_grade }}%</div>
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <button
                            type="button"
                            wire:click="$set('openSubmit', false)"
                            class="rounded-xl border px-4 py-2 text-sm hover:bg-slate-50"
                        >
                            {{ __('general.assessment_taker.actions.cancel') }}
                        </button>

                        <button
                            type="button"
                            wire:click="submit"
                            class="rounded-xl bg-black px-4 py-2 text-sm text-white hover:bg-slate-800"
                        >
                            {{ __('general.assessment_taker.actions.submit') }}
                        </button>
                    </div>
                </div>
            </div>
        @endif
    @endif
</div>