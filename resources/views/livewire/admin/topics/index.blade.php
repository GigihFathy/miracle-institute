<div class="space-y-6">
    <x-ui.page-header
        title="{{ __('admin.topics.page_title') }}"
        subtitle="{{ __('admin.topics.page_subtitle') }}"
    >
        <div class="flex items-center gap-2">
            @if($selectedCourse)
                <a href="{{ localized_route('admin.courses.index') }}"
                   class="rounded-xl border px-4 py-2 text-sm">
                    Kembali
                </a>
            @endif

            <button wire:click="create"
                class="admin-primary-button rounded-xl border border-brand-dark/20 px-4 py-2 text-sm transition">
                {{ __('admin.topics.actions.create') }}
            </button>
        </div>
    </x-ui.page-header>

    @if($selectedCourse)
        <div class="rounded-2xl border bg-white p-4">
            <div class="text-xs text-slate-500">Course Terpilih</div>
            <div class="mt-1 text-lg font-semibold text-slate-900">{{ $selectedCourse->title }}</div>
        </div>
    @endif

    <section class="space-y-4">
        <div class="rounded-2xl border bg-white p-4">
            <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                <input wire:model.live="search"
                    class="rounded-xl border px-4 py-2"
                    placeholder="{{ __('admin.topics.search_placeholder') }}">

                <select wire:model.live="teacherFilter"
                    class="rounded-xl border px-4 py-2">
                    <option value="">{{ __('admin.topics.filters.all_teachers') }}</option>
                    @foreach($teachers as $teacher)
                        <option value="{{ $teacher->id }}">{{ $teacher->full_name }}</option>
                    @endforeach
                </select>

                <select wire:model.live="statusFilter"
                    class="rounded-xl border px-4 py-2">
                    <option value="">{{ __('admin.topics.filters.all_status') }}</option>
                    <option value="published">{{ __('admin.topics.status.published') }}</option>
                    <option value="archived">{{ __('admin.topics.status.archived') }}</option>
                    <option value="draft">{{ __('admin.topics.status.draft') }}</option>
                </select>
            </div>
        </div>

        <div class="space-y-3 md:hidden">
            @forelse($rows as $row)
                <div class="rounded-2xl border bg-white p-4">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <div class="font-medium text-slate-900">{{ $row->name }}</div>
                            <div class="mt-1 text-xs text-slate-500">{{ $row->category }}</div>
                        </div>

                        @php
                            $displayStatus = $row->status === 'active' ? 'published' : $row->status;
                            $statusClass = match ($displayStatus) {
                                'published' => 'bg-emerald-100 text-emerald-700',
                                'archived' => 'bg-amber-100 text-amber-700',
                                'draft' => 'bg-slate-100 text-slate-700',
                                default => 'bg-slate-100 text-slate-700',
                            };
                        @endphp

                        <span class="shrink-0 rounded-full px-2 py-1 text-xs {{ $statusClass }}">
                            {{ __('admin.topics.status.' . $displayStatus, [], $displayStatus) }}
                        </span>
                    </div>

                    <div class="mt-3 space-y-1 text-xs text-slate-500">
                        <div>Pengajar: {{ $row->teacher?->full_name ?? '-' }}</div>
                        <div>Urutan: {{ $row->sort_order }}</div>
                        <div>{{ __('admin.topics.metrics.materials', ['count' => $row->materials_count]) }}</div>
                        <div>{{ __('admin.topics.metrics.sessions', ['count' => $row->video_sessions_count]) }}</div>
                        <div>{{ __('admin.topics.metrics.certificates', ['count' => $row->certificates_count]) }}</div>
                    </div>

                    <div class="mt-4 flex flex-wrap gap-2">
                        <a href="{{ localized_route('admin.materials.index', ['topicFilter' => $row->id]) }}"
                           class="admin-primary-button rounded-lg px-3 py-1.5 text-xs">
                            {{ __('admin.topics.actions.materials') }}
                        </a>

                        <a href="{{ localized_route('admin.sessions.index', ['topicFilter' => $row->id]) }}"
                           class="admin-primary-button rounded-lg px-3 py-1.5 text-xs">
                            {{ __('admin.topics.actions.sessions') }}
                        </a>

                        <button wire:click="edit('{{ $row->id }}')"
                            class="admin-edit-button inline-flex h-9 w-9 items-center justify-center rounded-lg transition"
                            title="{{ __('admin.topics.actions.edit') }}">
                            <span class="sr-only">{{ __('admin.topics.actions.edit') }}</span>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-4 w-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a2.25 2.25 0 1 1 3.182 3.182L10.582 17.13a4.5 4.5 0 0 1-1.897 1.13L6 19l.74-2.685a4.5 4.5 0 0 1 1.13-1.897L16.862 4.487ZM16.862 4.487 19.5 7.125" />
                            </svg>
                        </button>

                        <button wire:click="delete('{{ $row->id }}')"
                            class="admin-delete-button inline-flex h-9 w-9 items-center justify-center rounded-lg transition"
                            title="{{ __('admin.topics.actions.delete') }}">
                            <span class="sr-only">{{ __('admin.topics.actions.delete') }}</span>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-4 w-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673A2.25 2.25 0 0 1 15.916 21.75H8.084a2.25 2.25 0 0 1-2.245-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                            </svg>
                        </button>
                    </div>
                </div>
            @empty
                <div class="rounded-2xl border bg-white px-4 py-6 text-center text-slate-500">
                    {{ __('admin.topics.empty') }}
                </div>
            @endforelse
        </div>

        <div class="hidden md:block">
        <x-ui.table-shell class="table-auto">
            <thead class="admin-table-head text-left">
                <tr>
                    <th class="whitespace-nowrap px-4 py-3 font-medium text-slate-600">{{ __('admin.topics.table.topic') }}</th>
                    <th class="whitespace-nowrap px-4 py-3 font-medium text-slate-600">{{ __('admin.topics.table.teacher') }}</th>
                    <th class="whitespace-nowrap px-4 py-3 font-medium text-slate-600">{{ __('admin.topics.table.order') }}</th>
                    <th class="whitespace-nowrap px-4 py-3 font-medium text-slate-600">{{ __('admin.topics.table.content') }}</th>
                    <th class="whitespace-nowrap px-4 py-3 font-medium text-slate-600">{{ __('admin.topics.table.status') }}</th>
                    <th class="whitespace-nowrap px-4 py-3 font-medium text-slate-600">{{ __('admin.topics.table.action') }}</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-slate-100 bg-white">
                @forelse($rows as $row)
                    <tr class="align-top">
                        <td class="px-4 py-3">
                            <div class="font-medium text-slate-900">{{ $row->name }}</div>
                            <div class="text-xs text-slate-500">{{ $row->category }}</div>
                        </td>

                        <td class="whitespace-nowrap px-4 py-3">{{ $row->teacher?->full_name ?? '-' }}</td>
                        <td class="whitespace-nowrap px-4 py-3">{{ $row->sort_order }}</td>

                        <td class="px-4 py-3 text-xs text-slate-500">
                            {{ __('admin.topics.metrics.materials', ['count' => $row->materials_count]) }}<br>
                            {{ __('admin.topics.metrics.sessions', ['count' => $row->video_sessions_count]) }}<br>
                            {{ __('admin.topics.metrics.certificates', ['count' => $row->certificates_count]) }}
                        </td>

                        <td class="whitespace-nowrap px-4 py-3">
                            @php
                                $displayStatus = $row->status === 'active' ? 'published' : $row->status;
                                $statusClass = match ($displayStatus) {
                                    'published' => 'bg-emerald-100 text-emerald-700',
                                    'archived' => 'bg-amber-100 text-amber-700',
                                    'draft' => 'bg-slate-100 text-slate-700',
                                    default => 'bg-slate-100 text-slate-700',
                                };
                            @endphp

                            <span class="rounded-full px-2 py-1 text-xs {{ $statusClass }}">
                                {{ __('admin.topics.status.' . $displayStatus, [], $displayStatus) }}
                            </span>
                        </td>

                        <td class="px-4 py-3">
                            <div class="flex flex-wrap gap-2">
                                <a href="{{ localized_route('admin.materials.index', ['topicFilter' => $row->id]) }}"
                                   class="admin-primary-button rounded-lg px-3 py-1.5 text-xs">
                                    {{ __('admin.topics.actions.materials') }}
                                </a>

                                <a href="{{ localized_route('admin.sessions.index', ['topicFilter' => $row->id]) }}"
                                   class="admin-primary-button rounded-lg px-3 py-1.5 text-xs">
                                    {{ __('admin.topics.actions.sessions') }}
                                </a>

                                {{-- Assessments & Certificates actions removed from topic row — available on Courses page only --}}

                                <div class="my-1 w-full border-t"></div>

                                <div class="relative group">
                                    <button wire:click="edit('{{ $row->id }}')"
                                        class="admin-edit-button inline-flex h-9 w-9 items-center justify-center rounded-lg transition"
                                        title="{{ __('admin.topics.actions.edit') }}">
                                        <span class="sr-only">{{ __('admin.topics.actions.edit') }}</span>
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-4 w-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a2.25 2.25 0 1 1 3.182 3.182L10.582 17.13a4.5 4.5 0 0 1-1.897 1.13L6 19l.74-2.685a4.5 4.5 0 0 1 1.13-1.897L16.862 4.487ZM16.862 4.487 19.5 7.125" />
                                        </svg>
                                    </button>
                                    <span class="pointer-events-none absolute -top-9 left-1/2 -translate-x-1/2 whitespace-nowrap rounded-md border border-brand-dark/20 bg-white px-2 py-1 text-[11px] font-medium text-brand-dark opacity-0 transition group-hover:opacity-100 group-focus-within:opacity-100">
                                        {{ __('admin.topics.actions.edit') }}
                                    </span>
                                </div>

                                <div class="relative group">
                                    <button wire:click="delete('{{ $row->id }}')"
                                        class="admin-delete-button inline-flex h-9 w-9 items-center justify-center rounded-lg transition"
                                        title="{{ __('admin.topics.actions.delete') }}">
                                        <span class="sr-only">{{ __('admin.topics.actions.delete') }}</span>
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-4 w-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673A2.25 2.25 0 0 1 15.916 21.75H8.084a2.25 2.25 0 0 1-2.245-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                        </svg>
                                    </button>
                                    <span class="pointer-events-none absolute -top-9 left-1/2 -translate-x-1/2 whitespace-nowrap rounded-md border border-brand-dark/20 bg-white px-2 py-1 text-[11px] font-medium text-brand-dark opacity-0 transition group-hover:opacity-100 group-focus-within:opacity-100">
                                        {{ __('admin.topics.actions.delete') }}
                                    </span>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-6 text-center text-slate-500">
                            {{ __('admin.topics.empty') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </x-ui.table-shell>
        </div>

        <div>{{ $rows->links() }}</div>

        <div class="rounded-2xl border border-slate-200 bg-white px-4 py-4 text-xs leading-6 text-slate-600">
            <p class="font-semibold text-[#004777]">Keterangan status topik</p>
            <p><span class="font-semibold">Belum diterbitkan</span> berarti topik masih tahap persiapan dan belum tampil untuk murid.</p>
            <p><span class="font-semibold">Diterbitkan</span> berarti topik sudah aktif dan bisa diakses murid jika syarat aksesnya sudah terpenuhi.</p>
            <p><span class="font-semibold">Diarsipkan</span> berarti topik tidak lagi dipakai di alur belajar aktif, tetapi datanya masih disimpan.</p>
        </div>
    </section>

    @if($showModal)
        <div
            wire:click.self="$set('showModal', false)"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
        >
            <div class="flex max-h-[90vh] w-full max-w-2xl flex-col overflow-hidden rounded-2xl bg-white shadow-xl">
                <div class="flex items-center justify-between border-b p-5">
                    <h2 class="text-lg font-semibold">
                        {{ $editingId ? __('admin.topics.modal.edit_title') : __('admin.topics.modal.create_title') }}
                    </h2>

                    <button
                        wire:click="$set('showModal', false)"
                        class="text-slate-500 hover:text-black"
                    >
                        ✕
                    </button>
                </div>

                <div class="space-y-4 overflow-y-auto p-5">
                    <div class="rounded-xl bg-slate-50 px-4 py-3 text-xs text-slate-500">
                        <span class="font-semibold text-rose-500">*</span> menandakan field wajib diisi.
                    </div>

                    @if($errors->any())
                        <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                            Periksa kembali field yang wajib diisi.
                        </div>
                    @endif

                    @if($selectedCourse)
                        <input
                            value="{{ $selectedCourse->title }}"
                            disabled
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2 text-slate-600"
                        >
                    @endif

                    <div>
                        <label class="mb-1 block text-xs font-semibold text-slate-600">
                            Pengajar
                        </label>
                        <select wire:model="teacher_id" class="w-full rounded-xl border px-4 py-2">
                            <option value="">{{ __('admin.topics.form.select_teacher') }}</option>
                            @foreach($teachers as $teacher)
                                <option value="{{ $teacher->id }}">{{ $teacher->full_name }}</option>
                            @endforeach
                        </select>
                        @error('teacher_id') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <div class="mb-1 flex items-center justify-between gap-3">
                            <label class="block text-xs font-semibold text-slate-600">
                                Nama Topik <span class="text-rose-500">*</span>
                            </label>
                            <span class="text-[11px] text-slate-400">{{ mb_strlen($name ?? '') }}/70</span>
                        </div>
                        <input wire:model.live.debounce.300ms="name" maxlength="70" class="w-full rounded-xl border px-4 py-2" placeholder="{{ __('admin.topics.form.name_placeholder') }}">
                        @error('name') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-xs font-semibold text-slate-600">
                                Status <span class="text-rose-500">*</span>
                            </label>
                            <select wire:model="status" class="w-full rounded-xl border px-4 py-2">
                                <option value="published">{{ __('admin.topics.status.published') }}</option>
                                <option value="archived">{{ __('admin.topics.status.archived') }}</option>
                                <option value="draft">{{ __('admin.topics.status.draft') }}</option>
                            </select>
                            @error('status') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <details class="rounded-xl border border-sky-100 bg-sky-50 px-4 py-3 text-xs text-slate-600">
                        <summary class="cursor-pointer list-none font-semibold text-[#004777]">
                            <span class="flex items-center justify-between gap-3">
                                <span>Catatan penggunaan status</span>
                                <span class="text-[11px] font-medium text-slate-500">Lihat detail</span>
                            </span>
                        </summary>

                        <div class="mt-3 space-y-2 leading-6">
                            <p><span class="font-semibold">Belum diterbitkan</span> dipakai saat topik masih disusun dan belum siap dilihat murid.</p>
                            <p><span class="font-semibold">Diterbitkan</span> dipakai saat topik sudah siap diakses murid. Topik hanya bisa diterbitkan jika sudah punya minimal 1 sesi.</p>
                            <p><span class="font-semibold">Diarsipkan</span> dipakai saat topik lama sudah tidak ingin ditampilkan ke alur belajar aktif, tetapi datanya tetap disimpan untuk referensi.</p>
                        </div>
                    </details>

                    <div>
                        <label class="mb-1 block text-xs font-semibold text-slate-600">
                            Nomor Urut Topik
                        </label>
                        <input wire:model="sort_order" type="number" min="1" class="w-full rounded-xl border px-4 py-2" placeholder="{{ __('admin.topics.form.sort_order_placeholder') }}">
                        <p class="mt-1 text-[11px] leading-relaxed text-slate-500">
                            Menentukan posisi topik di dalam kursus. Saat membuat topik baru, sistem otomatis mengisi nomor urut berikutnya berdasarkan topik terakhir di kursus ini.
                        </p>
                        @error('sort_order') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="mb-1 block text-xs font-semibold text-slate-600">
                            Deskripsi <span class="text-rose-500">*</span>
                        </label>
                        <textarea wire:model="description" rows="4" class="w-full rounded-xl border px-4 py-2" placeholder="{{ __('admin.topics.form.description_placeholder') }}"></textarea>
                        @error('description') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="flex items-center justify-between border-t bg-slate-50 p-5">
                    <div class="flex gap-2">
                        <button
                            type="button"
                            wire:click="$set('showModal', false)"
                            class="rounded-xl border px-4 py-2"
                        >
                            {{ __('admin.topics.actions.cancel') }}
                        </button>

                        <button
                            wire:click="save"
                            class="admin-primary-button rounded-xl border border-brand-dark/20 px-4 py-2 transition"
                        >
                            {{ __('admin.topics.actions.save') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
