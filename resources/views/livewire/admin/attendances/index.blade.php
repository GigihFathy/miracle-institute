<div class="space-y-6">
    <x-ui.page-header
        title="{{ __('admin.attendances.page_title') }}"
        subtitle="{{ __('admin.attendances.page_subtitle') }}"
    >
        <div>
            <button wire:click="create"
                class="admin-primary-button rounded-xl border border-brand-dark/20 px-4 py-2 text-sm transition">
                {{ __('admin.attendances.actions.create') }}
            </button>
        </div>
    </x-ui.page-header>

    {{-- scope banner removed per UI simplification request --}}

    <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
        @foreach([
            ['label' => __('admin.attendances.stats.total'), 'value' => $stats['total']],
            ['label' => __('admin.attendances.stats.present'), 'value' => $stats['present']],
            ['label' => __('admin.attendances.stats.late'), 'value' => $stats['late']],
            ['label' => __('admin.attendances.stats.absent'), 'value' => $stats['absent']],
        ] as $card)
            <div class="rounded-2xl border bg-white p-5">
                <div class="text-xs text-slate-500">{{ $card['label'] }}</div>
                <div class="mt-1 text-2xl font-bold">
                    {{ number_format($card['value']) }}
                </div>
            </div>
        @endforeach
    </div>

    <div class="rounded-2xl border bg-white p-4">
        <div class="grid grid-cols-1 gap-3 md:grid-cols-5">
            <input wire:model.live="search"
                class="rounded-xl border px-4 py-2"
                placeholder="{{ __('admin.attendances.search_placeholder') }}">

            <select wire:model.live="topicFilter" class="rounded-xl border px-4 py-2">
                <option value="">{{ __('admin.attendances.filters.all_topics') }}</option>
                @foreach($topics as $topic)
                    <option value="{{ $topic->id }}">
                        {{ $topic->course?->title }} Â· {{ $topic->name }}
                    </option>
                @endforeach
            </select>

            <select wire:model.live="sessionFilter" class="rounded-xl border px-4 py-2">
                <option value="">{{ __('admin.attendances.filters.all_sessions') }}</option>
                @foreach($sessions as $session)
                    <option value="{{ $session->id }}">
                        {{ $session->topic?->name }} Â· {{ $session->title }}
                    </option>
                @endforeach
            </select>

            <select wire:model.live="statusFilter" class="rounded-xl border px-4 py-2">
                <option value="">{{ __('admin.attendances.filters.all_status') }}</option>
                <option value="present">{{ __('admin.attendances.status.present') }}</option>
                <option value="late">{{ __('admin.attendances.status.late') }}</option>
                <option value="online">{{ __('admin.attendances.status.absent') }}</option>
            </select>

            <select wire:model.live="perPage" class="rounded-xl border px-4 py-2">
                <option value="10">{{ trans_choice('admin.attendances.per_page', 10) }}</option>
                <option value="25">{{ trans_choice('admin.attendances.per_page', 25) }}</option>
                <option value="50">{{ trans_choice('admin.attendances.per_page', 50) }}</option>
            </select>
        </div>
    </div>

    <x-ui.table-shell class="table-auto">
        <thead class="admin-table-head text-left">
            <tr>
                <th class="whitespace-nowrap px-4 py-3 font-medium text-slate-600">{{ __('admin.attendances.table.session') }}</th>
                <th class="whitespace-nowrap px-4 py-3 font-medium text-slate-600">{{ __('admin.attendances.table.topic') }}</th>
                <th class="whitespace-nowrap px-4 py-3 font-medium text-slate-600">{{ __('admin.attendances.table.user') }}</th>
                <th class="whitespace-nowrap px-4 py-3 font-medium text-slate-600">{{ __('admin.attendances.table.status') }}</th>
                <th class="whitespace-nowrap px-4 py-3 font-medium text-slate-600">{{ __('admin.attendances.table.check_in') }}</th>
                <th class="whitespace-nowrap px-4 py-3 font-medium text-slate-600">{{ __('admin.attendances.table.action') }}</th>
            </tr>
        </thead>

        <tbody class="divide-y divide-slate-100 bg-white">
            @forelse($rows as $row)
                <tr class="align-top">
                    <td class="px-4 py-3">
                        <div class="font-medium text-slate-900">{{ $row->videoSession?->title }}</div>
                        <div class="text-xs text-slate-500">
                            {{ $row->videoSession?->start_at?->format('d M Y H:i') }}
                        </div>
                    </td>

                    <td class="px-4 py-3 text-sm text-slate-700">
                        <div>{{ $row->videoSession?->topic?->name }}</div>
                        <div class="text-xs text-slate-500">{{ $row->videoSession?->topic?->course?->title }}</div>
                    </td>

                    <td class="px-4 py-3">
                        <div class="font-medium text-slate-900">{{ $row->user?->full_name }}</div>
                        <div class="text-xs text-slate-500">{{ $row->user?->email }}</div>
                    </td>

                    <td class="whitespace-nowrap px-4 py-3">
                        <span class="rounded-full bg-slate-100 px-2 py-1 text-xs">
                            {{ __('admin.attendances.status.' . $row->status, [], $row->status) }}
                        </span>
                    </td>

                    <td class="whitespace-nowrap px-4 py-3 text-sm">
                        {{ $row->check_in_at?->format('d M Y H:i') ?? '-' }}
                    </td>

                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2 whitespace-nowrap">
                            <button wire:click="edit('{{ $row->id }}')"
                                class="admin-edit-button rounded-lg px-3 py-1.5 text-xs">
                                {{ __('admin.attendances.actions.edit') }}
                            </button>

                            <button wire:click="delete('{{ $row->id }}')"
                                class="admin-delete-button rounded-lg px-3 py-1.5 text-xs">
                                {{ __('admin.attendances.actions.delete') }}
                            </button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-4 py-6 text-center text-slate-500">
                        {{ __('admin.attendances.empty') }}
                    </td>
                </tr>
            @endforelse
        </tbody>
    </x-ui.table-shell>

    <div>{{ $rows->links() }}</div>

    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4"
            wire:click.self="$set('showModal', false)">

            <div class="max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-2xl bg-white p-6 space-y-4">

                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold">
                        {{ $editingId ? __('admin.attendances.modal.edit_title') : __('admin.attendances.modal.create_title') }}
                    </h2>
                    <button type="button" wire:click="$set('showModal', false)">Tutup</button>
                </div>

                <div class="rounded-xl bg-slate-50 px-4 py-3 text-xs text-slate-500">
                    <span class="font-semibold text-rose-500">*</span> menandakan field wajib diisi.
                </div>

                @if($errors->any())
                    <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                        Periksa kembali field yang wajib diisi.
                    </div>
                @endif

                <label class="mb-1 block text-xs font-semibold text-slate-600">Sesi <span class="text-rose-500">*</span></label>
                <select wire:model="video_session_id" class="w-full rounded-xl border px-4 py-2">
                    <option value="">{{ __('admin.attendances.form.select_session') }}</option>
                    @foreach($sessions as $session)
                        <option value="{{ $session->id }}">
                            {{ $session->topic?->name }} Â· {{ $session->title }}
                        </option>
                    @endforeach
                </select>
                @error('video_session_id') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror

                <label class="mb-1 block text-xs font-semibold text-slate-600">Pengguna <span class="text-rose-500">*</span></label>
                <select wire:model="user_id" class="w-full rounded-xl border px-4 py-2">
                    <option value="">{{ __('admin.attendances.form.select_user') }}</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}">
                            {{ $user->full_name }} Â· {{ $user->email }}
                        </option>
                    @endforeach
                </select>
                @error('user_id') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror

                <label class="mb-1 block text-xs font-semibold text-slate-600">Status <span class="text-rose-500">*</span></label>
                <select wire:model="status" class="w-full rounded-xl border px-4 py-2">
                    <option value="present">{{ __('admin.attendances.status.present') }}</option>
                    <option value="late">{{ __('admin.attendances.status.late') }}</option>
                    <option value="online">{{ __('admin.attendances.status.absent') }}</option>
                </select>
                @error('status') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror

                <input wire:model="check_in_at" type="datetime-local"
                    class="w-full rounded-xl border px-4 py-2">
                @error('check_in_at') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror

                <input wire:model="ip_address"
                    class="w-full rounded-xl border px-4 py-2"
                    placeholder="{{ __('admin.attendances.form.ip_address_placeholder') }}">
                @error('ip_address') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror

                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" wire:click="$set('showModal', false)"
                        class="rounded-xl border px-4 py-2">
                        {{ __('admin.attendances.actions.cancel') }}
                    </button>

                    <button wire:click="save"
                        class="admin-primary-button rounded-xl border border-brand-dark/20 px-4 py-2 transition">
                        {{ __('admin.attendances.actions.save') }}
                    </button>
                </div>

            </div>
        </div>
    @endif
</div>
