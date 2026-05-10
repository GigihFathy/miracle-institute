<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class TopicUserPermission extends Model
{
    use HasUuids;

    protected $table = 'topic_user_permissions';

    protected $fillable = [
        'topic_user_id',
        'permission',
        'granted_by',
    ];

    public function topicUser()
    {
        return $this->belongsTo(TopicUser::class, 'topic_user_id');
    }

    public function grantedBy()
    {
        return $this->belongsTo(User::class, 'granted_by');
    }
}