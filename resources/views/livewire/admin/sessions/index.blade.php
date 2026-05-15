<div x-data="{ open: @entangle('showModal').live }" class="mx-auto max-w-6xl space-y-6 px-4">
    <x-ui.page-header
        title="{{ __('admin.sessions.page_title') }}"
        subtitle="{{ __('admin.sessions.page_subtitle') }}"
    >
        <div>
            <button wire:click="create"
                class="rounded-xl bg-slate-900 px-4 py-2 text-sm text-white">
                {{ __('admin.sessions.actions.create') }}
            </button>
        </div>
    </x-ui.page-header>

    <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 xl:grid-cols-5">
        @foreach([
            'Total' => $stats['total'],
            'Scheduled' => $stats['scheduled'],
            'Ongoing' => $stats['ongoing'],
            'Completed' => $stats['completed'],
            'Cancelled' => $stats['cancelled'],
        ] as $label => $value)
            <div class="rounded-2xl border bg-white p-4">
                <div class="text-[11px] text-slate-500">{{ __('admin.sessions.stats.' . strtolower($label)) }}</div>
                <div class="mt-1 text-lg font-bold">{{ number_format($value) }}</div>
            </div>
        @endforeach
    </div>

    <div class="space-y-4">
        <div class="rounded-2xl border bg-white p-4 space-y-3">
            <div class="grid grid-cols-1 gap-3 md:grid-cols-4">
                <input wire:model.live="search"
                    class="rounded-xl border px-3 py-2 text-xs"
                    placeholder="{{ __('admin.sessions.search_placeholder') }}">

                <select wire:model.live="courseFilter" class="rounded-xl border px-3 py-2 text-xs">
                    <option value="">{{ __('admin.sessions.filters.all_courses') }}</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}">{{ $course->title }}</option>
                    @endforeach
                </select>

                <select wire:model.live="topicFilter" class="rounded-xl border px-3 py-2 text-xs">
                    <option value="">{{ __('admin.sessions.filters.all_topics') }}</option>
                    @foreach($topics as $topic)
                        <option value="{{ $topic->id }}">
                            {{ $topic->course?->title }} · {{ $topic->name }}
                        </option>
                    @endforeach
                </select>

                <select wire:model.live="statusFilter" class="rounded-xl border px-3 py-2 text-xs">
                    <option value="">{{ __('admin.sessions.filters.all_status') }}</option>
                    <option value="scheduled">{{ __('admin.sessions.status.scheduled') }}</option>
                    <option value="ongoing">{{ __('admin.sessions.status.ongoing') }}</option>
                    <option value="completed">{{ __('admin.sessions.status.completed') }}</option>
                    <option value="cancelled">{{ __('admin.sessions.status.cancelled') }}</option>
                </select>
            </div>
        </div>

        <x-ui.table-shell class="table-auto">
            <thead class="bg-slate-50 text-left">
                <tr>
                    <th class="whitespace-nowrap px-4 py-3 font-medium text-slate-600">{{ __('admin.sessions.table.course') }}</th>
                    <th class="whitespace-nowrap px-4 py-3 font-medium text-slate-600">{{ __('admin.sessions.table.title') }}</th>
                    <th class="whitespace-nowrap px-4 py-3 font-medium text-slate-600">{{ __('admin.sessions.table.schedule') }}</th>
                    <th class="whitespace-nowrap px-4 py-3 text-center font-medium text-slate-600">{{ __('admin.sessions.table.status') }}</th>
                    <th class="whitespace-nowrap px-4 py-3 text-center font-medium text-slate-600">{{ __('admin.sessions.table.attend') }}</th>
                    <th class="whitespace-nowrap px-4 py-3 font-medium text-slate-600">{{ __('admin.sessions.table.action') }}</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-slate-100 bg-white">
                @forelse($rows as $row)
                    <tr class="align-top">
                        <td class="max-w-[180px] whitespace-nowrap truncate px-4 py-3">
                            {{ $row->topic?->course?->title }}
                        </td>
                        <td class="max-w-[220px] whitespace-nowrap truncate px-4 py-3 font-medium text-slate-900">
                            {{ $row->title }}
                        </td>
                        <td class="whitespace-nowrap px-4 py-3">
                            <div>{{ $row->start_at?->format('d M H:i') }}</div>
                            <div class="text-xs text-slate-500">→ {{ $row->end_at?->format('H:i') }}</div>
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-center">
                            <span class="rounded-full bg-slate-100 px-2 py-1 text-xs">
                                {{ __('admin.sessions.status.' . $row->status, [], $row->status) }}
                            </span>
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-center">
                            {{ $row->attendances->count() }}
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex flex-wrap gap-2">
                                <a href="{{ localized_route('admin.attendances.index', ['sessionFilter' => $row->id]) }}"
                                   class="rounded-md bg-slate-100 px-2 py-1 text-xs hover:bg-slate-200">
                                    {{ __('admin.sessions.actions.attend') }}
                                </a>

                                <div class="my-1 w-full border-t"></div>

                                <button wire:click="edit('{{ $row->id }}')"
                                    class="rounded-md bg-blue-100 px-2 py-1 text-xs text-blue-700 hover:bg-blue-200">
                                    {{ __('admin.sessions.actions.edit') }}
                                </button>

                                <button wire:click="delete('{{ $row->id }}')"
                                    class="rounded-md bg-rose-100 px-2 py-1 text-xs text-rose-700 hover:bg-rose-200">
                                    {{ __('admin.sessions.actions.delete') }}
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-6 text-center text-slate-500">
                            {{ __('admin.sessions.empty') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </x-ui.table-shell>

        <div>{{ $rows->links() }}</div>
    </div>

    <template x-teleport="body">
        <div x-show="open"
             x-cloak
             x-transition
             @click.self="open = false; $wire.set('showModal', false)"
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4">

            <div class="max-h-[90vh] w-full max-w-xl space-y-4 overflow-y-auto rounded-2xl bg-white p-6 shadow-xl">

                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold">
                        {{ $editingId ? __('admin.sessions.modal.edit_title') : __('admin.sessions.modal.create_title') }}
                    </h2>
                    <button @click="open = false; $wire.set('showModal', false)">✕</button>
                </div>

                <div class="space-y-2">
                    <label class="text-xs font-medium text-slate-600">{{ __('admin.sessions.form.topic_label') }}</label>

                    <input
                        wire:model.live.debounce.300ms="topicSearch"
                        class="w-full rounded-xl border px-4 py-2"
                        placeholder="{{ __('admin.sessions.form.topic_search_placeholder') }}"
                    >

                    <div class="flex items-center justify-between text-xs text-slate-500">
                        <span>{{ __('admin.sessions.form.topic_helper') }}</span>

                        @if($topic_id)
                            <button type="button"
                                wire:click="clearTopicSelection"
                                class="underline hover:text-slate-700">
                                {{ __('admin.sessions.actions.clear') }}
                            </button>
                        @endif
                    </div>

                    @if($topic_id && $selectedTopic)
                        <div class="text-xs text-slate-600">
                            {{ __('admin.sessions.form.selected') }}:
                            <span class="font-medium">
                                {{ $selectedTopic->course?->title }} · {{ $selectedTopic->name }}
                            </span>
                        </div>
                    @endif

                    @if($showTopicResults)
                        <div class="max-h-56 overflow-y-auto rounded-xl border divide-y">
                            @forelse($topicOptions as $topic)
                                <button
                                    type="button"
                                    wire:key="topic-option-{{ $topic->id }}"
                                    wire:click="selectTopic('{{ $topic->id }}')"
                                    class="flex w-full items-center justify-between gap-3 px-4 py-3 text-left hover:bg-slate-50"
                                >
                                    <span class="text-sm text-slate-700">
                                        {{ $topic->course?->title }} · {{ $topic->name }}
                                    </span>

                                    @if($topic_id === $topic->id)
                                        <span class="rounded-full bg-slate-900 px-2 py-1 text-[11px] text-white">
                                            {{ __('admin.sessions.selected') }}
                                        </span>
                                    @endif
                                </button>
                            @empty
                                @if(filled($topicSearch))
                                    <div class="px-4 py-4 text-sm text-slate-500">
                                        {{ __('admin.sessions.no_matching_topics') }}
                                    </div>
                                @endif
                            @endforelse
                        </div>
                    @endif

                    @error('topic_id') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <input wire:model.live="title" class="w-full rounded-xl border px-4 py-2" placeholder="{{ __('admin.sessions.form.title_placeholder') }}">
                <input wire:model.live="zoom_link" class="w-full rounded-xl border px-4 py-2" placeholder="{{ __('admin.sessions.form.zoom_placeholder') }}">

                <div class="grid grid-cols-2 gap-3">
                    <input wire:model.live="start_at" type="datetime-local" class="rounded-xl border px-4 py-2">
                    <input wire:model.live="end_at" type="datetime-local" class="rounded-xl border px-4 py-2">
                </div>

                <div class="space-y-3 rounded-xl border bg-slate-50 px-4 py-3">
                    <div>
                        <div class="text-[11px] uppercase tracking-wide text-slate-500">
                            {{ __('admin.sessions.form.status_title') }}
                        </div>

                        <div class="mt-1 inline-flex items-center rounded-full border bg-white px-3 py-1 text-sm font-semibold text-slate-700">
                            {{ __('admin.sessions.status.' . $status, [], ucfirst($status)) }}
                        </div>
                    </div>

                    <div class="space-y-1 border-t pt-3 text-[11px] leading-relaxed text-slate-500">
                        <div><span class="font-medium text-slate-600">{{ __('admin.sessions.status.scheduled') }}</span> → {{ __('admin.sessions.status_desc.scheduled') }}</div>
                        <div><span class="font-medium text-slate-600">{{ __('admin.sessions.status.ongoing') }}</span> → {{ __('admin.sessions.status_desc.ongoing') }}</div>
                        <div><span class="font-medium text-slate-600">{{ __('admin.sessions.status.completed') }}</span> → {{ __('admin.sessions.status_desc.completed') }}</div>
                        <div><span class="font-medium text-slate-600">{{ __('admin.sessions.status.cancelled') }}</span> → {{ __('admin.sessions.status_desc.cancelled') }}</div>
                    </div>
                </div>

                @error('title') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                @error('zoom_link') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                @error('start_at') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                @error('end_at') <p class="text-sm text-red-600">{{ $message }}</p> @enderror

                <div class="flex justify-end gap-2">
                    <button @click="open = false; $wire.set('showModal', false)"
                        class="rounded-xl border px-4 py-2">
                        {{ __('admin.sessions.actions.cancel') }}
                    </button>

                    <button wire:click="save"
                        class="rounded-xl bg-slate-900 px-4 py-2 text-white">
                        {{ __('admin.sessions.actions.save') }}
                    </button>
                </div>
            </div>
        </div>
    </template>
</div>