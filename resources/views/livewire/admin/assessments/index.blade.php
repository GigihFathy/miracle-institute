<div x-data="{ open: @entangle('showModal').live }" class="mx-auto max-w-6xl space-y-6 px-4">
    <x-ui.page-header
        title="{{ __('admin.assessments.page_title') }}"
        subtitle="{{ __('admin.assessments.page_subtitle') }}"
    >
        <button wire:click="create"
            class="rounded-xl bg-slate-900 px-4 py-2 text-sm text-white">
            {{ __('admin.assessments.actions.create') }}
        </button>
    </x-ui.page-header>

    @if($selectedCourse)
        <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700">
            {{ __('admin.assessments.filters.all_courses') }}:
            <span class="font-semibold text-slate-900">{{ $selectedCourse->title }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 gap-3 md:grid-cols-3 xl:grid-cols-3">
        <input wire:model.live="search"
            class="w-full rounded-xl border px-4 py-2"
            placeholder="{{ __('admin.assessments.search_placeholder') }}">

        <select wire:model.live="courseFilter" class="rounded-xl border px-4 py-2">
            <option value="">{{ __('admin.assessments.filters.all_courses') }}</option>
            @foreach($courses as $course)
                <option value="{{ $course->id }}">{{ $course->title }}</option>
            @endforeach
        </select>

        <select wire:model.live="statusFilter" class="rounded-xl border px-4 py-2">
            <option value="">{{ __('admin.assessments.filters.all_status') }}</option>
            <option value="active">{{ __('admin.assessments.status.active') }}</option>
            <option value="inactive">{{ __('admin.assessments.status.inactive') }}</option>
            <option value="draft">{{ __('admin.assessments.status.draft') }}</option>
        </select>
    </div>

    <x-ui.table-shell class="table-auto">
        <thead class="bg-slate-50 text-left">
            <tr>
                <th class="whitespace-nowrap px-4 py-3 font-medium text-slate-600">{{ __('admin.assessments.table.course') }}</th>
                <th class="whitespace-nowrap px-4 py-3 font-medium text-slate-600">{{ __('admin.assessments.table.assessment') }}</th>
                <th class="whitespace-nowrap px-4 py-3 font-medium text-slate-600">{{ __('admin.assessments.table.questions') }}</th>
                <th class="whitespace-nowrap px-4 py-3 font-medium text-slate-600">{{ __('admin.assessments.table.grade') }}</th>
                <th class="whitespace-nowrap px-4 py-3 font-medium text-slate-600">{{ __('admin.assessments.table.attempts') }}</th>
                <th class="whitespace-nowrap px-4 py-3 font-medium text-slate-600">{{ __('admin.assessments.table.status') }}</th>
                <th class="whitespace-nowrap px-4 py-3 font-medium text-slate-600">{{ __('admin.assessments.table.action') }}</th>
            </tr>
        </thead>

        <tbody class="divide-y divide-slate-100 bg-white">
            @forelse($rows as $row)
                <tr class="align-top">
                    <td class="whitespace-nowrap px-4 py-3">
                        {{ $row->course?->title ?? '-' }}
                    </td>

                    <td class="px-4 py-3">
                        <div class="font-medium text-slate-900">{{ $row->title }}</div>
                        <div class="text-xs text-slate-500">{{ __('admin.assessments.course_centered_note') }}</div>
                    </td>

                    <td class="whitespace-nowrap px-4 py-3">{{ $row->questions_count }}</td>
                    <td class="whitespace-nowrap px-4 py-3">{{ $row->passing_grade }}</td>
                    <td class="whitespace-nowrap px-4 py-3">{{ $row->attempts_count }}</td>

                    <td class="whitespace-nowrap px-4 py-3">
                        <span class="rounded-full bg-slate-100 px-2 py-1 text-xs">
                            {{ __('admin.assessments.status.' . $row->status, [], $row->status) }}
                        </span>
                    </td>

                    <td class="px-4 py-3">
                        <div class="flex flex-wrap gap-2">
                            <a href="{{ localized_route('admin.assessments.questions', $row->id) }}"
                               class="rounded-lg bg-slate-100 px-3 py-1.5 text-xs hover:bg-slate-200">
                                {{ __('admin.assessments.actions.questions') }}
                            </a>

                            <div class="my-1 w-full border-t"></div>

                            <button wire:click="edit('{{ $row->id }}')"
                                class="rounded-lg bg-blue-100 px-3 py-1.5 text-xs text-blue-700 hover:bg-blue-200">
                                {{ __('admin.assessments.actions.edit') }}
                            </button>

                            <button wire:click="delete('{{ $row->id }}')"
                                class="rounded-lg bg-red-100 px-3 py-1.5 text-xs text-red-700 hover:bg-red-200">
                                {{ __('admin.assessments.actions.delete') }}
                            </button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-4 py-6 text-center text-slate-500">
                        {{ __('admin.assessments.empty') }}
                    </td>
                </tr>
            @endforelse
        </tbody>
    </x-ui.table-shell>

    <div>{{ $rows->links() }}</div>

    <template x-teleport="body">
        <div
            x-show="open"
            x-cloak
            x-transition
            @click.self="open = false"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
        >
            <div class="flex max-h-[90vh] w-full max-w-2xl flex-col rounded-2xl bg-white shadow-xl">

                <div class="flex items-center justify-between border-b p-5">
                    <h2 class="text-lg font-semibold">
                        {{ $editingId ? __('admin.assessments.modal.edit_title') : __('admin.assessments.modal.create_title') }}
                    </h2>

                    <button @click="open = false" class="text-slate-500 hover:text-black">
                        ✕
                    </button>
                </div>

                <div class="space-y-4 overflow-y-auto p-5">
                    <select wire:model="course_id" class="w-full rounded-xl border px-4 py-2">
                        <option value="">{{ __('admin.assessments.form.select_course') }}</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}" @disabled(!$editingId && $course->assessment)>
                                {{ $course->title }}
                            </option>
                        @endforeach
                    </select>

                    <input wire:model="title"
                        class="w-full rounded-xl border px-4 py-2"
                        placeholder="{{ __('admin.assessments.form.title_placeholder') }}">

                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <input wire:model="passing_grade" type="number"
                            class="w-full rounded-xl border px-4 py-2"
                            placeholder="{{ __('admin.assessments.form.passing_grade_placeholder') }}">

                        <input wire:model="question_limit" type="number"
                            class="w-full rounded-xl border px-4 py-2"
                            placeholder="{{ __('admin.assessments.form.question_limit_placeholder') }}">
                    </div>

                    <label class="flex items-center gap-2 text-sm">
                        <input type="checkbox" wire:model="randomize_questions">
                        {{ __('admin.assessments.form.randomize_questions') }}
                    </label>

                    <select wire:model="status"
                        class="w-full rounded-xl border px-4 py-2">
                        <option value="active">{{ __('admin.assessments.status.active') }}</option>
                        <option value="inactive">{{ __('admin.assessments.status.inactive') }}</option>
                        <option value="draft">{{ __('admin.assessments.status.draft') }}</option>
                    </select>
                </div>

                <div class="flex justify-end gap-2 border-t bg-slate-50 p-5">
                    <button
                        @click="open = false"
                        class="rounded-xl border px-4 py-2">
                        {{ __('admin.assessments.actions.cancel') }}
                    </button>

                    <button wire:click="save"
                        class="rounded-xl bg-slate-900 px-4 py-2 text-white">
                        {{ __('admin.assessments.actions.save') }}
                    </button>
                </div>

            </div>
        </div>
    </template>
</div>