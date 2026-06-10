<div class="space-y-6">
    <x-ui.page-header
        title="{{ __('admin.sessions.page_title') }}"
        subtitle="{{ __('admin.sessions.page_subtitle') }}"
    >
        <div class="flex items-center gap-2">
            @if($selectedFilterTopic)
                <a href="{{ localized_route('admin.topics.index', ['courseFilter' => $selectedFilterTopic->course_id]) }}"
                   class="rounded-xl border px-4 py-2 text-sm">
                    Kembali
                </a>

                @if($selectedFilterSession)
                    <button wire:click="edit('{{ $selectedFilterSession->id }}')"
                        class="admin-edit-button rounded-xl border border-brand-dark/20 px-4 py-2 text-sm transition">
                        {{ __('admin.sessions.actions.edit') }}
                    </button>
                @else
                    <button wire:click="create"
                        class="admin-primary-button rounded-xl border border-brand-dark/20 px-4 py-2 text-sm transition">
                        {{ __('admin.sessions.actions.create') }}
                    </button>
                @endif
            @else
                <button wire:click="create"
                    class="admin-primary-button rounded-xl border border-brand-dark/20 px-4 py-2 text-sm transition">
                    {{ __('admin.sessions.actions.create') }}
                </button>
            @endif
        </div>
    </x-ui.page-header>

    {{-- scope banner removed per UI simplification request --}}

    @if($selectedFilterTopic)
        <div class="space-y-4">
            @if($selectedFilterSession)
                <div class="rounded-2xl border bg-white p-4">
                    <div class="flex flex-wrap items-start justify-between gap-4">
                        <div>
                            <h2 class="text-lg font-semibold text-slate-900">{{ $selectedFilterSession->title }}</h2>
                            <p class="text-sm text-slate-500">{{ $selectedFilterTopic->course?->title }} · {{ $selectedFilterTopic->name }}</p>
                            <p class="mt-2 text-sm text-slate-600">{{ $selectedFilterSession->zoom_link }}</p>
                        </div>

                        <div class="rounded-2xl bg-slate-50 px-4 py-3 text-sm text-slate-700">
                            <div><span class="font-medium">{{ __('admin.sessions.table.schedule') }}:</span> {{ $selectedFilterSession->start_at?->format('d M Y H:i') }} → {{ $selectedFilterSession->end_at?->format('H:i') }}</div>
                            <div class="mt-1"><span class="font-medium">{{ __('admin.sessions.table.status') }}:</span> {{ __('admin.sessions.status.' . $selectedFilterSession->status, [], $selectedFilterSession->status) }}</div>
                        </div>
                    </div>
                </div>

                <div class="space-y-3">
                    <div class="text-sm font-semibold text-slate-700">
                        Kehadiran Sesi
                    </div>
                    <x-ui.table-shell class="table-auto">
                        <thead class="admin-table-head text-left">
                            <tr>
                                <th class="px-4 py-3 font-medium text-slate-600">Pengguna</th>
                                <th class="px-4 py-3 font-medium text-slate-600">Status</th>
                                <th class="px-4 py-3 font-medium text-slate-600">Check In</th>
                                <th class="px-4 py-3 font-medium text-slate-600">Check Out</th>
                                <th class="px-4 py-3 font-medium text-slate-600">IP</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @forelse($selectedFilterSession->attendances as $attendance)
                                <tr class="align-top">
                                    <td class="px-4 py-3">
                                        <div class="font-medium text-slate-900">{{ $attendance->user?->full_name }}</div>
                                        <div class="text-xs text-slate-500">{{ $attendance->user?->email }}</div>
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3">
                                        <span class="rounded-full bg-slate-100 px-2 py-1 text-xs">
                                            {{ __('admin.attendances.status.' . $attendance->status, [], $attendance->status) }}
                                        </span>
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3">{{ $attendance->check_in_at?->format('d M Y H:i') ?? '-' }}</td>
                                    <td class="whitespace-nowrap px-4 py-3">{{ $attendance->clock_out_at?->format('d M Y H:i') ?? '-' }}</td>
                                    <td class="px-4 py-3 text-xs text-slate-500">{{ $attendance->ip_address ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-6 text-center text-slate-500">Belum ada kehadiran.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </x-ui.table-shell>
                </div>
            @else
                <div class="rounded-2xl border bg-white p-6 text-center text-slate-500">
                    {{ __('admin.sessions.empty') }}
                </div>
            @endif
        </div>
    @else
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
                <thead class="admin-table-head text-left">
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
                            <td class="max-w-44 whitespace-nowrap truncate px-4 py-3">
                                {{ $row->topic?->course?->title }}
                            </td>
                            <td class="max-w-56 whitespace-nowrap truncate px-4 py-3 font-medium text-slate-900">
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
                                    <a href="{{ localized_route('admin.sessions.index', ['topicFilter' => $row->topic_id]) }}"
                                       class="admin-primary-button rounded-md px-2 py-1 text-xs">
                                        Buka
                                    </a>

                                    <div class="my-1 w-full border-t"></div>

                                    <div class="relative group">
                                        <button wire:click="edit('{{ $row->id }}')"
                                            class="admin-edit-button inline-flex h-9 w-9 items-center justify-center rounded-lg transition"
                                            title="{{ __('admin.sessions.actions.edit') }}">
                                            <span class="sr-only">{{ __('admin.sessions.actions.edit') }}</span>
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-4 w-4">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a2.25 2.25 0 1 1 3.182 3.182L10.582 17.13a4.5 4.5 0 0 1-1.897 1.13L6 19l.74-2.685a4.5 4.5 0 0 1 1.13-1.897L16.862 4.487ZM16.862 4.487 19.5 7.125" />
                                            </svg>
                                        </button>
                                        <span class="pointer-events-none absolute -top-9 left-1/2 -translate-x-1/2 whitespace-nowrap rounded-md border border-brand-dark/20 bg-white px-2 py-1 text-[11px] font-medium text-brand-dark opacity-0 transition group-hover:opacity-100 group-focus-within:opacity-100">
                                            {{ __('admin.sessions.actions.edit') }}
                                        </span>
                                    </div>

                                    <div class="relative group">
                                        <button wire:click="delete('{{ $row->id }}')"
                                            class="admin-delete-button inline-flex h-9 w-9 items-center justify-center rounded-lg transition"
                                            title="{{ __('admin.sessions.actions.delete') }}">
                                            <span class="sr-only">{{ __('admin.sessions.actions.delete') }}</span>
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-4 w-4">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673A2.25 2.25 0 0 1 15.916 21.75H8.084a2.25 2.25 0 0 1-2.245-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                            </svg>
                                        </button>
                                        <span class="pointer-events-none absolute -top-9 left-1/2 -translate-x-1/2 whitespace-nowrap rounded-md border border-brand-dark/20 bg-white px-2 py-1 text-[11px] font-medium text-brand-dark opacity-0 transition group-hover:opacity-100 group-focus-within:opacity-100">
                                            {{ __('admin.sessions.actions.delete') }}
                                        </span>
                                    </div>
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
    @endif

    @if($showModal)
        <div
             wire:click.self="$set('showModal', false)"
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4">

            <div class="max-h-[90vh] w-full max-w-2xl space-y-4 overflow-y-auto rounded-2xl bg-white p-6 shadow-xl">

                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold">
                        {{ $editingId ? __('admin.sessions.modal.edit_title') : __('admin.sessions.modal.create_title') }}
                    </h2>
                    <button type="button" wire:click="$set('showModal', false)">✕</button>
                </div>

                <div class="space-y-2">
                    <div class="rounded-xl bg-slate-50 px-4 py-3 text-xs text-slate-500">
                        <span class="font-semibold text-rose-500">*</span> menandakan field wajib diisi.
                    </div>

                    @if($errors->any())
                        <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                            Periksa kembali field yang wajib diisi.
                        </div>
                    @endif

                    <label class="text-xs font-medium text-slate-600">{{ __('admin.sessions.form.topic_label') }} <span class="text-rose-500">*</span></label>

                    @if($selectedFilterTopic)
                        <input
                            value="{{ $selectedFilterTopic->course?->title }} · {{ $selectedFilterTopic->name }}"
                            disabled
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2 text-slate-600"
                        >
                    @else
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
                                            <span class="rounded-full border border-brand-dark/20 bg-white px-2 py-1 text-[11px] text-brand-dark">
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
                    @endif

                    @error('topic_id') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-600">Judul Sesi <span class="text-rose-500">*</span></label>
                    <input wire:model.live="title" class="w-full rounded-xl border px-4 py-2" placeholder="{{ __('admin.sessions.form.title_placeholder') }}">
                    @error('title') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-600">Tautan Zoom <span class="text-rose-500">*</span></label>
                    <input wire:model.live="zoom_link" class="w-full rounded-xl border px-4 py-2" placeholder="{{ __('admin.sessions.form.zoom_placeholder') }}">
                    @error('zoom_link') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="mb-1 block text-xs font-semibold text-slate-600">Mulai <span class="text-rose-500">*</span></label>
                        <input wire:model.live="start_at" type="datetime-local" class="w-full rounded-xl border px-4 py-2">
                        @error('start_at') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold text-slate-600">Selesai <span class="text-rose-500">*</span></label>
                        <input wire:model.live="end_at" type="datetime-local" class="w-full rounded-xl border px-4 py-2">
                        @error('end_at') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>
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

                <div class="flex justify-end gap-2">
                    <button type="button" wire:click="$set('showModal', false)"
                        wire:loading.attr="disabled"
                        wire:target="save"
                        class="rounded-xl border px-4 py-2 disabled:cursor-not-allowed disabled:opacity-60">
                        {{ __('admin.sessions.actions.cancel') }}
                    </button>

                    <button wire:click="save"
                        wire:loading.attr="disabled"
                        wire:target="save"
                        class="admin-primary-button inline-flex min-w-[9rem] items-center justify-center gap-2 rounded-xl border border-brand-dark/20 px-4 py-2 transition disabled:cursor-not-allowed disabled:opacity-70">
                        <span wire:loading.remove wire:target="save">
                            {{ __('admin.sessions.actions.save') }}
                        </span>
                        <span wire:loading.inline-flex wire:target="save" class="items-center gap-2">
                            <svg class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-90" fill="currentColor" d="M4 12a8 8 0 0 1 8-8v4a4 4 0 0 0-4 4H4z"></path>
                            </svg>
                            <span>Menyimpan...</span>
                        </span>
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
