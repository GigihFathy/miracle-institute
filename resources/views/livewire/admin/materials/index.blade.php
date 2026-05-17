<div x-data="{ open: @entangle('showModal').live }" class="space-y-6">
    <x-ui.page-header
        title="{{ __('admin.materials.page_title') }}"
        subtitle="{{ __('admin.materials.page_subtitle') }}"
    />

    @if($selectedTopic)
        <div class="space-y-4">
            <div class="rounded-2xl border bg-white p-4">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <h2 class="font-semibold">{{ $selectedTopic->course?->title }} · {{ $selectedTopic->name }}</h2>
                        @if($selectedTopic->description)
                            <p class="text-xs text-slate-500">{{ Str::limit($selectedTopic->description, 200) }}</p>
                        @endif
                    </div>
                    <div class="flex items-center gap-2">
                            <a href="{{ localized_route('admin.topics.index', ['courseFilter' => $selectedTopic->course_id]) }}" class="rounded-xl border px-4 py-2 text-sm">
                                Back
                        </a>
                        @if(($this->isTopicFull[$selectedTopic->id] ?? false))
                            <button class="rounded-xl bg-slate-300 px-4 py-2 text-sm text-slate-500" disabled>
                                {{ __('admin.materials.actions.add') }}
                                <span class="ml-1 rounded-full bg-red-100 px-1.5 py-0.5 text-[10px] font-semibold text-red-700">MAX 5</span>
                            </button>
                        @else
                            <button wire:click="create('{{ $selectedTopic->id }}')" class="rounded-xl border border-brand-dark/20 bg-transparent px-4 py-2 text-sm text-brand-dark transition hover:bg-brand/10">
                                {{ __('admin.materials.actions.add') }}
                            </button>
                        @endif
                    </div>
                </div>
            </div>

            <div class="overflow-hidden rounded-2xl border bg-white">
                <div class="p-4">
                    <table class="w-full text-sm">
                        <thead class="border-b bg-white">
                            <tr>
                                <th class="p-4 text-left">{{ __('admin.materials.table.name') }}</th>
                                <th class="p-4">{{ __('admin.materials.table.type') }}</th>
                                <th class="p-4">{{ __('admin.materials.table.source') }}</th>
                                <th class="p-4">{{ __('admin.materials.table.visibility') }}</th>
                                <th class="p-4">{{ __('admin.materials.table.status') }}</th>
                                <th class="p-4">{{ __('admin.materials.table.action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($selectedTopic->materials as $row)
                                <tr class="border-t hover:bg-slate-50">
                                    <td class="p-4">
                                        <div class="font-medium">{{ $row->name }}</div>
                                        <div class="text-xs text-slate-500">{{ __('admin.materials.sort_order', ['count' => $row->sort_order]) }}</div>
                                    </td>
                                    <td class="p-4">{{ strtoupper($row->type) }}</td>
                                    <td class="break-all p-4 text-xs text-slate-500">{{ $row->path ?: $row->external_url }}</td>
                                    <td class="p-4">{{ __('admin.materials.visibility.' . $row->visibility, [], $row->visibility) }}</td>
                                    <td class="p-4">{{ __('admin.materials.status.' . $row->status, [], $row->status) }}</td>
                                    <td class="p-4">
                                        <div class="flex gap-3">
                                            <button wire:click="edit('{{ $row->id }}')" class="text-sm text-blue-600">{{ __('admin.materials.actions.edit') }}</button>
                                                <button wire:click="delete('{{ $row->id }}')" wire:confirm="Delete this material?" class="text-sm text-rose-600">{{ __('admin.materials.actions.delete') }}</button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="p-6 text-center text-slate-500">{{ __('admin.materials.empty_materials') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    <template x-teleport="body">
        <div x-show="open"
            x-cloak
            x-transition
            @click.self="open = false; $wire.set('showModal', false)"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">

            <div class="w-full max-w-lg space-y-4 rounded-2xl bg-white p-6 shadow-xl">

                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold">{{ $editingId ? __('admin.materials.modal.edit_title') : __('admin.materials.modal.create_title') }}</h2>
                    <button @click="open = false; $wire.set('showModal', false)" class="text-slate-500">✕</button>
                </div>

                <div class="space-y-3">
                    @if($selectedTopic)
                        <input
                            value="{{ $selectedTopic->course?->title }} · {{ $selectedTopic->name }}"
                            disabled
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2 text-slate-600"
                        >
                    @else
                        <select wire:model.live="topic_id" class="w-full rounded-xl border px-4 py-2">
                            <option value="">{{ __('admin.materials.form.select_topic') }}</option>
                            @foreach($topics as $t)
                                <option value="{{ $t->id }}">{{ $t->course?->title }} · {{ $t->name }}</option>
                            @endforeach
                        </select>
                    @endif

                    <input wire:model.live="name" class="w-full rounded-xl border px-4 py-2" placeholder="{{ __('admin.materials.form.name_placeholder') }}">

                    <select
                        wire:model.live="type"
                        wire:key="material-type-{{ $topic_id ?? 'new' }}-{{ $editingId ?? 'create' }}"
                        class="w-full rounded-xl border px-4 py-2"
                    >
                        <option value="">{{ __('admin.materials.form.select_type') }}</option>
                        @foreach($this->availableTypes as $opt)
                            <option value="{{ $opt }}">{{ strtoupper($opt) }}</option>
                        @endforeach
                        @if($editingId && $type && !in_array($type, $this->availableTypes, true))
                            <option value="{{ $type }}">{{ strtoupper($type) }} (current)</option>
                        @endif
                    </select>

                    @if($type === 'video')
                        <input wire:model.live="external_url"
                            class="w-full rounded-xl border px-4 py-2"
                            placeholder="{{ __('admin.materials.form.external_url_placeholder') }}">
                    @elseif(in_array($type, ['pdf', 'ppt'], true))
                        <input type="file" wire:model="materialFile" class="w-full rounded-xl border px-4 py-2">
                    @endif

                    <div class="grid grid-cols-2 gap-3">
                        <select wire:model.live="visibility" class="rounded-xl border px-4 py-2">
                            <option value="public">{{ __('admin.materials.visibility.public') }}</option>
                            <option value="private">{{ __('admin.materials.visibility.private') }}</option>
                        </select>

                        <select wire:model.live="status" class="rounded-xl border px-4 py-2">
                            <option value="active">{{ __('admin.materials.status.active') }}</option>
                            <option value="inactive">{{ __('admin.materials.status.inactive') }}</option>
                        </select>
                    </div>

                    <input wire:model.live="sort_order" type="number" min="0"
                        class="w-full rounded-xl border px-4 py-2" placeholder="{{ __('admin.materials.form.sort_order_placeholder') }}">
                </div>

                <div wire:loading wire:target="materialFile,save" class="mb-5 w-full">
                    <div class="flex animate-pulse gap-4 rounded-xl border border-slate-200 bg-slate-50 p-4">
                        <div class="h-10 w-10 rounded-full bg-slate-200"></div>
                        <div class="flex-1 space-y-3 py-1">
                            <div class="h-3 w-3/4 rounded bg-slate-200"></div>
                            <div class="space-y-2">
                                <div class="h-3 w-5/6 rounded bg-slate-200"></div>
                                <div class="h-3 w-1/2 rounded bg-slate-200"></div>
                            </div>
                        </div>
                    </div>
                </div>

                @error('topic_id') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                @error('name') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                @error('type') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                @error('external_url') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                @error('materialFile') <p class="text-sm text-red-600">{{ $message }}</p> @enderror

                <div class="flex justify-end gap-2 pt-3">
                    <button @click="open = false; $wire.set('showModal', false)" class="rounded-xl border px-4 py-2">
                        {{ __('admin.materials.actions.cancel') }}
                    </button>
                    <button wire:click="save"
                            wire:loading.attr="disabled"
                            class="rounded-xl border border-brand-dark/20 bg-transparent px-4 py-2 text-brand-dark transition hover:bg-brand/10">
                        {{ $uploading ? __('admin.materials.actions.uploading') : __('admin.materials.actions.save') }}
                    </button>
                </div>
            </div>
        </div>
    </template>
</div>