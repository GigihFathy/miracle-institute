<div class="space-y-6">
    <x-ui.page-header
        title="Penandatangan Sertifikat"
        subtitle="Kelola daftar penandatangan beserta rentang waktu jabatan mereka."
    >
        <a href="{{ localized_route('admin.signatories.preview') }}"
           target="_blank"
           class="rounded-xl border border-slate-200 px-4 py-2 text-sm text-slate-600 hover:bg-slate-50">
            Preview Sertifikat
        </a>
        <button wire:click="create" class="admin-primary-button rounded-xl px-4 py-2 text-sm">
            + Tambah Penandatangan
        </button>
    </x-ui.page-header>

    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
            {{ session('success') }}
        </div>
    @endif

    <div class="rounded-2xl border bg-white p-4">
        <input wire:model.live="search"
               class="w-full rounded-xl border px-4 py-2"
               placeholder="Cari nama atau jabatan...">
    </div>

    <x-ui.table-shell class="table-auto">
        <thead class="admin-table-head text-left">
            <tr>
                <th class="px-4 py-3 font-medium text-slate-600">Posisi</th>
                <th class="px-4 py-3 font-medium text-slate-600">Nama & Jabatan</th>
                <th class="px-4 py-3 font-medium text-slate-600">Aktif Dari</th>
                <th class="px-4 py-3 font-medium text-slate-600">Aktif Hingga</th>
                <th class="px-4 py-3 font-medium text-slate-600">Aksi</th>
            </tr>
        </thead>

        <tbody class="divide-y divide-slate-100 bg-white">
            @forelse($rows as $row)
                <tr class="align-middle transition-colors hover:bg-slate-50/60">
                    <td class="px-4 py-3 text-slate-600">
                        {{ $row->sort_order === 0 ? 'Kiri' : 'Kanan' }}
                    </td>

                    <td class="px-4 py-3">
                        <div class="font-medium text-slate-900">{{ $row->name }}</div>
                        <div class="text-xs text-slate-500">{{ $row->title }}</div>
                    </td>

                    <td class="whitespace-nowrap px-4 py-3 text-sm text-slate-700">
                        {{ $row->active_from->format('d M Y') }}
                    </td>

                    <td class="whitespace-nowrap px-4 py-3 text-sm text-slate-700">
                        @if($row->active_until)
                            {{ $row->active_until->format('d M Y') }}
                        @else
                            <span class="rounded-full bg-emerald-100 px-2 py-0.5 text-xs text-emerald-700">Aktif</span>
                        @endif
                    </td>

                    <td class="px-4 py-3">
                        <div class="flex flex-wrap gap-2">
                            <button wire:click="edit('{{ $row->id }}')"
                                    class="rounded-lg border px-3 py-1.5 text-xs text-slate-600 hover:bg-slate-50">
                                Edit
                            </button>
                            <button wire:click="delete('{{ $row->id }}')"
                                    wire:confirm="Hapus penandatangan ini?"
                                    class="rounded-lg border border-red-200 px-3 py-1.5 text-xs text-red-600 hover:bg-red-50">
                                Hapus
                            </button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-4 py-8 text-center text-slate-500">
                        Belum ada penandatangan. Klik "+ Tambah Penandatangan" untuk memulai.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </x-ui.table-shell>

    <div>{{ $rows->links() }}</div>

    {{-- Modal Create/Edit --}}
    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/60 p-4">
            <button type="button" class="absolute inset-0" wire:click="$set('showModal', false)" aria-label="Tutup modal"></button>

            <div class="relative z-10 w-full max-w-lg rounded-2xl bg-white p-6 shadow-2xl">
                <h3 class="mb-4 text-lg font-semibold text-slate-900">
                    {{ $editingId ? 'Edit Penandatangan' : 'Tambah Penandatangan' }}
                </h3>

                <div class="space-y-4">
                    <div>
                        <label class="mb-1 block text-xs font-semibold text-slate-600">
                            Nama <span class="text-rose-500">*</span>
                        </label>
                        <input wire:model="name"
                               class="w-full rounded-xl border px-4 py-2"
                               placeholder="Nama lengkap">
                        @error('name') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="mb-1 block text-xs font-semibold text-slate-600">
                            Jabatan <span class="text-rose-500">*</span>
                        </label>
                        <input wire:model="title"
                               class="w-full rounded-xl border px-4 py-2"
                               placeholder="cth. Rektor Miracle Institute">
                        @error('title') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="mb-1 block text-xs font-semibold text-slate-600">
                                Aktif Dari <span class="text-rose-500">*</span>
                            </label>
                            <input wire:model="active_from"
                                   type="date"
                                   class="w-full rounded-xl border px-4 py-2">
                            @error('active_from') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="mb-1 block text-xs font-semibold text-slate-600">
                                Aktif Hingga
                                <span class="text-slate-400 font-normal">(kosong = masih menjabat)</span>
                            </label>
                            <input wire:model="active_until"
                                   type="date"
                                   class="w-full rounded-xl border px-4 py-2">
                            @error('active_until') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="mb-1 block text-xs font-semibold text-slate-600">
                            Posisi di Sertifikat <span class="text-rose-500">*</span>
                        </label>
                        <select wire:model="sort_order" class="w-full rounded-xl border px-4 py-2">
                            <option value="0">Kiri</option>
                            <option value="1">Kanan</option>
                        </select>
                        @error('sort_order') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="mb-1 block text-xs font-semibold text-slate-600">
                            Gambar Tanda Tangan
                        </label>

                        @if($currentSignatureImage && !$signatureFile)
                            <div class="mb-2">
                                <img src="{{ asset('storage/' . $currentSignatureImage) }}"
                                     alt="Tanda tangan saat ini"
                                     class="h-14 w-auto object-contain rounded border p-1">
                                <p class="text-xs text-slate-400 mt-1">Tanda tangan saat ini. Upload baru untuk mengganti.</p>
                            </div>
                        @endif

                        @if($signatureFile)
                            <div class="mb-2">
                                <img src="{{ $signatureFile->temporaryUrl() }}"
                                     alt="Preview"
                                     class="h-14 w-auto object-contain rounded border p-1">
                            </div>
                        @endif

                        <input wire:model="signatureFile"
                               type="file"
                               accept="image/*"
                               class="w-full rounded-xl border px-4 py-2 text-sm">
                        @error('signatureFile') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                    <button type="button"
                            wire:click="$set('showModal', false)"
                            class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-50">
                        Batal
                    </button>
                    <button type="button"
                            wire:click="save"
                            class="admin-primary-button rounded-xl px-4 py-2.5 text-sm font-medium">
                        Simpan
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
