<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Concerns\HasUuid;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;

// #[Fillable(['name', 'email', 'password'])]
// #[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, HasUuid;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $guarded = [];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'dob' => 'date',
        'google_token_expires_at' => 'datetime',
    ];

    public function hasRole(string $role): bool
    {
        return $this->roles()
            ->where('name', $role)
            ->exists();
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_user')
            ->withPivot('assigned_at')
            ->withTimestamps();
    }

    public function courseEnrollments()
    {
        return $this->hasMany(CourseEnrollment::class);
    }

    public function topicProgresses()
    {
        return $this->hasManyThrough(
            TopicProgress::class,
            CourseEnrollment::class,
            'user_id',
            'course_enrollment_id',
            'id',
            'id'
        );
    }

    public function materialProgresses()
    {
        return $this->hasMany(MaterialProgress::class);
    }

    public function userViews()
    {
        return $this->hasMany(UserView::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function assessmentAttempts()
    {
        return $this->hasMany(AssessmentAttempt::class);
    }

    public function certificates()
    {
        return $this->hasMany(Certificate::class);
    }

    public function userDocuments()
    {
        return $this->hasMany(UserDocument::class);
    }

    public function uploadedMaterials()
    {
        return $this->hasMany(Material::class, 'uploader_id');
    }

    public function taughtTopics()
    {
        return $this->hasMany(Topic::class, 'teacher_id');
    }

    public function getFullNameAttribute()
    {
        return $this->name;
    }

    public function hasPermission($permission)
    {
        return $this->roles()
            ->whereHas('permissions', function ($q) use ($permission) {
                $q->where('name', $permission);
            })
            ->exists();
    }

    public function hasGoogleToken(): bool
    {
        return !empty($this->google_access_token)
            && !empty($this->google_refresh_token);
    }
}
