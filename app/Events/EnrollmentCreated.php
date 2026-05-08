<?php

namespace App\Events;

use App\Models\CourseEnrollment;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EnrollmentCreated
{
    use Dispatchable, SerializesModels;

    public function __construct(public CourseEnrollment $enrollment)
    {
    }
}