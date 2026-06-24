<div class="space-y-6">
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
                                Kembali
                        </a>
                        @if(($this->isTopicFull[$selectedTopic->id] ?? false))
                            <button class="rounded-xl bg-slate-300 px-4 py-2 text-sm text-slate-500" disabled>
                                {{ __('admin.materials.actions.add') }}
                                <span class="ml-1 rounded-full bg-red-100 px-1.5 py-0.5 text-[10px] font-semibold text-red-700">MAX 5</span>
                            </button>
                        @else
                            <button wire:click="create('{{ $selectedTopic->id }}')" class="admin-primary-button rounded-xl border border-brand-dark/20 px-4 py-2 text-sm transition">
                                {{ __('admin.materials.actions.add') }}
                            </button>
                        @endif
                    </div>
                </div>
            </div>

            <x-ui.table-shell class="table-auto">
                <thead class="admin-table-head text-left">
                    <tr>
                        <th class="px-4 py-3 font-medium">{{ __('admin.materials.table.name') }}</th>
                        <th class="px-4 py-3 font-medium">{{ __('admin.materials.table.type') }}</th>
                        <th class="px-4 py-3 font-medium">{{ __('admin.materials.table.source') }}</th>
                        <th class="px-4 py-3 font-medium">{{ __('admin.materials.table.visibility') }}</th>
                        <th class="px-4 py-3 font-medium">{{ __('admin.materials.table.status') }}</th>
                        <th class="px-4 py-3 font-medium">{{ __('admin.materials.table.action') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($selectedTopic->materials as $row)
                        <tr class="align-top hover:bg-slate-50/60">
                            <td class="px-4 py-3">
                                <div class="font-medium text-slate-900">{{ $row->name }}</div>
                                <div class="text-xs text-slate-500">{{ __('admin.materials.sort_order', ['count' => $row->sort_order]) }}</div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">{{ strtoupper($row->type) }}</td>
                            <td class="px-4 py-3 break-all text-xs text-slate-500">{{ $row->path ?: $row->external_url }}</td>
                            <td class="px-4 py-3 whitespace-nowrap">{{ __('admin.materials.visibility.' . $row->visibility, [], $row->visibility) }}</td>
                            <td class="px-4 py-3 whitespace-nowrap">{{ __('admin.materials.status.' . $row->status, [], $row->status) }}</td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap gap-2">
                                    <button wire:click="edit('{{ $row->id }}')" class="admin-edit-button rounded-lg px-3 py-1.5 text-xs">
                                        {{ __('admin.materials.actions.edit') }}
                                    </button>
                                    <button wire:click="delete('{{ $row->id }}')" wire:confirm="Delete this material?" class="admin-delete-button rounded-lg px-3 py-1.5 text-xs">
                                        {{ __('admin.materials.actions.delete') }}
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-6 text-center text-slate-500">{{ __('admin.materials.empty_materials') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </x-ui.table-shell>
        </div>
    @endif

    @if($showModal)
        <div
            wire:click.self="$set('showModal', false)"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">

            <div class="w-full max-w-lg space-y-4 rounded-2xl bg-white p-6 shadow-xl">

                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold">{{ $editingId ? __('admin.materials.modal.edit_title') : __('admin.materials.modal.create_title') }}</h2>
                    <button type="button" wire:click="$set('showModal', false)" class="text-slate-500">✕</button>
                </div>

                <div class="space-y-3">
                    <div class="rounded-xl bg-slate-50 px-4 py-3 text-xs text-slate-500">
                        <span class="font-semibold text-rose-500">*</span> menandakan field wajib diisi.
                    </div>

                    @if($errors->any())
                        <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                            Periksa kembali field yang wajib diisi.
                        </div>
                    @endif

                    @if($selectedTopic)
                        <input
                            value="{{ $selectedTopic->course?->title }} · {{ $selectedTopic->name }}"
                            disabled
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2 text-slate-600"
                        >
                    @else
                        <label class="mb-1 block text-xs font-semibold text-slate-600">Sesi <span class="text-rose-500">*</span></label>
                        <select wire:model.live="topic_id" class="w-full rounded-xl border px-4 py-2">
                            <option value="">{{ __('admin.materials.form.select_topic') }}</option>
                            @foreach($topics as $t)
                                <option value="{{ $t->id }}">{{ $t->course?->title }} · {{ $t->name }}</option>
                            @endforeach
                        </select>
                        @error('topic_id') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    @endif

                    <div class="mb-1 flex items-center justify-between gap-3">
                        <label class="block text-xs font-semibold text-slate-600">Nama Material <span class="text-rose-500">*</span></label>
                        <span class="text-[11px] text-slate-400">{{ mb_strlen($name ?? '') }}/50</span>
                    </div>
                    <input wire:model.live="name" maxlength="50" class="w-full rounded-xl border px-4 py-2" placeholder="{{ __('admin.materials.form.name_placeholder') }}">
                    @error('name') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror

                    <label class="mb-1 block text-xs font-semibold text-slate-600">Tipe Material <span class="text-rose-500">*</span></label>
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
                    @error('type') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror

                    @if($type === 'video')
                        <label class="mb-1 block text-xs font-semibold text-slate-600">URL Video <span class="text-rose-500">*</span></label>
                        <input wire:model.live="external_url"
                            class="w-full rounded-xl border px-4 py-2"
                            placeholder="{{ __('admin.materials.form.external_url_placeholder') }}">
                        <p class="mt-1 text-[11px] text-slate-500">Wajib diisi jika tipe material adalah video.</p>
                        @error('external_url') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    @elseif(in_array($type, ['pdf', 'ppt'], true))
                        <input type="file" wire:model="materialFile" accept=".pdf,.ppt,.pptx,application/pdf,application/vnd.ms-powerpoint,application/vnd.openxmlformats-officedocument.presentationml.presentation" class="w-full rounded-xl border px-4 py-2">
                        <p class="mt-1 text-[11px] text-slate-500">Format: PDF, PPT, PPTX · Maks. 10 MB</p>
                        @error('materialFile') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    @endif

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                        <label class="mb-1 block text-xs font-semibold text-slate-600">Visibilitas <span class="text-rose-500">*</span></label>
                            <select wire:model.live="visibility" class="w-full rounded-xl border px-4 py-2">
                            <option value="public">{{ __('admin.materials.visibility.public') }}</option>
                            <option value="private">{{ __('admin.materials.visibility.private') }}</option>
                            </select>
                            @error('visibility') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="mb-1 block text-xs font-semibold text-slate-600">Status <span class="text-rose-500">*</span></label>
                            <select wire:model.live="status" class="w-full rounded-xl border px-4 py-2">
                            <option value="active">{{ __('admin.materials.status.active') }}</option>
                            <option value="inactive">{{ __('admin.materials.status.inactive') }}</option>
                            </select>
                            @error('status') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="mb-1 block text-xs font-semibold text-slate-600">
                            Nomor Urut Material
                        </label>
                        <input wire:model.live="sort_order" type="number" min="1"
                            class="w-full rounded-xl border px-4 py-2" placeholder="{{ __('admin.materials.form.sort_order_placeholder') }}">
                        <p class="mt-1 text-[11px] leading-relaxed text-slate-500">
                            Menentukan posisi material di dalam sesi. Saat membuat material baru, sistem otomatis mengisi nomor urut berikutnya dari material terakhir pada sesi yang dipilih.
                        </p>
                        @error('sort_order') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>
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

                <div class="flex justify-end gap-2 pt-3">
                    <button type="button" wire:click="$set('showModal', false)" class="rounded-xl border px-4 py-2">
                        {{ __('admin.materials.actions.cancel') }}
                    </button>
                    <button wire:click="save"
                            wire:loading.attr="disabled"
                            wire:target="save"
                            class="admin-primary-button rounded-xl border border-brand-dark/20 px-4 py-2 transition">
                        <span wire:loading.remove wire:target="save">
                            {{ __('admin.materials.actions.save') }}
                        </span>
                        <span wire:loading wire:target="save">
                            {{ __('admin.materials.actions.uploading') }}
                        </span>
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
