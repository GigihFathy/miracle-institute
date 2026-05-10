<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
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
        return in_array($this->status, ['scheduled', 'ongoing'], true)
            && now()->betweenIncluded($this->start_at, $this->end_at);
    }

    public function attendanceStatusForNow(): string
    {
        return now()->diffInMinutes($this->start_at, false) <= 45 ? 'present' : 'late';
    }
}