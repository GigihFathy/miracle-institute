<?php

namespace App\Livewire\Sessions;

use App\Models\Attendance;
use App\Models\VideoSession;
use Carbon\Carbon;
use Livewire\Component;

class JoinSessionButton extends Component
{
    public string $videoSessionId;

    public VideoSession $session;

    public ?Attendance $attendance = null;

    public string $stateLabel = 'Terjadwal';
    public string $stateBadgeClass = 'bg-slate-100 text-slate-700';
    public bool $sessionEnded = false;
    public string $attendanceBadgeClass = 'bg-slate-100 text-slate-700';
    public string $attendanceLabel = 'Belum check-in';

    public bool $canJoin = false;
    public bool $canClockOut = false;

    public ?string $clockInDeadlineLabel = null;

    public function mount(string $videoSessionId): void
    {
        $this->videoSessionId = $videoSessionId;
        $this->refreshAttendance();
    }

    public function refreshAttendance(): void
    {
        $this->session = VideoSession::query()
            ->with(['topic.course'])
            ->findOrFail($this->videoSessionId);

        $this->attendance = Attendance::query()
            ->where('video_session_id', $this->session->id)
            ->where('user_id', auth()->id())
            ->first();

        $now = now();
        $start = Carbon::parse($this->session->start_at);
        $end = Carbon::parse($this->session->end_at);
        $clockInDeadline = $this->session->clockInClosesAt() ?? $start->copy()->addHour();

        if ($now->lt($start)) {
            $this->stateLabel = 'Terjadwal';
            $this->stateBadgeClass = 'bg-amber-100 text-amber-700';
            $this->sessionEnded = false;
        } elseif ($now->betweenIncluded($start, $end)) {
            $this->stateLabel = 'Berlangsung';
            $this->stateBadgeClass = 'bg-emerald-100 text-emerald-700';
            $this->sessionEnded = false;
        } else {
            $this->stateLabel = 'Selesai';
            $this->stateBadgeClass = 'bg-slate-100 text-slate-700';
            $this->sessionEnded = true;
        }

        if (!$this->attendance) {
            $this->attendanceBadgeClass = 'bg-slate-100 text-slate-700';
        } else {
            $this->attendanceBadgeClass = match ($this->attendance->status) {
                'present' => 'bg-emerald-100 text-emerald-700',
                'late' => 'bg-amber-100 text-amber-700',
                'online', 'absent' => 'bg-sky-100 text-sky-700',
                default => 'bg-slate-100 text-slate-700',
            };
        }

        $this->clockInDeadlineLabel = $clockInDeadline->format('d M Y, H:i');
        $clockOutWindowStart = $this->session->clockOutOpensAt() ?? $end->copy()->subMinutes(15);
        $clockOutWindowEnd = $this->session->clockOutClosesAt() ?? $end->copy()->addHours(2);

        $this->canJoin = $this->session->canJoinAt($now);

        $this->canClockOut = (bool) $this->attendance
            && ! $this->attendance->clock_out_at
            && $now->betweenIncluded($clockOutWindowStart, $clockOutWindowEnd);

        if ($this->attendance) {
            $this->attendanceLabel = match ($this->attendance->status) {
                'present' => 'Hadir',
                'late' => 'Terlambat',
                'online', 'absent' => 'Online',
                default => 'Sudah check-in',
            };
        } elseif ($now->gt($end)) {
            $this->attendanceBadgeClass = 'bg-sky-100 text-sky-700';
            $this->attendanceLabel = 'Online';
        } elseif ($now->betweenIncluded($start, $end)) {
            $this->attendanceLabel = 'Belum check-in';
        } else {
            $this->attendanceLabel = 'Belum dibuka';
        }
    }

    public function joinSession()
    {
        $this->refreshAttendance();

        if (!$this->canJoin) {
            session()->flash('error', 'Session sudah tidak tersedia.');
            return null;
        }

        return redirect()->route('sessions.join', ['videoSession' => $this->session->id]);
    }

    public function canRejoin(): bool
    {
        return $this->canJoin
            && (bool) $this->attendance
            && ! $this->attendance->clock_out_at;
    }

    public function clockOut(): void
    {
        $this->refreshAttendance();

        if (!$this->attendance) {
            session()->flash('error', 'Attendance belum tercatat.');
            return;
        }

        if ($this->attendance->clock_out_at) {
            session()->flash('info', 'Anda sudah melakukan clock out.');
            return;
        }

        if (! $this->canClockOut) {
            session()->flash('error', 'Clock out hanya bisa dilakukan mulai 15 menit sebelum sesi berakhir hingga 2 jam setelah sesi selesai.');
            return;
        }

        $attendance = Attendance::query()
            ->whereKey($this->attendance->id)
            ->lockForUpdate()
            ->first();

        if ($attendance &&!$attendance->clock_out_at) {
            $attendance->update([
                'clock_out_at' => now(),
            ]);
        }

        $this->refreshAttendance();
        session()->flash('success', 'Attendance completed.');
    }

    public function render()
    {
        return view('livewire.sessions.join-session-button');
    }
}
