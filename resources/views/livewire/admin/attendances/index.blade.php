<div x-data="{ open: @entangle('showModal').live }" class="max-w-6xl mx-auto px-4 space-y-6">

    <x-ui.page-header
        title="Attendances"
        subtitle="Kelola absensi peserta per video session."
    >
        <div>
            <button wire:click="create"
                class="px-4 py-2 rounded-xl bg-primary text-white text-sm">
                + New Attendance
            </button>
        </div>
    </x-ui.page-header>

    {{-- STATS --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        @foreach([
            ['label'=>'Total','value'=>$stats['total']],
            ['label'=>'Present','value'=>$stats['present']],
            ['label'=>'Late','value'=>$stats['late']],
            ['label'=>'Absent','value'=>$stats['absent']],
        ] as $card)
            <div class="rounded-2xl bg-white border p-5">
                <div class="text-xs text-slate-500">{{ $card['label'] }}</div>
                <div class="text-2xl font-bold mt-1">
                    {{ number_format($card['value']) }}
                </div>
            </div>
        @endforeach
    </div>

    {{-- FILTER --}}
    <div class="rounded-2xl bg-white border p-4">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-3">

            <input wire:model.live="search"
                class="border rounded-xl px-4 py-2"
                placeholder="Search user/session...">

            <select wire:model.live="topicFilter" class="border rounded-xl px-4 py-2">
                <option value="">All topics</option>
                @foreach($topics as $topic)
                    <option value="{{ $topic->id }}">
                        {{ $topic->course?->title }} · {{ $topic->name }}
                    </option>
                @endforeach
            </select>

            <select wire:model.live="sessionFilter" class="border rounded-xl px-4 py-2">
                <option value="">All sessions</option>
                @foreach($sessions as $session)
                    <option value="{{ $session->id }}">
                        {{ $session->topic?->name }} · {{ $session->title }}
                    </option>
                @endforeach
            </select>

            <select wire:model.live="statusFilter" class="border rounded-xl px-4 py-2">
                <option value="">All status</option>
                <option value="present">Present</option>
                <option value="late">Late</option>
                <option value="absent">Absent</option>
            </select>

            <select wire:model.live="perPage" class="border rounded-xl px-4 py-2">
                <option value="10">10 / page</option>
                <option value="25">25 / page</option>
                <option value="50">50 / page</option>
            </select>

        </div>
    </div>

    {{-- TABLE --}}
    <x-ui.table-shell class="table-auto">
        <thead class="bg-slate-50 text-left">
            <tr>
                <th class="px-4 py-3 font-medium text-slate-600 whitespace-nowrap">Session</th>
                <th class="px-4 py-3 font-medium text-slate-600 whitespace-nowrap">Topic</th>
                <th class="px-4 py-3 font-medium text-slate-600 whitespace-nowrap">User</th>
                <th class="px-4 py-3 font-medium text-slate-600 whitespace-nowrap">Status</th>
                <th class="px-4 py-3 font-medium text-slate-600 whitespace-nowrap">Check In</th>
                <th class="px-4 py-3 font-medium text-slate-600 whitespace-nowrap">Action</th>
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

                    <td class="px-4 py-3 whitespace-nowrap">
                        <span class="px-2 py-1 rounded-full text-xs bg-slate-100">
                            {{ $row->status }}
                        </span>
                    </td>

                    <td class="px-4 py-3 whitespace-nowrap text-sm">
                        {{ $row->check_in_at?->format('d M Y H:i') ?? '-' }}
                    </td>

                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2 whitespace-nowrap">
                            
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
                    <td colspan="6" class="px-4 py-6 text-center text-slate-500">
                        No attendance records found.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </x-ui.table-shell>

    <div>{{ $rows->links() }}</div>

    {{-- MODAL --}}
    <template x-teleport="body">
        <div x-show="open"
            x-cloak
            x-transition
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4"
            @click.self="open=false; $wire.set('showModal', false)">

            <div class="bg-white w-full max-w-xl max-h-[90vh] overflow-y-auto rounded-2xl p-6 space-y-4">

                <div class="flex justify-between items-center">
                    <h2 class="text-lg font-semibold">
                        {{ $editingId ? 'Edit Attendance' : 'New Attendance' }}
                    </h2>
                    <button @click="open=false; $wire.set('showModal', false)">✕</button>
                </div>

                <select wire:model="video_session_id" class="w-full border rounded-xl px-4 py-2">
                    <option value="">Select session</option>
                    @foreach($sessions as $session)
                        <option value="{{ $session->id }}">
                            {{ $session->topic?->name }} · {{ $session->title }}
                        </option>
                    @endforeach
                </select>

                <select wire:model="user_id" class="w-full border rounded-xl px-4 py-2">
                    <option value="">Select user</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}">
                            {{ $user->full_name }} · {{ $user->email }}
                        </option>
                    @endforeach
                </select>

                <select wire:model="status" class="w-full border rounded-xl px-4 py-2">
                    <option value="present">Present</option>
                    <option value="late">Late</option>
                    <option value="absent">Absent</option>
                </select>

                <input wire:model="check_in_at" type="datetime-local"
                    class="w-full border rounded-xl px-4 py-2">

                <input wire:model="ip_address"
                    class="w-full border rounded-xl px-4 py-2"
                    placeholder="IP Address">

                <div class="flex justify-end gap-2 pt-2">
                    <button @click="open=false; $wire.set('showModal', false)"
                        class="px-4 py-2 border rounded-xl">
                        Cancel
                    </button>

                    <button wire:click="save"
                        class="px-4 py-2 bg-primary text-white rounded-xl">
                        Save
                    </button>
                </div>

            </div>
        </div>
    </template>

</div>