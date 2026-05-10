<div x-data="{ open: @entangle('showModal').live }" class="max-w-6xl mx-auto px-4 space-y-6">

    <x-ui.page-header
        title="Assessments"
        subtitle="Course-centered assessments. Questions dikelola di halaman terpisah."
    >
        <button wire:click="create"
            class="px-4 py-2 rounded-xl bg-slate-900 text-white text-sm">
            + New Assessment
        </button>
    </x-ui.page-header>

    <div class="grid grid-cols-1 md:grid-cols-3 xl:grid-cols-3 gap-3">
        <input wire:model.live="search"
            class="w-full border rounded-xl px-4 py-2"
            placeholder="Search assessment...">

        <select wire:model.live="courseFilter" class="border rounded-xl px-4 py-2">
            <option value="">All courses</option>
            @foreach($courses as $course)
                <option value="{{ $course->id }}">{{ $course->title }}</option>
            @endforeach
        </select>

        <select wire:model.live="statusFilter" class="border rounded-xl px-4 py-2">
            <option value="">All status</option>
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
            <option value="draft">Draft</option>
        </select>
    </div>

    <x-ui.table-shell class="table-auto">
        <thead class="bg-slate-50 text-left">
            <tr>
                <th class="px-4 py-3 font-medium text-slate-600 whitespace-nowrap">Course</th>
                <th class="px-4 py-3 font-medium text-slate-600 whitespace-nowrap">Assessment</th>
                <th class="px-4 py-3 font-medium text-slate-600 whitespace-nowrap">Q</th>
                <th class="px-4 py-3 font-medium text-slate-600 whitespace-nowrap">Grade</th>
                <th class="px-4 py-3 font-medium text-slate-600 whitespace-nowrap">Attempts</th>
                <th class="px-4 py-3 font-medium text-slate-600 whitespace-nowrap">Status</th>
                <th class="px-4 py-3 font-medium text-slate-600 whitespace-nowrap">Action</th>
            </tr>
        </thead>

        <tbody class="divide-y divide-slate-100 bg-white">
            @forelse($rows as $row)
                <tr class="align-top">
                    <td class="px-4 py-3 whitespace-nowrap">
                        {{ $row->course?->title ?? '-' }}
                    </td>

                    <td class="px-4 py-3">
                        <div class="font-medium text-slate-900">{{ $row->title }}</div>
                        <div class="text-xs text-slate-500">Course-centered assessment</div>
                    </td>

                    <td class="px-4 py-3 whitespace-nowrap">{{ $row->questions_count }}</td>
                    <td class="px-4 py-3 whitespace-nowrap">{{ $row->passing_grade }}</td>
                    <td class="px-4 py-3 whitespace-nowrap">{{ $row->attempts_count }}</td>

                    <td class="px-4 py-3 whitespace-nowrap">
                        <span class="px-2 py-1 rounded-full text-xs bg-slate-100">
                            {{ $row->status }}
                        </span>
                    </td>

                    <td class="px-4 py-3">
                        <div class="flex flex-wrap gap-2">
                            <a href="{{ route('admin.assessments.questions', $row->id) }}"
                               class="px-3 py-1.5 rounded-lg text-xs bg-slate-100 hover:bg-slate-200">
                                Questions
                            </a>

                            <div class="w-full border-t my-1"></div>

                            <button wire:click="edit('{{ $row->id }}')"
                                class="px-3 py-1.5 rounded-lg text-xs bg-blue-100 text-blue-700 hover:bg-blue-200">
                                Edit
                            </button>

                            <button wire:click="delete('{{ $row->id }}')"
                                class="px-3 py-1.5 rounded-lg text-xs bg-red-100 text-red-700 hover:bg-red-200">
                                Delete
                            </button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="px-4 py-6 text-center text-slate-500">
                        No assessments found
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
            <div class="bg-white w-full max-w-2xl rounded-2xl shadow-xl flex flex-col max-h-[90vh]">

                <div class="flex justify-between items-center p-5 border-b">
                    <h2 class="text-lg font-semibold">
                        {{ $editingId ? 'Edit Assessment' : 'New Assessment' }}
                    </h2>

                    <button
                        @click="open = false"
                        class="text-slate-500 hover:text-black">
                        ✕
                    </button>
                </div>

                <div class="p-5 space-y-4 overflow-y-auto">

                    <select wire:model="course_id" class="w-full border rounded-xl px-4 py-2">
                        <option value="">Select course</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}"
                                @disabled(!$editingId && $course->assessment)>
                                {{ $course->title }}
                            </option>
                        @endforeach
                    </select>

                    <input wire:model="title"
                        class="w-full border rounded-xl px-4 py-2"
                        placeholder="Title">

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <input wire:model="passing_grade" type="number"
                            class="w-full border rounded-xl px-4 py-2"
                            placeholder="Passing grade">

                        <input wire:model="question_limit" type="number"
                            class="w-full border rounded-xl px-4 py-2"
                            placeholder="Question limit">
                    </div>

                    <label class="flex items-center gap-2 text-sm">
                        <input type="checkbox" wire:model="randomize_questions">
                        Randomize questions
                    </label>

                    <select wire:model="status"
                        class="w-full border rounded-xl px-4 py-2">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="draft">Draft</option>
                    </select>

                </div>

                <div class="flex justify-end gap-2 p-5 border-t bg-slate-50">
                    <button
                        @click="open = false"
                        class="px-4 py-2 border rounded-xl">
                        Cancel
                    </button>

                    <button wire:click="save"
                        class="px-4 py-2 bg-slate-900 text-white rounded-xl">
                        Save
                    </button>
                </div>

            </div>
        </div>
    </template>

</div>