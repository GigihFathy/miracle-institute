<?php

namespace App\Livewire\Mentor\Dashboard;

use App\Models\Assessment;
use App\Models\Certificate;
use App\Models\CourseEnrollment;
use App\Models\Material;
use App\Models\Topic;
use App\Models\TopicProgress;
use Livewire\Component;

class MentorDashboard extends Component
{
    public function render()
    {
        $user = auth()->user();
        $userId = $user->id;

        $hasStudentRole = $user->roles->contains('name', 'student');

        $mentoredTopicsQuery = Topic::with('course')
            ->where('teacher_id', $userId);

        $mentoredTopics = $mentoredTopicsQuery->latest()->take(6)->get();
        $topicIds = $mentoredTopicsQuery->pluck('id');

        $studentEnrollmentIds = TopicProgress::whereIn('topic_id', $topicIds)
            ->distinct()
            ->pluck('course_enrollment_id');

        $myCoursesCount = 0;
        $myTopicsCompleted = 0;
        $myCertificatesCount = 0;

        if ($hasStudentRole) {
            $myEnrollmentIds = CourseEnrollment::where('user_id', $userId)->pluck('id');

            $myCoursesCount = $myEnrollmentIds->count();

            $myTopicsCompleted = TopicProgress::whereIn('course_enrollment_id', $myEnrollmentIds)
                ->where('status', 'completed')
                ->count();

            $myCertificatesCount = Certificate::where('user_id', $userId)->count();
        }

        return view('livewire.mentor.dashboard.mentor-dashboard', [
            // Mentor Metrics
            'mentorTopicsCount' => $topicIds->count(),
            'mentorMaterialsCount' => Material::where('uploader_id', $userId)->count(),
            'mentorStudentsCount' => $studentEnrollmentIds->count(),
            'mentorAssessmentsCount' => Assessment::whereHas('topic', fn ($q) => $q->where('teacher_id', $userId))->count(),

            // Student Metrics (Conditional)
            'hasStudentRole' => $hasStudentRole,
            'myCoursesCount' => $myCoursesCount,
            'myTopicsCompleted' => $myTopicsCompleted,
            'myCertificatesCount' => $myCertificatesCount,

            // Lists
            'latestTopics' => $mentoredTopics,
            'latestMaterials' => Material::with('topic')
                ->where('uploader_id', $userId)
                ->latest()
                ->take(6)
                ->get(),
        ])->layout('layouts.learning');
    }
}