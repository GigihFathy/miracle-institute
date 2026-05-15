<div
    x-data="{
        open: @entangle('showModal').live,
        deleteOpen: @entangle('showDeleteModal').live
    }"
    class="space-y-6"
>
    <x-ui.page-header title="{{ __('admin.study_programs.page_title') }}" subtitle="{{ __('admin.study_programs.page_subtitle') }}">
        <button wire:click="create" class="rounded-xl bg-slate-900 px-4 py-2 text-sm text-white transition hover:bg-slate-800">
            {{ __('admin.study_programs.actions.create') }}
        </button>
    </x-ui.page-header>

    <section class="space-y-4">
        <div class="rounded-2xl border bg-white p-4">
            <div class="grid grid-cols-1 gap-3 md:grid-cols-3">
                <input wire:model.live="search" class="w-full rounded-xl border px-4 py-2" placeholder="{{ __('admin.study_programs.search_placeholder') }}">

                <select wire:model.live="perPage" class="w-full rounded-xl border px-4 py-2 md:col-start-3">
                    <option value="5">{{ trans_choice('admin.study_programs.per_page', 5) }}</option>
                    <option value="10">{{ trans_choice('admin.study_programs.per_page', 10) }}</option>
                    <option value="20">{{ trans_choice('admin.study_programs.per_page', 20) }}</option>
                </select>
            </div>
        </div>

        <div class="overflow-hidden rounded-2xl border bg-white">
            <div class="overflow-x-auto">
                <table class="min-w-full w-full text-sm">
                    <thead class="bg-slate-50 text-left text-slate-600">
                        <tr>
                            <th class="px-4 py-3 font-medium">{{ __('admin.study_programs.table.title') }}</th>
                            <th class="px-4 py-3 font-medium">{{ __('admin.study_programs.table.status') }}</th>
                            <th class="px-4 py-3 text-right font-medium">{{ __('admin.study_programs.table.action') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse($rows as $row)
                            <tr class="transition-colors hover:bg-slate-50/60">
                                <td class="px-4 py-3">
                                    <div class="font-medium text-slate-900">{{ $row->title }}</div>
                                    <div class="mt-1 text-xs text-slate-500">{{ \Illuminate\Support\Str::limit($row->description, 80) }}</div>
                                </td>

                                <td class="whitespace-nowrap px-4 py-3">
                                    @if($row->status === 'active')
                                        <span class="inline-flex items-center rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-medium text-emerald-700 ring-1 ring-emerald-200">
                                            {{ __('admin.study_programs.status.active') }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center rounded-full bg-rose-100 px-2.5 py-1 text-xs font-medium text-rose-700 ring-1 ring-rose-200">
                                            {{ __('admin.study_programs.status.inactive') }}
                                        </span>
                                    @endif
                                </td>

                                <td class="px-4 py-3">
                                    <div class="flex justify-end gap-2">
                                        <div class="relative group">
                                            <button
                                                wire:click="edit('{{ $row->id }}')"
                                                class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-blue-50 text-blue-600 transition hover:bg-blue-100"
                                                title="{{ __('admin.study_programs.actions.edit') }}"
                                            >
                                                <span class="sr-only">{{ __('admin.study_programs.actions.edit') }}</span>
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-4 w-4">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a2.25 2.25 0 1 1 3.182 3.182L10.582 17.13a4.5 4.5 0 0 1-1.897 1.13L6 19l.74-2.685a4.5 4.5 0 0 1 1.13-1.897L16.862 4.487ZM16.862 4.487 19.5 7.125" />
                                                </svg>
                                            </button>
                                            <span class="pointer-events-none absolute -top-9 left-1/2 -translate-x-1/2 whitespace-nowrap rounded-md bg-slate-900 px-2 py-1 text-[11px] font-medium text-white opacity-0 transition group-hover:opacity-100 group-focus-within:opacity-100">
                                                {{ __('admin.study_programs.actions.edit') }}
                                            </span>
                                        </div>

                                        <div class="relative group">
                                            <button
                                                wire:click="confirmDelete('{{ $row->id }}')"
                                                class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-rose-50 text-rose-600 transition hover:bg-rose-100"
                                                title="{{ __('admin.study_programs.actions.delete') }}"
                                            >
                                                <span class="sr-only">{{ __('admin.study_programs.actions.delete') }}</span>
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-4 w-4">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673A2.25 2.25 0 0 1 15.916 21.75H8.084a2.25 2.25 0 0 1-2.245-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                                </svg>
                                            </button>
                                            <span class="pointer-events-none absolute -top-9 left-1/2 -translate-x-1/2 whitespace-nowrap rounded-md bg-slate-900 px-2 py-1 text-[11px] font-medium text-white opacity-0 transition group-hover:opacity-100 group-focus-within:opacity-100">
                                                {{ __('admin.study_programs.actions.delete') }}
                                            </span>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-4 py-8 text-center text-slate-500">
                                    {{ __('admin.study_programs.empty') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div>{{ $rows->links() }}</div>
    </section>

    <template x-teleport="body">
        <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center">
            <div class="absolute inset-0 bg-black/50"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @click="open = false"></div>

            <div @click.stop
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="relative mx-4 w-full max-w-xl space-y-4 rounded-2xl bg-white p-6 shadow-xl">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold">
                        {{ $editingId ? __('admin.study_programs.modal.edit_title') : __('admin.study_programs.modal.create_title') }}
                    </h2>

                    <button @click="open = false" class="text-slate-500 hover:text-black">✕</button>
                </div>

                <div class="space-y-3">
                    <div>
                        <input wire:model="title" class="w-full rounded-xl border px-4 py-2" placeholder="{{ __('admin.study_programs.form.title_placeholder') }}">
                        @error('title')
                            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <textarea wire:model="description" rows="4" class="w-full rounded-xl border px-4 py-2" placeholder="{{ __('admin.study_programs.form.description_placeholder') }}"></textarea>
                        @error('description')
                            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <select wire:model="status" class="w-full rounded-xl border px-4 py-2">
                            <option value="active">{{ __('admin.study_programs.status.active') }}</option>
                            <option value="inactive">{{ __('admin.study_programs.status.inactive') }}</option>
                        </select>
                        @error('status')
                            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <button @click="open = false" class="rounded-xl border px-4 py-2 hover:bg-slate-50">
                        {{ __('admin.study_programs.actions.cancel') }}
                    </button>

                    <button wire:click="save" class="rounded-xl bg-slate-900 px-4 py-2 text-white hover:bg-slate-800">
                        {{ __('admin.study_programs.actions.save') }}
                    </button>
                </div>
            </div>
        </div>
    </template>

    <template x-teleport="body">
        <div x-show="deleteOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center">
            <div class="absolute inset-0 bg-black/50"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @click="deleteOpen = false"></div>

            <div @click.stop
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="relative mx-4 w-full max-w-md space-y-4 rounded-2xl bg-white p-6 shadow-xl">
                <h2 class="text-lg font-semibold text-slate-900">
                    {{ __('admin.study_programs.delete.title') }}
                </h2>
                <p class="text-sm text-slate-600">
                    {{ __('admin.study_programs.delete.subtitle') }}
                </p>

                <div class="flex justify-end gap-2 pt-2">
                    <button @click="deleteOpen = false" class="rounded-xl border px-4 py-2 hover:bg-slate-50">
                        {{ __('admin.study_programs.actions.cancel') }}
                    </button>

                    <button wire:click="delete" class="rounded-xl bg-rose-600 px-4 py-2 text-white hover:bg-rose-700">
                        {{ __('admin.study_programs.actions.delete') }}
                    </button>
                </div>
            </div>
        </div>
    </template>
</div>