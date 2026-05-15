<div x-data="{ open: @entangle('showModal').live }" class="mx-auto max-w-6xl space-y-6 px-4">
    <x-ui.page-header
        title="{{ __('admin.certificates.page_title') }}"
        subtitle="{{ __('admin.certificates.page_subtitle') }}"
    >
        <button wire:click="create"
            class="rounded-xl bg-slate-900 px-4 py-2 text-sm text-white">
            {{ __('admin.certificates.actions.create') }}
        </button>
    </x-ui.page-header>

    <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
        <div class="rounded-2xl border bg-white p-5">
            <div class="text-xs text-slate-500">{{ __('admin.certificates.stats.total') }}</div>
            <div class="mt-1 text-3xl font-bold">{{ number_format($stats['total']) }}</div>
        </div>

        <div class="rounded-2xl border bg-emerald-50/30 p-5">
            <div class="text-xs text-slate-500">{{ __('admin.certificates.stats.issued') }}</div>
            <div class="mt-1 text-3xl font-bold text-emerald-600">
                {{ number_format($stats['issued']) }}
            </div>
        </div>

        <div class="rounded-2xl border bg-slate-50 p-5">
            <div class="text-xs text-slate-500">{{ __('admin.certificates.stats.draft') }}</div>
            <div class="mt-1 text-3xl font-bold">
                {{ number_format($stats['draft']) }}
            </div>
        </div>

        <div class="rounded-2xl border bg-rose-50/30 p-5">
            <div class="text-xs text-slate-500">{{ __('admin.certificates.stats.expired') }}</div>
            <div class="mt-1 text-3xl font-bold text-rose-600">
                {{ number_format($stats['expired']) }}
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-3 md:grid-cols-5">
        <input wire:model.live="search"
            class="w-full rounded-xl border px-4 py-2"
            placeholder="{{ __('admin.certificates.search_placeholder') }}">

        <select wire:model.live="courseFilter" class="rounded-xl border px-4 py-2">
            <option value="">{{ __('admin.certificates.filters.all_courses') }}</option>
            @foreach($courses as $course)
                <option value="{{ $course->id }}">{{ $course->title }}</option>
            @endforeach
        </select>

        <select wire:model.live="topicFilter" class="rounded-xl border px-4 py-2">
            <option value="">{{ __('admin.certificates.filters.all_topics') }}</option>
            @foreach($topics as $topic)
                <option value="{{ $topic->id }}">
                    {{ $topic->course?->title }} · {{ $topic->name }}
                </option>
            @endforeach
        </select>

        <select wire:model.live="typeFilter" class="rounded-xl border px-4 py-2">
            <option value="">{{ __('admin.certificates.filters.all_types') }}</option>
            <option value="course">{{ __('admin.certificates.types.course') }}</option>
            <option value="topic">{{ __('admin.certificates.types.topic') }}</option>
        </select>

        <select wire:model.live="statusFilter" class="rounded-xl border px-4 py-2">
            <option value="">{{ __('admin.certificates.filters.all_status') }}</option>
            <option value="issued">{{ __('admin.certificates.status.issued') }}</option>
            <option value="draft">{{ __('admin.certificates.status.draft') }}</option>
            <option value="expired">{{ __('admin.certificates.status.expired') }}</option>
        </select>
    </div>

    <x-ui.table-shell class="table-auto">
        <thead class="bg-slate-50 text-left">
            <tr>
                <th class="whitespace-nowrap px-4 py-3 font-medium text-slate-600">{{ __('admin.certificates.table.certificate') }}</th>
                <th class="whitespace-nowrap px-4 py-3 font-medium text-slate-600">{{ __('admin.certificates.table.course_topic') }}</th>
                <th class="whitespace-nowrap px-4 py-3 font-medium text-slate-600">{{ __('admin.certificates.table.type') }}</th>
                <th class="whitespace-nowrap px-4 py-3 font-medium text-slate-600">{{ __('admin.certificates.table.issued') }}</th>
                <th class="whitespace-nowrap px-4 py-3 font-medium text-slate-600">{{ __('admin.certificates.table.status') }}</th>
                <th class="whitespace-nowrap px-4 py-3 font-medium text-slate-600">{{ __('admin.certificates.table.action') }}</th>
            </tr>
        </thead>

        <tbody class="divide-y divide-slate-100 bg-white">
            @forelse($rows as $row)
                <tr class="align-top">
                    <td class="px-4 py-3">
                        <div class="font-medium text-slate-900">{{ $row->certificate_number }}</div>
                        <div class="max-w-[200px] truncate text-xs text-slate-500">
                            {{ $row->file_path }}
                        </div>
                        <span class="text-xs text-slate-500">
                            {{ $row->user?->full_name }}
                        </span>
                    </td>

                    <td class="px-4 py-3">
                        <div class="font-medium text-slate-900">{{ $row->course?->title ?? '-' }}</div>
                        <div class="text-xs text-slate-500">{{ $row->topic?->name ?? '-' }}</div>
                    </td>

                    <td class="whitespace-nowrap px-4 py-3">{{ __('admin.certificates.types.' . $row->type, [], ucfirst($row->type)) }}</td>
                    <td class="whitespace-nowrap px-4 py-3">{{ $row->issued_at?->format('d M Y H:i') ?? '-' }}</td>

                    <td class="whitespace-nowrap px-4 py-3">
                        <span class="rounded-full bg-slate-100 px-2 py-1 text-xs">
                            {{ __('admin.certificates.status.' . $row->status, [], $row->status) }}
                        </span>
                    </td>

                    <td class="px-4 py-3">
                        <div class="flex flex-wrap gap-2">
                            @if($row->file_path)
                                <a href="{{ localized_route('certificates.download', $row->id) }}"
                                    class="rounded-lg bg-slate-100 px-3 py-1.5 text-xs hover:bg-slate-200">
                                    {{ __('admin.certificates.actions.download') }}
                                </a>
                            @endif

                            <div class="my-1 w-full border-t"></div>

                            <button wire:click="edit('{{ $row->id }}')"
                                class="rounded-lg bg-blue-100 px-3 py-1.5 text-xs text-blue-700 hover:bg-blue-200">
                                {{ __('admin.certificates.actions.edit') }}
                            </button>

                            <button wire:click="delete('{{ $row->id }}')"
                                class="rounded-lg bg-red-100 px-3 py-1.5 text-xs text-red-700 hover:bg-red-200">
                                {{ __('admin.certificates.actions.delete') }}
                            </button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-4 py-6 text-center text-slate-500">
                        {{ __('admin.certificates.empty') }}
                    </td>
                </tr>
            @endforelse
        </tbody>
    </x-ui.table-shell>

    <template x-teleport="body">
        <div x-show="open"
             x-cloak
             x-transition
             @click.self="open = false; $wire.set('showModal', false)"
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4">

            <div class="flex max-h-[90vh] w-full max-w-2xl flex-col rounded-2xl bg-white shadow-xl">

                <div class="flex items-center justify-between border-b p-6">
                    <h2 class="text-lg font-semibold">
                        {{ $editingId ? __('admin.certificates.modal.edit_title') : __('admin.certificates.modal.create_title') }}
                    </h2>

                    <button @click="open = false; $wire.set('showModal', false)"
                        class="text-xl text-slate-500">
                        ✕
                    </button>
                </div>

                <div class="space-y-4 overflow-y-auto p-6">
                    <input wire:model="certificate_number"
                        class="w-full rounded-xl border px-4 py-2"
                        placeholder="{{ __('admin.certificates.form.certificate_number_placeholder') }}">

                    <select wire:model="user_id"
                        class="w-full rounded-xl border px-4 py-2">
                        <option value="">{{ __('admin.certificates.form.select_user') }}</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">
                                {{ $user->full_name }} · {{ $user->email }}
                            </option>
                        @endforeach
                    </select>

                    <select wire:model="course_id"
                        class="w-full rounded-xl border px-4 py-2">
                        <option value="">{{ __('admin.certificates.form.select_course') }}</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}">{{ $course->title }}</option>
                        @endforeach
                    </select>

                    <select wire:model="topic_id"
                        class="w-full rounded-xl border px-4 py-2">
                        <option value="">{{ __('admin.certificates.form.select_topic') }}</option>
                        @foreach($topics as $topic)
                            <option value="{{ $topic->id }}">
                                {{ $topic->course?->title }} · {{ $topic->name }}
                            </option>
                        @endforeach
                    </select>

                    <select wire:model="type"
                        class="w-full rounded-xl border px-4 py-2">
                        <option value="course">{{ __('admin.certificates.types.course') }}</option>
                        <option value="topic">{{ __('admin.certificates.types.topic') }}</option>
                    </select>

                    <input wire:model="file_path"
                        class="w-full rounded-xl border px-4 py-2"
                        placeholder="{{ __('admin.certificates.form.file_path_placeholder') }}">

                    <input wire:model="issued_at"
                        type="datetime-local"
                        class="w-full rounded-xl border px-4 py-2">

                    <select wire:model="status"
                        class="w-full rounded-xl border px-4 py-2">
                        <option value="issued">{{ __('admin.certificates.status.issued') }}</option>
                        <option value="draft">{{ __('admin.certificates.status.draft') }}</option>
                        <option value="expired">{{ __('admin.certificates.status.expired') }}</option>
                    </select>
                </div>

                <div class="flex justify-end gap-2 border-t p-4">
                    <button @click="open = false; $wire.set('showModal', false)"
                        class="rounded-xl border px-4 py-2">
                        {{ __('admin.certificates.actions.cancel') }}
                    </button>

                    <button wire:click="save"
                        class="rounded-xl bg-slate-900 px-4 py-2 text-white">
                        {{ __('admin.certificates.actions.save') }}
                    </button>
                </div>
            </div>
        </div>
    </template>
</div>