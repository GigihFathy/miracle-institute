<div x-data="{ open: @entangle('showModal').live }" class="space-y-6">
    <x-ui.page-header
        title="{{ __('admin.materials.page_title') }}"
        subtitle="{{ __('admin.materials.page_subtitle') }}"
    />

    <div class="grid grid-cols-1 gap-3 md:grid-cols-5">
        <input wire:model.live="search" class="w-full rounded-xl border px-4 py-2" placeholder="{{ __('admin.materials.search_placeholder') }}">

        <select wire:model.live="courseFilter" class="rounded-xl border px-4 py-2">
            <option value="">{{ __('admin.materials.filters.all_courses') }}</option>
            @foreach($courses as $course)
                <option value="{{ $course->id }}">{{ $course->title }}</option>
            @endforeach
        </select>

        <select wire:model.live="topicFilter" class="rounded-xl border px-4 py-2">
            <option value="">{{ __('admin.materials.filters.all_topics') }}</option>
            @foreach($topics as $topic)
                <option value="{{ $topic->id }}">{{ $topic->course?->title }} · {{ $topic->name }}</option>
            @endforeach
        </select>

        <select wire:model.live="typeFilter" class="rounded-xl border px-4 py-2">
            <option value="">{{ __('admin.materials.filters.all_types') }}</option>
            <option value="pdf">{{ __('admin.materials.types.pdf') }}</option>
            <option value="ppt">{{ __('admin.materials.types.ppt') }}</option>
            <option value="video">{{ __('admin.materials.types.video') }}</option>
        </select>

        <select wire:model.live="statusFilter" class="rounded-xl border px-4 py-2">
            <option value="">{{ __('admin.materials.filters.all_status') }}</option>
            <option value="active">{{ __('admin.materials.status.active') }}</option>
            <option value="inactive">{{ __('admin.materials.status.inactive') }}</option>
        </select>
    </div>

    <div class="space-y-4">
        @forelse($topics->groupBy('course_id') as $group)
            @php($course = $group->first()?->course)
            <div class="overflow-hidden rounded-2xl border bg-white">
                <div class="flex items-center justify-between border-b bg-slate-50 p-4">
                    <div>
                        <h2 class="font-semibold">{{ $course?->title }}</h2>
                        <p class="text-xs text-slate-500">{{ __('admin.materials.course_group', ['count' => $group->count()]) }}</p>
                    </div>
                    <a href="{{ localized_route('admin.topics.index', ['courseFilter' => $course?->id]) }}" class="text-sm underline">
                        {{ __('admin.materials.actions.open_topics') }}
                    </a>
                </div>

                <div class="divide-y">
                    @foreach($group as $topic)
                        <div class="p-4">
                            <div class="flex items-center justify-between gap-3">
                                <div>
                                    <div class="font-semibold">{{ $topic->name }}</div>
                                    <div class="text-xs text-slate-500">
                                        {{ __('admin.materials.topic_meta', [
                                            'materials' => $topic->materials->count(),
                                            'visibility' => $topic->visibility,
                                            'status' => $topic->status,
                                        ]) }}
                                    </div>
                                </div>

                                <div class="flex gap-2">
                                    <button wire:click="toggleTopic('{{ $topic->id }}')" class="rounded-lg border px-3 py-1 text-sm">
                                        {{ in_array($topic->id, $openTopics) ? __('admin.materials.actions.hide') : __('admin.materials.actions.show') }}
                                    </button>

                                    @if(($this->isTopicFull[$topic->id] ?? false))
                                        <button
                                            class="flex cursor-not-allowed items-center gap-1 rounded-lg bg-slate-300 px-3 py-1 text-sm text-slate-500"
                                            title="{{ __('admin.materials.full_tooltip') }}"
                                            disabled
                                        >
                                            <span>{{ __('admin.materials.actions.add') }}</span>
                                            <span class="rounded-full bg-red-100 px-1 py-0.5 text-xs text-red-700">FULL</span>
                                        </button>
                                    @else
                                        <button wire:click="create('{{ $topic->id }}')" class="rounded-lg bg-slate-900 px-3 py-1 text-sm text-white transition-colors hover:bg-slate-800">
                                            {{ __('admin.materials.actions.add') }}
                                        </button>
                                    @endif
                                </div>
                            </div>

                            @if(in_array($topic->id, $openTopics))
                                <div class="mt-4 overflow-x-auto">
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
                                            @foreach($topic->materials as $row)
                                                <tr class="border-t hover:bg-slate-50">
                                                    <td class="p-4">
                                                        <div class="font-medium">{{ $row->name }}</div>
                                                        <div class="text-xs text-slate-500">{{ __('admin.materials.sort_order', ['count' => $row->sort_order]) }}</div>
                                                    </td>
                                                    <td class="p-4">{{ strtoupper($row->type) }}</td>
                                                    <td class="break-all p-4 text-xs text-slate-500">
                                                        {{ $row->path ?: $row->external_url }}
                                                    </td>
                                                    <td class="p-4">{{ __('admin.materials.visibility.' . $row->visibility, [], $row->visibility) }}</td>
                                                    <td class="p-4">{{ __('admin.materials.status.' . $row->status, [], $row->status) }}</td>
                                                    <td class="p-4">
                                                        <div class="flex gap-3">
                                                            <button wire:click="edit('{{ $row->id }}')" class="text-sm text-blue-600">{{ __('admin.materials.actions.edit') }}</button>
                                                            <button wire:click="delete('{{ $row->id }}')" class="text-sm text-rose-600">{{ __('admin.materials.actions.delete') }}</button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach

                                            @if($topic->materials->isEmpty())
                                                <tr>
                                                    <td colspan="6" class="p-6 text-center text-slate-500">
                                                        {{ __('admin.materials.empty_materials') }}
                                                    </td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @empty
            <div class="rounded-2xl border bg-white p-6 text-center text-slate-500">
                {{ __('admin.materials.empty') }}
            </div>
        @endforelse
    </div>

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
                    <select wire:model.live="topic_id" class="w-full rounded-xl border px-4 py-2">
                        <option value="">{{ __('admin.materials.form.select_topic') }}</option>
                        @foreach($topics as $t)
                            <option value="{{ $t->id }}">{{ $t->course?->title }} · {{ $t->name }}</option>
                        @endforeach
                    </select>

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
                            class="rounded-xl bg-slate-900 px-4 py-2 text-white">
                        {{ $uploading ? __('admin.materials.actions.uploading') : __('admin.materials.actions.save') }}
                    </button>
                </div>
            </div>
        </div>
    </template>
</div>