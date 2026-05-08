<div class="rounded-2xl border p-4 space-y-4">
    <div class="space-y-1">
        <div class="font-semibold">{{ $session->title }}</div>
        <div class="text-sm text-slate-500">
            {{ $session->topic?->course?->title }} · {{ $session->topic?->name }}
        </div>
        <div class="text-xs text-slate-500">
            {{ $session->start_at?->format('d M Y, H:i') }} - {{ $session->end_at?->format('H:i') }}
        </div>
    </div>

    <div class="text-sm">
        Status: {{ $stateLabel }}
    </div>

    <div class="text-xs text-slate-500">
        Clock-in deadline: {{ $clockInDeadline->format('d M Y, H:i') }}
    </div>

    @if($attendance)
        <div class="space-y-1 text-sm">
            <div>Check in: {{ $attendance->check_in_at?->format('d M Y, H:i') ?? '-' }}</div>
            <div>Check out: {{ $attendance->clock_out_at?->format('d M Y, H:i') ?? '-' }}</div>
        </div>
    @endif

    <div class="flex flex-wrap gap-2">
        @if(! $attendance)
            <button wire:click="joinSession" class="px-4 py-2 rounded-xl bg-slate-900 text-white text-sm">
                Join Session
            </button>
        @else
            @if(! $attendance->clock_out_at)
                <button wire:click="clockOut" class="px-4 py-2 rounded-xl border text-sm">
                    Clock Out
                </button>
            @endif
        @endif
    </div>

    @if(session()->has('error'))
        <div class="text-sm text-rose-600">{{ session('error') }}</div>
    @endif

    @if(session()->has('success'))
        <div class="text-sm text-emerald-600">{{ session('success') }}</div>
    @endif
</div>