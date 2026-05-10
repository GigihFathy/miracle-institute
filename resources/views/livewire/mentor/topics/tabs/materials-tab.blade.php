<section class="grid grid-cols-1 gap-6 xl:grid-cols-[1.2fr_0.8fr]">
    <div class="rounded-2xl border bg-white p-5">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h2 class="text-lg font-semibold">Selected Material</h2>
                <p class="text-sm text-slate-500">Preview dan detail data material.</p>
            </div>

            <div class="flex gap-2">
                @if($selectedMaterial)
                    <button type="button"
                            wire:click="editMaterial('{{ $selectedMaterial->id }}')"
                            class="rounded-xl border px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">
                        Edit
                    </button>

                    <button type="button"
                            wire:click="deleteMaterial('{{ $selectedMaterial->id }}')"
                            class="rounded-xl border px-4 py-2 text-sm text-rose-600 hover:bg-rose-50">
                        Delete
                    </button>
                @endif
            </div>
        </div>

        <div class="mt-4 space-y-4">
            @if($selectedMaterial)
                <div class="grid grid-cols-2 gap-3 text-sm">
                    <div class="rounded-xl border p-4">
                        <div class="text-xs text-slate-500">Name</div>
                        <div class="mt-1 font-semibold text-slate-900">{{ $selectedMaterial->name }}</div>
                    </div>
                    <div class="rounded-xl border p-4">
                        <div class="text-xs text-slate-500">Type</div>
                        <div class="mt-1 font-semibold text-slate-900">{{ strtoupper($selectedMaterial->type) }}</div>
                    </div>
                    <div class="rounded-xl border p-4">
                        <div class="text-xs text-slate-500">Status</div>
                        <div class="mt-1 font-semibold text-slate-900">{{ ucfirst($selectedMaterial->status) }}</div>
                    </div>
                    <div class="rounded-xl border p-4">
                        <div class="text-xs text-slate-500">Sort Order</div>
                        <div class="mt-1 font-semibold text-slate-900">#{{ $selectedMaterial->sort_order }}</div>
                    </div>
                </div>

                @if($selectedMaterial->type === 'video' && $materialPreviewUrl)
                    <div class="aspect-video overflow-hidden rounded-2xl bg-slate-100">
                        <iframe src="{{ $materialPreviewUrl }}" class="h-full w-full" allowfullscreen></iframe>
                    </div>
                @elseif($materialPreviewUrl)
                    <div class="rounded-2xl border bg-slate-50 p-5">
                        <a href="{{ $materialPreviewUrl }}" target="_blank" class="font-medium text-slate-900 underline">
                            Open / download material
                        </a>
                    </div>
                @else
                    <div class="rounded-xl border border-dashed p-6 text-sm text-slate-500">
                        Material ini belum memiliki preview.
                    </div>
                @endif
            @else
                <div class="rounded-xl border border-dashed p-6 text-sm text-slate-500">
                    Pilih material dari daftar kanan.
                </div>
            @endif
        </div>
    </div>

    <aside class="rounded-2xl border bg-white p-5">
        <div class="flex items-center justify-between gap-3">
            <h2 class="text-lg font-semibold">Materials List</h2>

            @if($canAddMaterial)
                <button type="button"
                        wire:click="openMaterialModal"
                        class="rounded-xl bg-slate-900 px-4 py-2 text-sm text-white hover:bg-slate-700">
                    Add Material
                </button>
            @else
                <span class="rounded-full border px-3 py-1 text-xs text-slate-500">
                    Limit reached
                </span>
            @endif
        </div>

        <div class="mt-4 space-y-2">
            @forelse($materials as $material)
                <button type="button"
                        wire:click="selectMaterial('{{ $material->id }}')"
                        class="w-full rounded-xl border p-4 text-left transition
                               {{ $selectedMaterial?->id === $material->id ? 'border-slate-900 bg-slate-900 text-white shadow-sm' : 'hover:border-slate-400' }}">
                    <div class="flex items-center justify-between gap-3">
                        <div class="min-w-0">
                            <div class="truncate text-sm font-medium">
                                #{{ $material->sort_order }} · {{ $material->name }}
                            </div>
                            <div class="mt-1 text-xs {{ $selectedMaterial?->id === $material->id ? 'text-slate-300' : 'text-slate-500' }}">
                                {{ strtoupper($material->type) }} · {{ ucfirst($material->status) }}
                            </div>
                        </div>

                        <span class="rounded-full border px-2 py-1 text-[11px] uppercase text-slate-600">
                            Edit
                        </span>
                    </div>
                </button>
            @empty
                <div class="rounded-xl border border-dashed p-5 text-sm text-slate-500">
                    Belum ada material.
                </div>
            @endforelse
        </div>
    </aside>

    <x-ui.mentor.modal
        :show="$showMaterialModal"
        title="{{ $editingMaterialId ? 'Edit Material' : 'Add Material' }}"
        subtitle="Gunakan external path untuk Google Drive."
        wire:click="closeMaterialModal"
    >
        <form wire:submit.prevent="saveMaterial" class="space-y-4">
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label class="text-xs font-medium text-slate-500">Material Name</label>
                    <input wire:model.defer="materialName" class="mt-1 w-full rounded-xl border px-4 py-2" placeholder="Material name">
                    @error('materialName') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div class="sm:col-span-2">
                    <label class="text-xs font-medium text-slate-500">External URL</label>
                    <input wire:model.defer="materialExternalUrl" class="mt-1 w-full rounded-xl border px-4 py-2" placeholder="Google Drive / access link">
                    @error('materialExternalUrl') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="text-xs font-medium text-slate-500">Type</label>
                    <select wire:model.defer="materialType" class="mt-1 w-full rounded-xl border px-4 py-2">
                        <option value="">Select</option>
                        @foreach($materialTypeOptions as $type)
                            <option value="{{ $type }}">{{ strtoupper($type) }}</option>
                        @endforeach
                    </select>
                    @error('materialType') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                    @if(empty($materialTypeOptions))
                        <p class="mt-1 text-xs text-slate-500">Semua tipe sudah dipakai pada topic ini.</p>
                    @endif
                </div>

                <div>
                    <label class="text-xs font-medium text-slate-500">Status</label>
                    <select wire:model.defer="materialStatus" class="mt-1 w-full rounded-xl border px-4 py-2">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                    @error('materialStatus') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div class="sm:col-span-2">
                    <label class="text-xs font-medium text-slate-500">Sort Order</label>
                    <input wire:model.defer="materialSortOrder" type="number" min="1" class="mt-1 w-full rounded-xl border px-4 py-2">
                    @error('materialSortOrder') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 border-t pt-4">
                <button type="button" wire:click="closeMaterialModal" class="rounded-xl border px-4 py-2 text-sm text-slate-600">
                    Cancel
                </button>
                <button type="submit" class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-700">
                    {{ $editingMaterialId ? 'Update Material' : 'Save Material' }}
                </button>
            </div>
        </form>
    </x-ui.mentor.modal>
</section>