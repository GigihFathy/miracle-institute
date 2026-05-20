<div class="space-y-6 lg:px-36">
    <section class="rounded-2xl border border-slate-200 bg-white px-6 pt-6 shadow-[0_18px_40px_color-mix(in_oklab,#004777_10%,transparent)]">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
            <div class="space-y-2">
                <div class="text-xs uppercase tracking-[0.18em] text-[color:color-mix(in_oklab,#004777_45%,white)]">
                    {{ __('mentor.topic_workspace.header') }} · {{ $topic->course?->title }}
                </div>
                <h1 class="text-2xl font-bold text-mentor-primary">
                    {{ $topic->name }}
                </h1>
                <p class="max-w-3xl text-sm text-[color:color-mix(in_oklab,#004777_72%,white)]">
                    {{ __('mentor.topic_workspace.subtitle') }}
                </p>
            </div>

            <div class="flex gap-3">
                <a href="{{ localized_route('topics.show', $topic->slug) }}"
                   class="rounded-xl border border-slate-200 bg-mentor-primary-soft-2 px-4 py-2 text-sm font-medium text-mentor-primary transition hover:bg-mentor-secondary">
                    {{ __('mentor.topic_workspace.visit_topic') }}
                </a>
            </div>
        </div>

        <div class="mt-6 grid grid-cols-2 gap-3 text-sm sm:grid-cols-3">
            <div class="rounded-xl border border-slate-200 bg-mentor-primary-soft-2 p-4">
                <div class="text-xs uppercase tracking-wide text-[color:color-mix(in_oklab,#004777_50%,white)]">{{ __('mentor.topic_workspace.cards.topic') }}</div>
                <div class="mt-1 font-semibold text-mentor-primary">{{ strtoupper($topic->status) }}</div>
            </div>
            <div class="rounded-xl border border-slate-200 bg-mentor-primary-soft-2 p-4">
                <div class="text-xs uppercase tracking-wide text-[color:color-mix(in_oklab,#004777_50%,white)]">{{ __('mentor.topic_workspace.cards.course') }}</div>
                <div class="mt-1 font-semibold text-mentor-primary">{{ $topic->course?->title ?? '-' }}</div>
            </div>
            <div class="rounded-xl border border-slate-200 bg-mentor-primary-soft-2 p-4">
                <div class="text-xs uppercase tracking-wide text-[color:color-mix(in_oklab,#004777_50%,white)]">{{ __('mentor.topic_workspace.cards.program') }}</div>
                <div class="mt-1 font-semibold text-mentor-primary">{{ $topic->course?->studyProgram?->title ?? '-' }}</div>
            </div>
        </div>

        <div class="mt-6">
            <div class="-mb-px flex gap-2 overflow-x-auto">
                @foreach($tabs as $key => $label)
                    <button type="button"
                            wire:click="setTab('{{ $key }}')"
                            class="mentor-tab-button whitespace-nowrap
                                {{ $tab === $key
                                    ? 'mentor-tab-button-active'
                                    : '' }}">
                        {{ $label }}
                    </button>
                @endforeach
            </div>
        </div>
    </section>

    @livewire($activeComponent, ['topicId' => $topic->id], key($activeComponent . '-' . $topic->id))
</div>
