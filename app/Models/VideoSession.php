<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VideoSession extends Model
{
    use HasFactory, HasUuid;

    protected $table = 'video_sessions';

    public $incrementing = false;
    protected $keyType = 'string';

    protected $guarded = [];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'reminder_sent_at' => 'datetime',
    ];

    public function topic()
    {
        return $this->belongsTo(Topic::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'video_session_id');
    }

    public function isJoinable(): bool
    {
        return $this->canJoinAt(now());
    }

    public function attendanceStatusForNow(): string
    {
        return $this->attendanceStatusAt(now());
    }

    public function clockInClosesAt(): ?CarbonInterface
    {
        if (! $this->start_at || ! $this->end_at) {
            return null;
        }

        $oneHourAfterStart = $this->start_at->copy()->addHour();

        return $oneHourAfterStart->lt($this->end_at)
            ? $oneHourAfterStart
            : $this->end_at->copy();
    }

    public function clockOutOpensAt(): ?CarbonInterface
    {
        return $this->end_at?->copy()->subMinutes(15);
    }

    public function clockOutClosesAt(): ?CarbonInterface
    {
        return $this->end_at?->copy()->addHours(2);
    }

    public function canJoinAt(CarbonInterface $moment): bool
    {
        return $this->start_at
            && $this->end_at
            && in_array($this->status, ['scheduled', 'ongoing'], true)
            && $moment->betweenIncluded($this->start_at, $this->end_at);
    }

    public function canClockInAt(CarbonInterface $moment): bool
    {
        $clockInClosesAt = $this->clockInClosesAt();

        return $this->start_at
            && $clockInClosesAt
            && $moment->betweenIncluded($this->start_at, $clockInClosesAt);
    }

    public function canClockOutAt(CarbonInterface $moment): bool
    {
        $clockOutOpensAt = $this->clockOutOpensAt();
        $clockOutClosesAt = $this->clockOutClosesAt();

        return $clockOutOpensAt
            && $clockOutClosesAt
            && $moment->betweenIncluded($clockOutOpensAt, $clockOutClosesAt);
    }

    public function attendanceStatusAt(CarbonInterface $moment): string
    {
        return $this->canClockInAt($moment) ? 'present' : 'late';
    }
}
