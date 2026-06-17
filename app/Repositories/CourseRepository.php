<?php

namespace App\Repositories;

use App\Models\Course;
use App\Models\CourseEnrollment;

class CourseRepository
{
    public function getPublishedCourses()
    {
        return Course::where('status', 'active')
            ->get();
    }

    public function getCourseWithTopics($courseId)
    {
        return Course::with(['topics.materials', 'topics.sessions'])
            ->findOrFail($courseId);
    }

    public function isUserEnrolled($userId, $courseId)
    {
        return CourseEnrollment::where([
            'user_id' => $userId,
            'course_id' => $courseId
        ])->exists();
    }
}
