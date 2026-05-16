<div x-data="{ open: @entangle('showModal').live }" class="mx-auto max-w-6xl space-y-6 px-4">
    <x-ui.page-header
        title="{{ __('admin.courses.page_title') }}"
        subtitle="{{ __('admin.courses.page_subtitle') }}"
    >
        <div>
            <button wire:click="create"
                class="rounded-xl bg-slate-900 px-4 py-2 text-sm text-white">
                {{ __('admin.courses.actions.create') }}
            </button>
        </div>
    </x-ui.page-header>

    <section class="space-y-4">
        <div class="rounded-2xl border bg-white p-4 space-y-3">
            <div class="grid grid-cols-1 gap-3 md:grid-cols-4">
                <input wire:model.live="search"
                       class="rounded-xl border px-4 py-2"
                       placeholder="{{ __('admin.courses.search_placeholder') }}">

                <select wire:model.live="studyProgramFilter"
                        class="rounded-xl border px-4 py-2">
                    <option value="">{{ __('admin.courses.filters.all_programs') }}</option>
                    @foreach($studyPrograms as $sp)
                        <option value="{{ $sp->id }}">{{ $sp->title }}</option>
                    @endforeach
                </select>

                <select wire:model.live="statusFilter"
                        class="rounded-xl border px-4 py-2">
                    <option value="">{{ __('admin.courses.filters.all_status') }}</option>
                    <option value="active">{{ __('admin.courses.status.active') }}</option>
                    <option value="inactive">{{ __('admin.courses.status.inactive') }}</option>
                </select>

                <select wire:model.live="perPage"
                        class="rounded-xl border px-4 py-2">
                  <option value="3">{{ trans_choice('admin.courses.per_page', 3) }}</option>
                  <option value="5">{{ trans_choice('admin.courses.per_page', 5) }}</option>
                  <option value="10">{{ trans_choice('admin.courses.per_page', 10) }}</option>
                </select>
            </div>
        </div>

        <x-ui.table-shell class="table-auto">
            <thead class="bg-slate-50 text-left">
                <tr>
                    <th class="whitespace-nowrap px-4 py-3 font-medium text-slate-600">{{ __('admin.courses.table.course') }}</th>
                    <th class="whitespace-nowrap px-4 py-3 font-medium text-slate-600">{{ __('admin.courses.table.program') }}</th>
                    <th class="whitespace-nowrap px-4 py-3 font-medium text-slate-600">{{ __('admin.courses.table.topics') }}</th>
                    <th class="whitespace-nowrap px-4 py-3 font-medium text-slate-600">{{ __('admin.courses.table.enrollments') }}</th>
                    <th class="whitespace-nowrap px-4 py-3 font-medium text-slate-600">{{ __('admin.courses.table.certificates') }}</th>
                    <th class="whitespace-nowrap px-4 py-3 font-medium text-slate-600">{{ __('admin.courses.table.status') }}</th>
                    <th class="whitespace-nowrap px-4 py-3 font-medium text-slate-600">{{ __('admin.courses.table.action') }}</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-slate-100 bg-white">
                @forelse($rows as $row)
                    <tr class="align-top">
                        <td class="px-4 py-3">
                            <div class="font-medium text-slate-900">{{ $row->title }}</div>
                            <div class="text-xs text-slate-500">{{ $row->slug }}</div>
                        </td>

                        <td class="whitespace-nowrap px-4 py-3">{{ $row->studyProgram?->title }}</td>
                        <td class="whitespace-nowrap px-4 py-3">{{ $row->topics_count }}</td>
                        <td class="whitespace-nowrap px-4 py-3">{{ $row->enrollments_count }}</td>
                        <td class="whitespace-nowrap px-4 py-3">{{ $row->certificates_count }}</td>

                        <td class="whitespace-nowrap px-4 py-3">
                            @php $s = $row->status; @endphp
                            <span class="rounded-full px-2 py-1 text-xs {{ $s === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-700' }}">
                                {{ __('admin.courses.status.' . $s) }}
                            </span>
                        </td>

                        <td class="px-4 py-3">
                            <div class="flex flex-wrap gap-2">
                                <a href="{{ localized_route('admin.topics.index', ['courseFilter' => $row->id]) }}"
                                   class="rounded-lg bg-slate-100 px-3 py-1.5 text-xs hover:bg-slate-200">
                                    {{ __('admin.courses.actions.topics') }}
                                </a>

                                <a href="{{ localized_route('admin.assessments.index', ['courseFilter' => $row->id]) }}"
                                   class="rounded-lg bg-slate-100 px-3 py-1.5 text-xs hover:bg-slate-200">
                                    {{ __('admin.courses.actions.assessments') }}
                                </a>

                                <div class="my-1 w-full border-t"></div>

                                <button wire:click="edit('{{ $row->id }}')" title="{{ __('admin.courses.actions.edit') }}"
                                        class="rounded-lg bg-slate-100 p-2 text-xs hover:bg-slate-200">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536M4 13.5V20h6.5L20.5 9.999l-6.5-6.5L4 13.5z" />
                                    </svg>
                                </button>

                                <button wire:click="delete('{{ $row->id }}')" title="{{ __('admin.courses.actions.delete') }}"
                                        onclick="confirm('{{ __('admin.courses.confirm_delete') }}') || event.stopImmediatePropagation()"
                                        class="rounded-lg bg-slate-100 p-2 text-xs hover:bg-slate-200">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3" />
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-6 text-center text-slate-500">
                            {{ __('admin.courses.empty') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </x-ui.table-shell>

        <div>{{ $rows->links() }}</div>
    </section>

    <template x-teleport="body">
        <div
            x-show="open"
            x-cloak
            x-transition
            @click.self="open = false; $wire.set('showModal', false)"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
        >
            <div @click.stop class="flex max-h-[90vh] w-full max-w-2xl flex-col overflow-hidden rounded-2xl bg-white shadow-2xl">
                <div class="flex shrink-0 items-center justify-between border-b bg-white p-6">
                    <h2 class="text-lg font-semibold">{{ $editingId ? __('admin.courses.modal.edit_title') : __('admin.courses.modal.create_title') }}</h2>
                    <button @click="open = false; $wire.set('showModal', false)" class="text-slate-500 hover:text-black">✕</button>
                </div>

                <div class="flex-1 space-y-4 overflow-y-auto p-6">
                    <select wire:model="study_program_id" class="w-full rounded-xl border px-4 py-2">
                        <option value="">{{ __('admin.courses.form.select_program') }}</option>
                        @foreach($studyPrograms as $sp)
                            <option value="{{ $sp->id }}">{{ $sp->title }}</option>
                        @endforeach
                    </select>

                    <input wire:model="title" class="w-full rounded-xl border px-4 py-2" placeholder="{{ __('admin.courses.form.title_placeholder') }}">

                    <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-xs text-slate-500">{{ __('admin.courses.form.poster_preview') }}</label>
                            <div class="flex h-40 w-full items-center justify-center overflow-hidden rounded-xl border bg-slate-100">
                                @if($poster)
                                    <img src="{{ asset($poster) }}" alt="poster" class="h-full w-full object-contain">
                                @else
                                    <div class="text-xs text-slate-400">{{ __('admin.courses.form.no_poster') }}</div>
                                @endif
                            </div>
                        </div>

                        <div>
                            <label class="mb-1 block text-xs text-slate-500">{{ __('admin.courses.form.choose_thumbnail') }}</label>
                            <div id="file:thumbnail" class="grid max-h-40 grid-cols-4 gap-2 overflow-auto rounded-lg border p-1">
                                @foreach($thumbnails as $t)
                                    <button type="button" wire:click="selectThumbnail('{{ $t }}')" class="overflow-hidden rounded-md border p-0 {{ $poster === $t ? 'ring-2 ring-slate-900' : '' }}">
                                        <img src="{{ asset($t) }}" class="h-16 w-full object-cover" alt="thumb">
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <textarea wire:model="description" rows="4" class="w-full rounded-xl border px-4 py-2" placeholder="{{ __('admin.courses.form.description_placeholder') }}"></textarea>

                    <select wire:model="status" class="w-full rounded-xl border px-4 py-2">
                        <option value="active">{{ __('admin.courses.status.active') }}</option>
                        <option value="inactive">{{ __('admin.courses.status.inactive') }}</option>
                    </select>
                </div>

                <div class="flex shrink-0 justify-end gap-2 border-t bg-slate-50 p-6">
                    <button @click="open = false; $wire.set('showModal', false)" class="rounded-xl border px-4 py-2">
                        {{ __('admin.courses.actions.cancel') }}
                    </button>
                    <button wire:click="save" class="rounded-xl bg-slate-900 px-4 py-2 text-white">
                        {{ __('admin.courses.actions.save') }}
                    </button>
                </div>
            </div>
        </div>
    </template>
</div>