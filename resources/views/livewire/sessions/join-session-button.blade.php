<div class="space-y-4 rounded-2xl border bg-white p-4"
     wire:poll.visible.10s="refreshAttendance">
    <div class="space-y-1">
        <div class="font-semibold">{{ $session->title }}</div>
        <div class="text-xs text-slate-500">
            {{ $session->start_at?->format('d M Y, H:i') ?? '-' }} - {{ $session->end_at?->format('H:i') ?? '-' }}
        </div>
    </div>

    <div class="flex items-center justify-between gap-3">
        <div class="text-sm">
            {{ __('general.session_join_button.status') }} <span class="font-medium">{{ $stateLabel }}</span>
        </div>

        <span class="rounded-full px-2 py-1 text-xs {{ $stateBadgeClass }}">
            {{ strtoupper($stateLabel) }}
        </span>
    </div>

    @if($clockInDeadlineLabel)
        <div class="text-xs text-slate-500">
            {{ __('general.session_join_button.clock_in_deadline') }}: {{ $clockInDeadlineLabel }}
        </div>
    @else
        <div class="text-xs text-amber-600">
            {{ __('general.session_join_button.schedule_incomplete') }}
        </div>
    @endif

    <div class="flex flex-wrap gap-2">
        @if(! $attendance)
            <button wire:click="joinSession"
                    wire:loading.attr="disabled"
                    wire:target="joinSession,refreshAttendance"
                    @disabled(! $canJoin)
                    class="admin-primary-button rounded-xl px-4 py-2 text-sm disabled:cursor-not-allowed disabled:opacity-50">
                {{ __('general.session_join_button.actions.join_session') }}
            </button>
        @else
            @if($this->canRejoin())
                <button wire:click="joinSession"
                        wire:loading.attr="disabled"
                        wire:target="joinSession,refreshAttendance"
                        class="admin-primary-button rounded-xl px-4 py-2 text-sm disabled:cursor-not-allowed disabled:opacity-50">
                    Join Zoom
                </button>
            @endif

            @if(! $attendance->clock_out_at)
                <button wire:click="clockOut"
                        wire:loading.attr="disabled"
                        wire:target="clockOut,refreshAttendance"
                        @disabled(! $canClockOut)
                        class="rounded-xl border px-4 py-2 text-sm disabled:cursor-not-allowed disabled:opacity-50">
                    {{ __('general.session_join_button.actions.clock_out') }}
                </button>
            @else
                <div class="text-sm text-emerald-700">
                    {{ __('general.session_join_button.attendance_completed') }}
                </div>
            @endif
        @endif
    </div>

    <div class="space-y-1 text-sm">
        <div class="flex items-center gap-2">
            <span class="text-slate-500">{{ __('general.session_join_button.attendance') }}:</span>
            <span class="rounded-full px-2 py-1 text-xs {{ $attendanceBadgeClass }}">
                {{ $attendanceLabel }}
            </span>
        </div>
        <div>{{ __('general.session_join_button.check_in') }}: {{ $attendance?->check_in_at?->format('d M Y, H:i') ?? '-' }}</div>
        <div>{{ __('general.session_join_button.check_out') }}: {{ $attendance?->clock_out_at?->format('d M Y, H:i') ?? '-' }}</div>
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
