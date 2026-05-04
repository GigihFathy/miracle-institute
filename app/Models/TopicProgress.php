<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;

class TopicProgress extends Model
{
    use HasFactory, HasUuid;

    protected $table = 'topic_progresses';

    public $incrementing = false;
    protected $keyType = 'string';

    protected $guarded = [];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function courseEnrollment()
    {
        return $this->belongsTo(CourseEnrollment::class);
    }

    public function topic()
    {
        return $this->belongsTo(Topic::class);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->whereHas('courseEnrollment', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        });
    }
}