<div class="rounded-2xl border p-4 space-y-4 bg-white"
     wire:poll.visible.10s="refreshAttendance">
    <div class="space-y-1">
        <div class="font-semibold">{{ $session->title }}</div>
        <div class="text-sm text-slate-500">
            {{ $session->topic?->course?->title }} · {{ $session->topic?->name }}
        </div>
        <div class="text-xs text-slate-500">
            {{ $session->start_at?->format('d M Y, H:i') ?? '-' }} - {{ $session->end_at?->format('H:i') ?? '-' }}
        </div>
    </div>

    <div class="flex items-center justify-between gap-3">
        <div class="text-sm">
            Status: <span class="font-medium">{{ $stateLabel }}</span>
        </div>

        <span class="text-xs px-2 py-1 rounded-full {{ $stateBadgeClass }}">
            {{ strtoupper($stateLabel) }}
        </span>
    </div>

    @if($clockInDeadlineLabel)
        <div class="text-xs text-slate-500">
            Clock-in deadline: {{ $clockInDeadlineLabel }}
        </div>
    @else
        <div class="text-xs text-amber-600">
            Session schedule belum lengkap.
        </div>
    @endif

    @if($attendance)
        <div class="space-y-1 text-sm">
            <div class="flex items-center gap-2">
                <span class="text-slate-500">Attendance:</span>
                <span class="text-xs px-2 py-1 rounded-full {{ $attendanceBadgeClass }}">
                    {{ strtoupper($attendance->status) }}
                </span>
            </div>
            <div>Check in: {{ $attendance->check_in_at?->format('d M Y, H:i') ?? '-' }}</div>
            <div>Check out: {{ $attendance->clock_out_at?->format('d M Y, H:i') ?? '-' }}</div>
        </div>
    @endif

    <div class="flex flex-wrap gap-2">
        @if(!$attendance)
            <button wire:click="joinSession"
                    wire:loading.attr="disabled"
                    wire:target="joinSession,refreshAttendance"
                    @disabled(!$canJoin)
                    class="px-4 py-2 rounded-xl bg-slate-900 text-white text-sm disabled:opacity-50 disabled:cursor-not-allowed">
                Join Session
            </button>
        @elseif(!$attendance->clock_out_at)
            <button wire:click="clockOut"
                    wire:loading.attr="disabled"
                    wire:target="clockOut,refreshAttendance"
                    @disabled(!$canClockOut)
                    class="px-4 py-2 rounded-xl border text-sm disabled:opacity-50 disabled:cursor-not-allowed">
                Clock Out
            </button>
        @else
            <div class="text-sm text-emerald-700">
                Attendance completed.
            </div>
        @endif
    </div>

    @if(session()->has('error'))
        <div class="text-sm text-rose-600">{{ session('error') }}</div>
    @endif

    @if(session()->has('info'))
        <div class="text-sm text-slate-600">{{ session('info') }}</div>
    @endif

    @if(session()->has('success'))
        <div class="text-sm text-emerald-600">{{ session('success') }}</div>
    @endif
</div>