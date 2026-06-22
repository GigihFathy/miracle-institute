<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory, HasUuid;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $guarded = [];

    public function topics()
    {
        return $this->hasMany(Topic::class);
    }

    public function enrollments()
    {
        return $this->hasMany(CourseEnrollment::class);
    }

    public function certificates()
    {
        return $this->hasMany(Certificate::class);
    }

    public function assessment()
    {
        return $this->hasOne(Assessment::class);
    }

    public function collaborators()
    {
        return $this->hasMany(CourseUser::class);
    }
}
