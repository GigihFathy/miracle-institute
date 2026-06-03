<div wire:key="assessment-{{ $attempt->id }}-{{ $currentIndex }}" class="mx-auto max-w-6xl space-y-6 p-4 sm:p-6">

    @if($showIntro)
        <section class="overflow-hidden rounded-3xl border border-slate-200 bg-white">
            <div class="bg-mentor-primary px-6 py-6 text-white">
                <div class="max-w-3xl">
                    <div class="text-xs uppercase tracking-[0.18em]">
                        {{ $assessment->course?->title ?? __('general.assessment_taker.defaults.course_assessment') }}
                    </div>

                    <h1 class="mt-1 text-2xl font-bold">
                        {{ $assessment->title }}
                    </h1>

                    <p class="mt-2 max-w-3xl text-sm leading-6">
                        {{ __('general.assessment_taker.intro.description') }}
                    </p>
                </div>
            </div>

            <div class="flex flex-col gap-4 p-6 lg:flex-row lg:items-start lg:justify-between">
                <div class="space-y-3">
                    <div class="flex flex-wrap gap-3 text-sm text-slate-600">
                        <div>
                            <span class="text-slate-500">{{ __('general.assessment_taker.metrics.started') }}:</span>
                            <span class="font-medium text-mentor-primary">{{ $attempt->started_at?->format('d M Y, H:i') ?? '-' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-500">{{ __('general.assessment_taker.metrics.questions') }}:</span>
                            <span class="font-medium text-mentor-primary">{{ $attempt->total_questions }}</span>
                        </div>
                        <div>
                            <span class="text-slate-500">{{ __('general.assessment_taker.meta.passing_label') }}:</span>
                            <span class="font-medium text-mentor-primary">{{ $assessment->passing_grade }}%</span>
                        </div>
                    </div>

                    <div>
                        <div class="mb-2 text-xs uppercase tracking-wide text-slate-400">
                            {{ __('general.assessment_taker.instructions.title') }}
                        </div>

                        <ul class="list-disc space-y-2 pl-5 text-sm text-slate-700">
                            <li>{{ __('general.assessment_taker.instructions.read_carefully') }}</li>
                            <li>{{ __('general.assessment_taker.instructions.auto_save') }}</li>
                            <li>{{ __('general.assessment_taker.instructions.submit_when_ready') }}</li>
                        </ul>
                    </div>
                </div>

                <div class="flex flex-wrap gap-3">
                    <button
                        type="button"
                        wire:click="beginQuiz"
                        class="admin-primary-button rounded-xl px-5 py-3 text-sm"
                    >
                        {{ $hasExistingAttempt ? __('general.assessment_taker.actions.resume_test') : __('general.assessment_taker.actions.start_test') }}
                    </button>
                </div>
            </div>
        </section>
    @else

        <section class="overflow-hidden rounded-3xl border border-slate-200 bg-white">
            <div class="bg-mentor-primary px-6 py-6 text-white">
                <div class="max-w-3xl">
                    <div class="text-xs uppercase tracking-[0.18em]">
                        {{ $assessment->course?->title ?? __('general.assessment_taker.defaults.course_assessment') }}
                    </div>

                    <h1 class="mt-1 text-2xl font-bold">
                        {{ $assessment->title }}
                    </h1>

                    <p class="mt-2 max-w-3xl text-sm leading-6">
                        {{ __('general.assessment_taker.intro.description') }}
                    </p>
                </div>
            </div>

            <div class="flex flex-wrap gap-x-5 gap-y-2 p-6 text-sm text-slate-600">
                <div>
                    <span class="text-slate-500">{{ __('general.assessment_taker.metrics.started') }}:</span>
                    <span class="font-medium text-mentor-primary">{{ $attempt->started_at?->format('d M Y, H:i') ?? '-' }}</span>
                </div>

                <div>
                    <span class="text-slate-500">{{ __('general.assessment_taker.metrics.questions') }}:</span>
                    <span class="font-medium text-mentor-primary">{{ count($questions) }}</span>
                </div>

                <div>
                    <span class="text-slate-500">{{ __('general.assessment_taker.meta.passing_label') }}:</span>
                    <span class="font-medium text-mentor-primary">{{ $assessment->passing_grade }}%</span>
                </div>
            </div>
        </section>

        <section class="grid grid-cols-1 gap-6 xl:grid-cols-[260px_1fr]">
            <aside class="sticky top-24 h-fit rounded-3xl border border-slate-200 bg-white p-5">
                <div class="mb-3 flex items-center justify-between">
                    <h2 class="font-semibold">{{ __('general.assessment_taker.navigator.title') }}</h2>
                    <span class="text-xs text-slate-500">
                        {{ $this->answeredCount }} / {{ count($questions) }}
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
                                ? 'border-mentor-primary bg-mentor-primary text-white'
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
                    <section wire:key="question-{{ $q['id'] }}" class="space-y-5 rounded-3xl border border-slate-200 bg-white p-6">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <h2 class="text-xl font-semibold text-mentor-primary">
                                    {{ $currentIndex + 1 }}. {{ $q['question'] }}
                                </h2>
                            </div>
                        </div>

                        <div class="space-y-3">
                            @foreach($q['options'] as $opt)
                                <button
                                    type="button"
                                    wire:click="selectOption('{{ $q['id'] }}', '{{ $opt['id'] }}')"
                                    class="flex w-full items-start gap-3 rounded-2xl border p-4 text-left transition
                                    {{ ($answers[$q['id']] ?? null) == $opt['id']
                                        ? 'border-mentor-primary bg-mentor-primary text-white'
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
                            class="admin-primary-button rounded-xl px-6 py-3 text-sm"
                        >
                            {{ __('general.assessment_taker.actions.submit_answers') }}
                        </button>
                    </section>

                    @error('answers')
                        <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                            {{ $message }}
                        </div>
                    @enderror
                @endif
            </main>
        </section>

        @if($openSubmit)
            <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
                <div class="w-full max-w-lg space-y-4 rounded-3xl bg-white p-6">
                    <div class="space-y-2">
                        <h3 class="text-lg font-semibold">{{ __('general.assessment_taker.submit_modal.title') }}</h3>
                        <p class="text-sm leading-6 text-slate-600">
                            {{ __('general.assessment_taker.submit_modal.description') }}
                        </p>
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <button
                            type="button"
                            wire:click="$set('openSubmit', false)"
                            class="admin-neutral-button rounded-xl px-4 py-2 text-sm"
                        >
                            {{ __('general.assessment_taker.actions.cancel') }}
                        </button>

                        <button
                            type="button"
                            wire:click="submit"
                            @disabled(! $this->allQuestionsAnswered)
                            class="admin-primary-button rounded-xl px-4 py-2 text-sm disabled:opacity-50"
                        >
                            {{ __('general.assessment_taker.actions.submit') }}
                        </button>
                    </div>
                </div>
            </div>
        @endif
    @endif
</div>
