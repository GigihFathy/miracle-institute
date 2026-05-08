<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;

class Topic extends Model
{
    use HasFactory, HasUuid;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $guarded = [];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function materials()
    {
        return $this->hasMany(Material::class);
    }

    public function videoSessions()
    {
        return $this->hasMany(VideoSession::class, 'topic_id');
    }

    public function attendances()
    {
        return $this->hasManyThrough(
            Attendance::class,
            VideoSession::class,
            'topic_id',
            'video_session_id',
            'id',
            'id'
        );
    }

    public function topicProgresses()
    {
        return $this->hasMany(TopicProgress::class);
    }

    public function userViews()
    {
        return $this->hasMany(UserView::class);
    }

    public function certificates()
    {
        return $this->hasMany(Certificate::class);
    }
}