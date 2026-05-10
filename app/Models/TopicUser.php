<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class TopicUser extends Model
{
    use HasUuids;

    protected $table = 'topic_user';

    protected $fillable = [
        'topic_id',
        'user_id',
        'role_type',
        'status',
        'invited_by',
        'joined_at',
    ];

    protected $casts = [
        'joined_at' => 'datetime',
    ];

    public function topic()
    {
        return $this->belongsTo(Topic::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function inviter()
    {
        return $this->belongsTo(User::class, 'invited_by');
    }

    public function permissions()
    {
        return $this->hasMany(TopicUserPermission::class, 'topic_user_id');
    }

    public function hasPermission(string $permission): bool
    {
        return $this->permissions()->where('permission', $permission)->exists();
    }
}