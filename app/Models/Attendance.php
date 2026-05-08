<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Attendance extends Model
{
    use HasFactory, HasUuid;

    protected $table = 'attendances';

    public $incrementing = false;
    protected $keyType = 'string';

    protected $guarded = [];

    protected $casts = [
        'check_in_at' => 'datetime',
        'clock_out_at' => 'datetime',
    ];

    public function videoSession()
    {
        return $this->belongsTo(VideoSession::class, 'video_session_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}