<div class="mx-auto w-full max-w-7xl space-y-6 px-4 py-6 sm:px-6 lg:px-8">
    <section class="overflow-hidden rounded-3xl border border-[color:color-mix(in_oklab,#004777_12%,white)] bg-white shadow-[0_20px_50px_color-mix(in_oklab,#004777_8%,transparent)]">
        <div class="relative overflow-hidden bg-gradient-to-br from-[var(--mentor-primary)] to-[#0a659b] px-5 py-6 text-white sm:px-7">
            <div class="relative flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
                <div class="space-y-2">
                    <div class="text-xs font-semibold uppercase tracking-[0.2em] text-white/65">
                        {{ $topic->course?->title }}
                    </div>
                    <h1 class="text-2xl font-bold tracking-tight text-white sm:text-3xl">
                        {{ $topic->name }}
                    </h1>
                    <p class="max-w-3xl text-sm leading-6 text-white/75">
                        {{ __('mentor.topic_workspace.subtitle') }}
                    </p>
                </div>

            </div>
        </div>

        <div class="grid grid-cols-1 gap-3 px-5 py-5 text-sm sm:grid-cols-3 sm:px-7">
            <div class="mentor-workspace-card p-4">
                <div class="text-xs font-semibold uppercase tracking-wide text-[color:color-mix(in_oklab,#004777_55%,white)]">{{ __('mentor.topic_workspace.cards.topic') }}</div>
                <div class="mt-1 font-semibold text-mentor-primary">{{ strtoupper($topic->status) }}</div>
            </div>
            <div class="mentor-workspace-card p-4">
                <div class="text-xs font-semibold uppercase tracking-wide text-[color:color-mix(in_oklab,#004777_55%,white)]">{{ __('mentor.topic_workspace.cards.course') }}</div>
                <div class="mt-1 font-semibold text-mentor-primary">{{ $topic->course?->title ?? '-' }}</div>
            </div>
        </div>

        <div class="border-t border-slate-100 px-3 pt-3 sm:px-5">
            <div class="flex gap-2 overflow-x-auto">
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
