<div x-data="{ open: @entangle('showModal').live }" class="max-w-6xl mx-auto px-4 space-y-6">

    <x-ui.page-header
        title="Topics"
        subtitle="Halaman utama topic. Semua detail content berada di bawah course yang relevan."
    >
        <button wire:click="create"
            class="px-4 py-2 rounded-xl bg-primary text-white text-sm">
            + New Topic
        </button>
    </x-ui.page-header>

    {{-- STATS --}}
    <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-5 gap-4">
        @foreach([
            ['Courses', $stats['courses']],
            ['Topics', $stats['topics']],
            ['Materials', $stats['materials']],
            ['Sessions', $stats['sessions']],
            ['Certificates', $stats['certificates']],
        ] as [$label, $value])
            <div class="rounded-2xl bg-white border p-5">
                <div class="text-xs text-slate-500">{{ $label }}</div>
                <div class="text-2xl font-bold mt-1">
                    {{ number_format($value) }}
                </div>
            </div>
        @endforeach
    </div>

    {{-- TABLE --}}
    <section class="space-y-4">

        <div class="rounded-2xl bg-white border p-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-3">

                <input wire:model.live="search"
                    class="border rounded-xl px-4 py-2"
                    placeholder="Search topic...">

                <select wire:model.live="courseFilter"
                    class="border rounded-xl px-4 py-2">
                    <option value="">All courses</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}">{{ $course->title }}</option>
                    @endforeach
                </select>

                <select wire:model.live="teacherFilter"
                    class="border rounded-xl px-4 py-2">
                    <option value="">All teachers</option>
                    @foreach($teachers as $teacher)
                        <option value="{{ $teacher->id }}">{{ $teacher->full_name }}</option>
                    @endforeach
                </select>

                <select wire:model.live="statusFilter"
                    class="border rounded-xl px-4 py-2">
                    <option value="">All status</option>
                    <option value="published">Published</option>
                    <option value="archived">Archived</option>
                    <option value="draft">Draft</option>
                </select>

            </div>
        </div>

        <x-ui.table-shell class="table-auto">
            <thead class="bg-slate-50 text-left">
                <tr>
                    <th class="px-4 py-3 font-medium text-slate-600 whitespace-nowrap">Course</th>
                    <th class="px-4 py-3 font-medium text-slate-600 whitespace-nowrap">Topic</th>
                    <th class="px-4 py-3 font-medium text-slate-600 whitespace-nowrap">Teacher</th>
                    <th class="px-4 py-3 font-medium text-slate-600 whitespace-nowrap">Order</th>
                    <th class="px-4 py-3 font-medium text-slate-600 whitespace-nowrap">Content</th>
                    <th class="px-4 py-3 font-medium text-slate-600 whitespace-nowrap">Status</th>
                    <th class="px-4 py-3 font-medium text-slate-600 whitespace-nowrap">Action</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-slate-100 bg-white">
                @forelse($rows as $row)
                    <tr class="align-top">
                        <td class="px-4 py-3 whitespace-nowrap">{{ $row->course?->title }}</td>

                        <td class="px-4 py-3">
                            <div class="font-medium text-slate-900">{{ $row->name }}</div>
                            <div class="text-xs text-slate-500">{{ $row->category }}</div>
                        </td>

                        <td class="px-4 py-3 whitespace-nowrap">{{ $row->teacher?->full_name }}</td>
                        <td class="px-4 py-3 whitespace-nowrap">{{ $row->sort_order }}</td>

                        <td class="px-4 py-3 text-xs text-slate-500">
                            Materials: {{ $row->materials_count }}<br>
                            Sessions: {{ $row->video_sessions_count }}<br>
                            Certificates: {{ $row->certificates_count }}
                        </td>

                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="px-2 py-1 rounded-full text-xs bg-slate-100">
                                {{ $row->status }}
                            </span>
                        </td>

                        <td class="px-4 py-3">
                            <div class="flex flex-wrap gap-2">
                                
                                <a href="{{ route('admin.materials.index', ['topicFilter' => $row->id]) }}"
                                   class="px-3 py-1.5 rounded-lg text-xs bg-slate-100 hover:bg-slate-200">
                                    Materials
                                </a>

                                <a href="{{ route('admin.sessions.index', ['topicFilter' => $row->id]) }}"
                                   class="px-3 py-1.5 rounded-lg text-xs bg-slate-100 hover:bg-slate-200">
                                    Sessions
                                </a>

                                <a href="{{ route('admin.assessments.index', ['topicFilter' => $row->id]) }}"
                                   class="px-3 py-1.5 rounded-lg text-xs bg-slate-100 hover:bg-slate-200">
                                    Assessments
                                </a>

                                <a href="{{ route('admin.attendances.index', ['topicFilter' => $row->id]) }}"
                                   class="px-3 py-1.5 rounded-lg text-xs bg-slate-100 hover:bg-slate-200">
                                    Attendances
                                </a>

                                <a href="{{ route('admin.certificates.index', ['topicFilter' => $row->id]) }}"
                                   class="px-3 py-1.5 rounded-lg text-xs bg-slate-100 hover:bg-slate-200">
                                    Certificates
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
                        <td colspan="7" class="px-4 py-6 text-center text-slate-500">
                            No data.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </x-ui.table-shell>

        <div>{{ $rows->links() }}</div>

    </section>

    {{-- MODAL --}}
    <template x-teleport="body">
        <div 
            x-show="open"
            x-cloak
            x-transition
            @click.self="open = false; $wire.set('showModal', false)"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
        >

            <div class="bg-white w-full max-w-2xl rounded-2xl shadow-xl flex flex-col max-h-[90vh]">

                <!-- HEADER -->
                <div class="flex justify-between items-center p-5 border-b">
                    <h2 class="text-lg font-semibold">
                        {{ $editingId ? 'Edit Topic' : 'New Topic' }}
                    </h2>

                    <button 
                        @click="open = false; $wire.set('showModal', false)"
                        class="text-slate-500 hover:text-black"
                    >
                        ✕
                    </button>
                </div>

                <!-- BODY (SCROLL AREA) -->
                <div class="p-5 space-y-4 overflow-y-auto">

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
                    <input wire:model="category" class="w-full border rounded-xl px-4 py-2" placeholder="Category">
                    <input wire:model="poster" class="w-full border rounded-xl px-4 py-2" placeholder="Poster path/url">

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <select wire:model="visibility" class="w-full border rounded-xl px-4 py-2">
                            <option value="Public">Public</option>
                            <option value="Private">Private</option>
                        </select>

                        <select wire:model="status" class="w-full border rounded-xl px-4 py-2">
                            <option value="published">Published</option>
                            <option value="archived">Archived</option>
                            <option value="draft">Draft</option>
                        </select>
                    </div>

                    <input wire:model="sort_order" type="number" class="w-full border rounded-xl px-4 py-2" placeholder="Sort order">

                    <textarea wire:model="description" rows="4" class="w-full border rounded-xl px-4 py-2" placeholder="Description"></textarea>

                </div>

                <!-- FOOTER -->
                <div class="flex justify-between items-center p-5 border-t bg-slate-50">
                    <div class="flex gap-2">
                        <button 
                            @click="open = false; $wire.set('showModal', false)"
                            class="px-4 py-2 border rounded-xl"
                        >
                            Cancel
                        </button>

                        <button 
                            wire:click="save"
                            class="px-4 py-2 bg-primary text-white rounded-xl"
                        >
                            Save
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </template>

</div>