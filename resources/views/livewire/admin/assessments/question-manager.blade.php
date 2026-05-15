<div x-data="{ open: @entangle('openModal').live }" class="space-y-6">
    <x-ui.page-header
        title="{{ __('admin.question_manager.page_title') }}"
        subtitle="{{ $assessment->course?->title ?? '-' }} · {{ $assessment->title }}"
    >
        <div class="flex gap-2">
            <a href="{{ localized_route('admin.assessments.index', ['courseFilter' => $assessment->course_id]) }}"
               class="rounded-xl border px-4 py-2 text-sm">
                {{ __('admin.question_manager.actions.back') }}
            </a>

            <button wire:click="create"
                class="rounded-xl bg-slate-900 px-4 py-2 text-sm text-white">
                {{ __('admin.question_manager.actions.create') }}
            </button>
        </div>
    </x-ui.page-header>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-5">
        <div class="rounded-2xl border bg-white p-5">
            <div class="text-xs text-slate-500">{{ __('admin.question_manager.stats.course') }}</div>
            <div class="mt-1 text-lg font-bold">{{ $assessment->course?->title ?? '-' }}</div>
        </div>

        <div class="rounded-2xl border bg-white p-5">
            <div class="text-xs text-slate-500">{{ __('admin.question_manager.stats.questions') }}</div>
            <div class="mt-1 text-2xl font-bold">{{ number_format($questionsCount) }}</div>
        </div>

        <div class="rounded-2xl border bg-white p-5">
            <div class="text-xs text-slate-500">{{ __('admin.question_manager.stats.attempts') }}</div>
            <div class="mt-1 text-2xl font-bold">{{ number_format($attemptsCount) }}</div>
        </div>

        <div class="rounded-2xl border bg-white p-5">
            <div class="text-xs text-slate-500">{{ __('admin.question_manager.stats.passing_grade') }}</div>
            <div class="mt-1 text-2xl font-bold">{{ $assessment->passing_grade }}</div>
        </div>
    </div>

    <div class="overflow-hidden rounded-2xl border bg-white">
        <table class="w-full text-sm">
            <thead class="bg-slate-50">
                <tr>
                    <th class="p-4 text-left">{{ __('admin.question_manager.table.question') }}</th>
                    <th class="p-4 text-left">{{ __('admin.question_manager.table.options') }}</th>
                    <th class="p-4">{{ __('admin.question_manager.table.order') }}</th>
                    <th class="p-4">{{ __('admin.question_manager.table.action') }}</th>
                </tr>
            </thead>

            <tbody>
                @forelse($questions as $q)
                    <tr class="align-top border-t">
                        <td class="p-4">
                            <div class="font-medium">{{ $q->question }}</div>
                            <div class="text-xs text-slate-500">{{ $q->question_type }}</div>
                        </td>

                        <td class="space-y-1 p-4 text-xs">
                            @foreach($q->options as $opt)
                                <div class="{{ $opt->is_correct ? 'font-semibold text-emerald-600' : 'text-slate-500' }}">
                                    {{ $opt->is_correct ? '✓' : '•' }} {{ $opt->option_text }}
                                </div>
                            @endforeach
                        </td>

                        <td class="p-4 text-center">{{ $q->sort_order }}</td>

                        <td class="p-4">
                            <div class="flex justify-center gap-2">
                                <button wire:click="edit('{{ $q->id }}')"
                                    class="rounded-lg bg-blue-100 px-3 py-1.5 text-xs text-blue-700 hover:bg-blue-200">
                                    {{ __('admin.question_manager.actions.edit') }}
                                </button>

                                <button wire:click="delete('{{ $q->id }}')"
                                    class="rounded-lg bg-red-100 px-3 py-1.5 text-xs text-red-700 hover:bg-red-200">
                                    {{ __('admin.question_manager.actions.delete') }}
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="p-6 text-center text-slate-500">
                            {{ __('admin.question_manager.empty') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <template x-teleport="body">
        <div
            x-show="open"
            x-cloak
            x-transition
            @click.self="open = false"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
        >
            <div class="flex max-h-[90vh] w-full max-w-xl flex-col rounded-2xl bg-white shadow-xl">

                <div class="flex items-center justify-between border-b p-5">
                    <h2 class="text-lg font-semibold">
                        {{ $editingId ? __('admin.question_manager.modal.edit_title') : __('admin.question_manager.modal.create_title') }}
                    </h2>

                    <button @click="open = false" class="text-slate-500 hover:text-black">
                        ✕
                    </button>
                </div>

                <div class="space-y-4 overflow-y-auto p-5">
                    <textarea wire:model="question"
                        rows="4"
                        class="w-full rounded-xl border px-4 py-2"
                        placeholder="{{ __('admin.question_manager.form.question_placeholder') }}"></textarea>

                    <div class="space-y-3">
                        <div class="text-sm font-medium">{{ __('admin.question_manager.form.options_title') }}</div>

                        @foreach($options as $i => $opt)
                            <div class="flex items-center gap-3">
                                <button
                                    type="button"
                                    wire:click="$set('correctIndex', {{ $i }})"
                                    class="flex h-5 w-5 items-center justify-center rounded border {{ $correctIndex === $i ? 'bg-emerald-600 text-white' : 'bg-white' }}"
                                >
                                    @if($correctIndex === $i) ✓ @endif
                                </button>

                                <input type="text"
                                    wire:model="options.{{ $i }}.option_text"
                                    class="w-full rounded-lg border px-3 py-2"
                                    placeholder="{{ __('admin.question_manager.form.option_placeholder', ['number' => $i + 1]) }}">
                            </div>
                        @endforeach
                    </div>

                    <input wire:model="sort_order"
                        type="number"
                        class="w-full rounded-xl border px-4 py-2"
                        placeholder="{{ __('admin.question_manager.form.sort_order_placeholder') }}">
                </div>

                <div class="flex justify-end gap-3 border-t bg-slate-50 p-5">
                    <button @click="open = false"
                        class="rounded-xl border px-4 py-2">
                        {{ __('admin.question_manager.actions.cancel') }}
                    </button>

                    <button wire:click="save"
                        class="rounded-xl bg-slate-900 px-4 py-2 text-white">
                        {{ __('admin.question_manager.actions.save') }}
                    </button>
                </div>
            </div>
        </div>
    </template>
</div>