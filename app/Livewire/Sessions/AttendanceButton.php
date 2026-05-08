<?php

namespace App\Livewire\Sessions;

use App\Models\Attendance;
use App\Models\VideoSession;
use Carbon\Carbon;
use Livewire\Component;

class AttendanceButton extends Component
{
    public VideoSession $session;

    public ?Attendance $attendance = null;

    public bool $canJoin = false;
    public bool $canClockOut = false;
    public string $stateLabel = 'Not checked in';

    public function mount(VideoSession $session): void
    {
        $this->session = $session->loadMissing('topic.course');

        if (auth()->check()) {
            $this->attendance = Attendance::where('video_session_id', $this->session->id)
                ->where('user_id', auth()->id())
                ->first();
        }

        $this->syncState();
    }

    public function joinSession()
    {
        abort_unless(auth()->check(), 403);

        $now = now();

        if (! $this->canClockIn($now)) {
            session()->flash('error', 'Waktu akses sesi sudah melewati batas clock-in.');
            return null;
        }

        $attendance = Attendance::updateOrCreate(
            [
                'video_session_id' => $this->session->id,
                'user_id' => auth()->id(),
            ],
            [
                'status' => $this->resolveStatus($now),
                'check_in_at' => $now,
                'ip_address' => request()->ip(),
            ]
        );

        $this->attendance = $attendance;
        $this->syncState();

        return redirect()->away($this->session->zoom_link);
    }

    public function clockOut(): void
    {
        abort_unless(auth()->check(), 403);

        if (! $this->attendance) {
            return;
        }

        $now = now();

        if (! $this->canClockOut($now)) {
            session()->flash('error', 'Clock-out hanya tersedia sampai 15 menit sebelum sesi berakhir.');
            return;
        }

        $this->attendance->update([
            'clock_out_at' => $now,
        ]);

        $this->attendance->refresh();
        $this->syncState();

        session()->flash('success', 'Clock-out tersimpan.');
    }

    private function syncState(): void
    {
        if (! $this->attendance) {
            $this->canJoin = $this->canClockIn(now());
            $this->canClockOut = false;
            $this->stateLabel = $this->canJoin ? 'Ready to join' : 'Session locked';
            return;
        }

        $this->canJoin = false;
        $this->canClockOut = ! $this->attendance->clock_out_at && $this->canClockOut(now());

        $this->stateLabel = match ($this->attendance->status) {
            'present' => 'Present',
            'late' => 'Late',
            'absent' => 'Absent',
            default => 'Checked in',
        };
    }

    private function clockInDeadline(): Carbon
    {
        $startWindow = $this->session->start_at->copy()->addMinutes(45);
        $endWindow = $this->session->end_at->copy()->subMinutes(15);

        return $startWindow->lt($endWindow) ? $startWindow : $endWindow;
    }

    private function canClockIn(Carbon $moment): bool
    {
        return $moment->betweenIncluded($this->session->start_at, $this->clockInDeadline());
    }

    private function canClockOut(Carbon $moment): bool
    {
        return $moment->betweenIncluded($this->session->start_at, $this->session->end_at->copy()->subMinutes(15));
    }

    private function resolveStatus(Carbon $checkIn): string
    {
        $minutesAfterStart = $this->session->start_at->diffInMinutes($checkIn, false);

        return $minutesAfterStart <= 15 ? 'present' : 'late';
    }

    public function render()
    {
        $deadline = $this->clockInDeadline();

        return view('livewire.sessions.attendance-button', [
            'clockInDeadline' => $deadline,
        ]);
    }
}