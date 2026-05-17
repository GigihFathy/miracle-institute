<div class="space-y-6">

    <!-- Header -->
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold">Course Management</h1>

        <button wire:click="create"
                class="px-4 py-2 bg-black text-white rounded-xl">
            + Add Course
        </button>
    </div>

    <!-- Filters -->
    <div class="flex gap-3">
        <input type="text"
               wire:model.live="search"
               placeholder="Search course..."
               class="border rounded-xl px-4 py-2 w-full">

        <select wire:model.live="studyProgram"
                class="border rounded-xl px-4 py-2">
            <option value="">All Programs</option>
            @foreach($studyPrograms as $sp)
                <option value="{{ $sp->id }}">{{ $sp->title }}</option>
            @endforeach
        </select>
    </div>

    <!-- Table -->
    <div class="bg-white border rounded-2xl overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="p-3 text-left">Title</th>
                    <th>Program</th>
                    <th></th>
                </tr>
            </thead>

            <tbody>
                @foreach($courses as $course)
                    <tr class="border-t">
                        <td class="p-3 font-medium">{{ $course->title }}</td>
                        <td>{{ $course->studyProgram?->title }}</td>
                        <td class="flex gap-2 p-3">
                            <button wire:click="edit('{{ $course->id }}')" class="text-blue-600">Edit</button>
                            <button wire:click="delete('{{ $course->id }}')" class="text-red-600">Delete</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="p-3">
            {{ $courses->links() }}
        </div>
    </div>

    <!-- Form -->
    @if($editingId !== null)
        <div class="bg-white border rounded-2xl p-6 space-y-4">
            <h2 class="font-semibold text-lg">
                {{ $editingId ? 'Edit Course' : 'Create Course' }}
            </h2>

            <input wire:model="title" placeholder="Title" class="w-full border p-2 rounded-xl">
            <textarea wire:model="description" placeholder="Description" class="w-full border p-2 rounded-xl"></textarea>

          
            <select wire:model="study_program_id" class="border p-2 rounded-xl">
                <option value="">Select Program</option>
                @foreach($studyPrograms as $sp)
                    <option value="{{ $sp->id }}">{{ $sp->title }}</option>
                @endforeach
            </select>
          

            <button wire:click="save"
                    class="px-4 py-2 bg-black text-white rounded-xl">
                Save
            </button>
        </div>
    @endif

</div>