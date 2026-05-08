<?php

namespace App\Livewire\Sessions;

use App\Models\Attendance;
use App\Models\VideoSession;
use App\Services\AttendanceAutomationService;
use Carbon\Carbon;
use Livewire\Component;

class AttendanceButton extends Component
{
    public string $sessionId;

    public VideoSession $session;

    public ?Attendance $attendance = null;

    public bool $canJoin = false;
    public bool $canClockOut = false;
    public string $stateLabel = 'Not checked in';

    public ?Carbon $clockInDeadline = null;
    public ?Carbon $clockOutDeadline = null;

    public function mount(string $sessionId): void
    {
        $this->sessionId = $sessionId;

        $this->session = VideoSession::query()
            ->with(['topic.course'])
            ->findOrFail($this->sessionId);

        $this->refreshAttendance();
    }

    public function refreshAttendance(): void
    {
        $this->session->loadMissing('topic.course');

        if (! $this->session->start_at || ! $this->session->end_at) {
            $this->attendance = null;
            $this->canJoin = false;
            $this->canClockOut = false;
            $this->stateLabel = 'Schedule incomplete';
            $this->clockInDeadline = null;
            $this->clockOutDeadline = null;
            return;
        }

        $this->clockInDeadline = $this->resolveClockInDeadline();
        $this->clockOutDeadline = $this->resolveClockOutDeadline();

        $this->attendance = auth()->check()
            ? Attendance::query()
                ->where('video_session_id', $this->session->id)
                ->where('user_id', auth()->id())
                ->first()
            : null;

        $this->syncState();
    }

    public function joinSession(AttendanceAutomationService $automationService)
    {
        abort_unless(auth()->check(), 403);

        if (! $this->scheduleReady()) {
            session()->flash('error', 'Jadwal sesi belum lengkap.');
            return null;
        }

        try {
            $attendance = $automationService->recordSessionAccess(
                $this->session,
                auth()->user(),
                request()->ip()
            );

            $this->attendance = $attendance->fresh();
            $this->syncState();

            session()->flash('success', 'Presensi masuk tersimpan.');
        } catch (\Throwable $e) {
            session()->flash('error', $e->getMessage());
            $this->refreshAttendance();
            return null;
        }

        return redirect()->away($this->session->zoom_link);
    }

    public function clockOut(): void
    {
        abort_unless(auth()->check(), 403);

        if (! $this->attendance) {
            session()->flash('error', 'Belum ada data presensi masuk.');
            return;
        }

        if ($this->attendance->clock_out_at) {
            session()->flash('info', 'Anda sudah clock-out untuk sesi ini.');
            $this->refreshAttendance();
            return;
        }

        $now = now();

        if (! $this->canClockOut($now)) {
            session()->flash('error', 'Clock-out hanya tersedia sampai 15 menit sebelum sesi berakhir.');
            return;
        }

        $this->attendance->forceFill([
            'clock_out_at' => $now,
        ])->save();

        $this->attendance->refresh();
        $this->syncState();

        session()->flash('success', 'Clock-out tersimpan.');
    }

    public function syncState(): void
    {
        if (! $this->scheduleReady()) {
            $this->canJoin = false;
            $this->canClockOut = false;
            $this->stateLabel = 'Schedule incomplete';
            return;
        }

        if (! $this->attendance) {
            $this->canJoin = $this->canClockIn(now());
            $this->canClockOut = false;

            $this->stateLabel = match (true) {
                now()->lt($this->session->start_at) => 'Waiting for session',
                $this->canJoin => 'Ready to join',
                default => 'Clock-in closed',
            };

            return;
        }

        $this->canJoin = false;
        $this->canClockOut = ! $this->attendance->clock_out_at && $this->canClockOut(now());

        $this->stateLabel = $this->attendance->clock_out_at
            ? 'Completed'
            : match ($this->attendance->status) {
                'present' => 'Present',
                'late' => 'Late',
                'absent' => 'Absent',
                default => 'Checked in',
            };
    }

    private function scheduleReady(): bool
    {
        return (bool) ($this->session->start_at && $this->session->end_at);
    }

    private function resolveClockInDeadline(): ?Carbon
    {
        if (! $this->scheduleReady()) {
            return null;
        }

        $startWindow = $this->session->start_at->copy()->addMinutes(45);
        $endWindow = $this->session->end_at->copy()->subMinutes(15);

        return $startWindow->lt($endWindow) ? $startWindow : $endWindow;
    }

    private function resolveClockOutDeadline(): ?Carbon
    {
        if (! $this->scheduleReady()) {
            return null;
        }

        return $this->session->end_at->copy()->subMinutes(15);
    }

    private function canClockIn(Carbon $moment): bool
    {
        if (! $this->clockInDeadline) {
            return false;
        }

        return $moment->betweenIncluded($this->session->start_at, $this->clockInDeadline);
    }

    private function canClockOut(Carbon $moment): bool
    {
        if (! $this->clockOutDeadline) {
            return false;
        }

        return $moment->betweenIncluded($this->session->start_at, $this->clockOutDeadline);
    }

    public function render()
    {
        return view('livewire.sessions.attendance-button', [
            'clockInDeadline' => $this->clockInDeadline,
            'clockOutDeadline' => $this->clockOutDeadline,
        ]);
    }
}