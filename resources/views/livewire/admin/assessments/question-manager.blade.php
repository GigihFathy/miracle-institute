<div class="space-y-6">

    <x-ui.page-header
        title="Question Manager"
        subtitle="{{ $assessment->title }}"
    >
        <button wire:click="create"
                class="px-4 py-2 rounded-xl bg-slate-900 text-white text-sm">
            + New Question
        </button>
    </x-ui.page-header>

    <!-- TABLE -->
    <div class="rounded-2xl bg-white border overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50">
                <tr>
                    <th class="p-4 text-left">Question</th>
                    <th class="p-4 text-left">Options</th>
                    <th class="p-4">Action</th>
                </tr>
            </thead>

            <tbody>
                @foreach($questions as $q)
                    <tr class="border-t">
                        <td class="p-4 font-medium">{{ $q->question }}</td>

                        <td class="p-4 text-xs space-y-1">
                            @foreach($q->options as $opt)
                                <div class="{{ $opt->is_correct ? 'text-emerald-600 font-semibold' : 'text-slate-500' }}">
                                    ✓ {{ $opt->option_text }}
                                </div>
                            @endforeach
                        </td>

                        <td class="p-4 flex gap-3 justify-center">
                            <button wire:click="edit('{{ $q->id }}')" class="text-blue-600">Edit</button>
                            <button wire:click="delete('{{ $q->id }}')" class="text-rose-600">Delete</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- MODAL -->
    <div x-data="{ open: @entangle('openModal') }">

        <div x-show="open"
             x-cloak
             class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">

            <div class="w-full max-w-xl bg-white rounded-2xl p-6 space-y-4">

                <h2 class="text-lg font-semibold">
                    {{ $editingId ? 'Edit Question' : 'New Question' }}
                </h2>

                <!-- QUESTION -->
                <textarea wire:model="question"
                          rows="4"
                          class="w-full border rounded-xl px-4 py-2"
                          placeholder="Question..."></textarea>

                <!-- OPTIONS -->
                <div class="space-y-3">
                    <div class="text-sm font-medium">Options (select correct one)</div>

                    @foreach($options as $i => $opt)
                        <div class="flex items-center gap-3">

                            <!-- pseudo checkbox (but single select) -->
                            <button type="button"
                                    wire:click="$set('correctIndex', {{ $i }})"
                                    class="w-5 h-5 rounded border flex items-center justify-center
                                    {{ $correctIndex === $i ? 'bg-emerald-600 text-white' : 'bg-white' }}">
                                @if($correctIndex === $i)
                                    ✓
                                @endif
                            </button>

                            <input type="text"
                                   wire:model="options.{{ $i }}.option_text"
                                   class="w-full border rounded-lg px-3 py-2"
                                   placeholder="Option {{ $i + 1 }}">
                        </div>
                    @endforeach
                </div>

                <!-- ACTION -->
                <div class="flex justify-end gap-3">
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
    </div>
</div>