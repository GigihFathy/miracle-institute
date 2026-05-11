<div x-data="{ open: @entangle('openModal').live }" class="space-y-6">

    <x-ui.page-header
        title="Question Manager"
        subtitle="{{ $assessment->course?->title ?? '-' }} · {{ $assessment->title }}"
    >
        <div class="flex gap-2">
            <a href="{{ route('admin.assessments.index', ['courseFilter' => $assessment->course_id]) }}"
               class="px-4 py-2 rounded-xl border text-sm">
                Back
            </a>

            <button wire:click="create"
                class="px-4 py-2 rounded-xl bg-slate-900 text-white text-sm">
                + New Question
            </button>
        </div>
    </x-ui.page-header>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 gap-4">
        <div class="rounded-2xl bg-white border p-5">
            <div class="text-xs text-slate-500">Course</div>
            <div class="text-lg font-bold mt-1">{{ $assessment->course?->title ?? '-' }}</div>
        </div>

        <div class="rounded-2xl bg-white border p-5">
            <div class="text-xs text-slate-500">Questions</div>
            <div class="text-2xl font-bold mt-1">{{ number_format($questionsCount) }}</div>
        </div>

        <div class="rounded-2xl bg-white border p-5">
            <div class="text-xs text-slate-500">Attempts</div>
            <div class="text-2xl font-bold mt-1">{{ number_format($attemptsCount) }}</div>
        </div>

        <div class="rounded-2xl bg-white border p-5">
            <div class="text-xs text-slate-500">Passing Grade</div>
            <div class="text-2xl font-bold mt-1">{{ $assessment->passing_grade }}</div>
        </div>
    </div>

    <div class="rounded-2xl bg-white border overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50">
                <tr>
                    <th class="p-4 text-left">Question</th>
                    <th class="p-4 text-left">Options</th>
                    <th class="p-4">Order</th>
                    <th class="p-4">Action</th>
                </tr>
            </thead>

            <tbody>
                @forelse($questions as $q)
                    <tr class="border-t align-top">
                        <td class="p-4">
                            <div class="font-medium">{{ $q->question }}</div>
                            <div class="text-xs text-slate-500">{{ $q->question_type }}</div>
                        </td>

                        <td class="p-4 text-xs space-y-1">
                            @foreach($q->options as $opt)
                                <div class="{{ $opt->is_correct ? 'text-emerald-600 font-semibold' : 'text-slate-500' }}">
                                    {{ $opt->is_correct ? '✓' : '•' }} {{ $opt->option_text }}
                                </div>
                            @endforeach
                        </td>

                        <td class="p-4 text-center">{{ $q->sort_order }}</td>

                        <td class="p-4">
                            <div class="flex gap-2 justify-center">
                                <button wire:click="edit('{{ $q->id }}')"
                                    class="px-3 py-1.5 rounded-lg text-xs bg-blue-100 text-blue-700 hover:bg-blue-200">
                                    Edit
                                </button>

                                <button wire:click="delete('{{ $q->id }}')"
                                    class="px-3 py-1.5 rounded-lg text-xs bg-red-100 text-red-700 hover:bg-red-200">
                                    Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="p-6 text-center text-slate-500">
                            No questions.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <template x-teleport="body">
        <div
            x-show="open"
            x-cloak
            x-transition
            @click.self="open = false"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
        >
            <div class="bg-white w-full max-w-xl rounded-2xl shadow-xl flex flex-col max-h-[90vh]">

                <div class="flex justify-between items-center p-5 border-b">
                    <h2 class="text-lg font-semibold">
                        {{ $editingId ? 'Edit Question' : 'New Question' }}
                    </h2>

                    <button @click="open = false" class="text-slate-500 hover:text-black">
                        ✕
                    </button>
                </div>

                <div class="p-5 space-y-4 overflow-y-auto">
                    <textarea wire:model="question"
                        rows="4"
                        class="w-full border rounded-xl px-4 py-2"
                        placeholder="Question..."></textarea>

                    <div class="space-y-3">
                        <div class="text-sm font-medium">Options</div>

                        @foreach($options as $i => $opt)
                            <div class="flex items-center gap-3">
                                <button
                                    type="button"
                                    wire:click="$set('correctIndex', {{ $i }})"
                                    class="w-5 h-5 rounded border flex items-center justify-center {{ $correctIndex === $i ? 'bg-emerald-600 text-white' : 'bg-white' }}"
                                >
                                    @if($correctIndex === $i) ✓ @endif
                                </button>

                                <input type="text"
                                    wire:model="options.{{ $i }}.option_text"
                                    class="w-full border rounded-lg px-3 py-2"
                                    placeholder="Option {{ $i + 1 }}">
                            </div>
                        @endforeach
                    </div>

                    <input wire:model="sort_order"
                        type="number"
                        class="w-full border rounded-xl px-4 py-2"
                        placeholder="Sort order">
                </div>

                <div class="flex justify-end gap-3 p-5 border-t bg-slate-50">
                    <button @click="open = false"
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