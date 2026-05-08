<div wire:poll.5s="refreshStatus" class="space-y-4">
    <div class="rounded-2xl bg-white border p-5 space-y-2">
        <h3 class="font-semibold">{{ $session->title }}</h3>
        <p class="text-sm text-gray-500">
            {{ $session->topic?->course?->title }} · {{ $session->topic?->name }}
        </p>
        <p class="text-sm">{{ $windowMessage }}</p>
    </div>

    @if ($errors->has('attendance'))
        <div class="rounded-lg bg-red-50 text-red-700 px-4 py-3 text-sm">
            {{ $errors->first('attendance') }}
        </div>
    @endif

    @if($alreadyCheckedIn)
        <div class="rounded-lg bg-green-50 text-green-700 px-4 py-3 text-sm">
            Anda sudah check-in.
        </div>
    @else
        @can('checkIn', $session)
            <button type="button"
                    wire:click="checkIn"
                    wire:loading.attr="disabled"
                    @disabled(!$canCheckIn)
                    class="px-4 py-2 rounded-lg bg-primary text-white text-sm disabled:opacity-50 disabled:bg-slate-300">
                Check In
            </button>
        @endcan
    @endif
</div>