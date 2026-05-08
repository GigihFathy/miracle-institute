<div class="space-y-6">
    <x-ui.page-header
        title="Attendances"
        subtitle="Presensi sesi dengan aturan clock-in ketat: maksimal 45 menit setelah start dan minimal 15 menit sebelum end."
    >
        <div>
            <button wire:click="create"
                class="px-4 py-2 rounded-xl bg-primary text-white text-sm">
                + New Attendance
            </button>
        </div>
    </x-ui.page-header>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="rounded-2xl bg-white border p-5">
            <div class="text-xs text-slate-500">Total</div>
            <div class="text-3xl font-bold mt-1">{{ number_format($stats['total']) }}</div>
        </div>
        <div class="rounded-2xl bg-emerald-50/30 border p-5">
            <div class="text-xs text-slate-500">Present</div>
            <div class="text-3xl font-bold mt-1 text-emerald-600">{{ number_format($stats['present']) }}</div>
        </div>
        <div class="rounded-2xl bg-amber-50/30 border p-5">
            <div class="text-xs text-slate-500">Late</div>
            <div class="text-3xl font-bold mt-1 text-amber-600">{{ number_format($stats['late']) }}</div>
        </div>
        <div class="rounded-2xl bg-rose-50/30 border p-5">
            <div class="text-xs text-slate-500">Absent</div>
            <div class="text-3xl font-bold mt-1 text-rose-600">{{ number_format($stats['absent']) }}</div>
        </div>
    </div>

    <div class="rounded-2xl bg-white border p-5 space-y-3">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-lg font-semibold">Attendance Rules</h2>
                <p class="text-sm text-slate-500">
                    Clock-in hanya dibuka setelah sesi dimulai dan ditutup pada batas sesi yang paling ketat.
                </p>
            </div>

            <div class="rounded-xl border bg-slate-50 px-4 py-2 text-sm text-slate-600">
                Start +45 min / End -15 min
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-5 gap-3">
            <input wire:model.live="search"
                   type="search"
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

    <div class="rounded-2xl bg-white border overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-left">
                <tr>
                    <th class="p-4">Session</th>
                    <th class="p-4">Participant</th>
                    <th class="p-4">Status</th>
                    <th class="p-4">Check In</th>
                    <th class="p-4">Window</th>
                    <th class="p-4">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rows as $row)
                    @php
                        $session = $row->videoSession;
                        $joinCutoff = $session?->start_at && $session?->end_at
                            ? ($session->start_at->copy()->addMinutes(45)->lt($session->end_at->copy()->subMinutes(15))
                                ? $session->start_at->copy()->addMinutes(45)
                                : $session->end_at->copy()->subMinutes(15))
                            : null;
                    @endphp

                    <tr class="border-t align-top">
                        <td class="p-4">
                            <div class="font-medium">{{ $session?->title }}</div>
                            <div class="text-xs text-slate-500">
                                {{ $session?->topic?->course?->title }} · {{ $session?->topic?->name }}
                            </div>
                            <div class="text-xs text-slate-400 mt-1">
                                {{ $session?->start_at?->format('d M Y, H:i') }} - {{ $session?->end_at?->format('H:i') }}
                            </div>
                        </td>

                        <td class="p-4">
                            <div class="font-medium">{{ $row->user?->full_name }}</div>
                            <div class="text-xs text-slate-500">{{ $row->user?->email }}</div>
                        </td>

                        <td class="p-4">
                            <div class="flex flex-col gap-2">
                                <span class="inline-flex w-fit px-2 py-1 rounded-full text-xs
                                    {{ $row->status === 'present' ? 'bg-emerald-100 text-emerald-700'
                                        : ($row->status === 'late' ? 'bg-amber-100 text-amber-700' : 'bg-rose-100 text-rose-700') }}">
                                    {{ ucfirst($row->status) }}
                                </span>

                                <span class="text-xs text-slate-500">
                                    {{ $this->attendanceTimingLabel($row) }}
                                </span>
                            </div>
                        </td>

                        <td class="p-4">
                            {{ $row->check_in_at?->format('d M Y, H:i') ?? '-' }}
                        </td>

                        <td class="p-4 text-xs text-slate-500">
                            @if($joinCutoff)
                                Open until {{ $joinCutoff->format('H:i') }}
                            @else
                                -
                            @endif
                        </td>

                        <td class="p-4">
                            <div class="flex flex-wrap gap-2">
                                <button wire:click="edit('{{ $row->id }}')"
                                    class="px-3 py-1.5 rounded-lg text-xs bg-blue-100 text-blue-700 hover:bg-blue-200">
                                    Edit
                                </button>

                                <button wire:click="setStatus('{{ $row->id }}', 'present')"
                                    class="px-3 py-1.5 rounded-lg text-xs bg-emerald-100 text-emerald-700 hover:bg-emerald-200">
                                    Present
                                </button>

                                <button wire:click="setStatus('{{ $row->id }}', 'late')"
                                    class="px-3 py-1.5 rounded-lg text-xs bg-amber-100 text-amber-700 hover:bg-amber-200">
                                    Late
                                </button>

                                <button wire:click="setStatus('{{ $row->id }}', 'absent')"
                                    class="px-3 py-1.5 rounded-lg text-xs bg-rose-100 text-rose-700 hover:bg-rose-200">
                                    Absent
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
                        <td colspan="6" class="p-6 text-center text-slate-500">
                            No attendance records found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

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