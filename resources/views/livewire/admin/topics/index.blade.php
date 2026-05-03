<div class="space-y-6">
    <x-ui.page-header title="Topics" subtitle="Kelola topik, teacher, visibility, dan urutan tampil." />

    <div class="grid grid-cols-1 xl:grid-cols-[1.2fr_0.8fr] gap-6">
        <section class="space-y-4">
            <div class="rounded-2xl bg-white border p-4">
                <input wire:model.live="search" class="w-full border rounded-xl px-4 py-2" placeholder="Search topic...">
            </div>

            <div class="rounded-2xl bg-white border overflow-hidden">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 text-left">
                        <tr>
                            <th class="p-4">Name</th>
                            <th class="p-4">Course</th>
                            <th class="p-4">Teacher</th>
                            <th class="p-4">Visibility</th>
                            <th class="p-4">Status</th>
                            <th class="p-4">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rows as $row)
                            <tr class="border-t">
                                <td class="p-4">
                                    <div class="font-medium">{{ $row->name }}</div>
                                    <div class="text-xs text-slate-500">{{ $row->category }}</div>
                                </td>
                                <td class="p-4">{{ $row->course?->title }}</td>
                                <td class="p-4">{{ $row->teacher?->full_name }}</td>
                                <td class="p-4">{{ $row->visibility }}</td>
                                <td class="p-4">{{ $row->status }}</td>
                                <td class="p-4 flex gap-3">
                                    <button wire:click="edit('{{ $row->id }}')" class="text-blue-600">Edit</button>
                                    <button wire:click="delete('{{ $row->id }}')" class="text-rose-600">Delete</button>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="p-6 text-center text-slate-500">No data.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div>{{ $rows->links() }}</div>
        </section>

        <aside class="rounded-2xl bg-white border p-5 space-y-4 h-fit">
            <h2 class="font-semibold text-lg">{{ $editingId ? 'Edit' : 'New' }} Topic</h2>

            <select wire:model="course_id" class="w-full border rounded-xl px-4 py-2">
                <option value="">Select course</option>
                @foreach($courses as $course)
                    <option value="{{ $course->id }}">{{ $course->title }}</option>
                @endforeach
            </select>

            <select wire:model="teacher_id" class="w-full border rounded-xl px-4 py-2">
                <option value="">Select teacher</option>
                @foreach($teachers as $teacher)
                    <option value="{{ $teacher->id }}">{{ $teacher->full_name }}</option>
                @endforeach
            </select>

            <input wire:model="name" class="w-full border rounded-xl px-4 py-2" placeholder="Name">
            <input wire:model="poster" class="w-full border rounded-xl px-4 py-2" placeholder="Poster path/url">
            <select wire:model="visibility" class="w-full border rounded-xl px-4 py-2">
                <option value="Public">Public</option>
                <option value="Private">Private</option>
            </select>
            <select wire:model="status" class="w-full border rounded-xl px-4 py-2">
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
                <option value="draft">Draft</option>
            </select>
            <input wire:model="sort_order" type="number" class="w-full border rounded-xl px-4 py-2" placeholder="Sort order">
            <textarea wire:model="description" rows="4" class="w-full border rounded-xl px-4 py-2" placeholder="Description"></textarea>

            <button wire:click="save" class="w-full px-4 py-2 rounded-xl bg-slate-900 text-white">
                Save
            </button>
        </aside>
    </div>
</div>