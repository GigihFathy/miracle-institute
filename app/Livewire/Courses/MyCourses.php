<?php

namespace App\Livewire\Courses;

use App\Livewire\Concerns\WithTableState;
use App\Models\CourseEnrollment;
use App\Models\TopicProgress;
use Livewire\Component;

class MyCourses extends Component
{
    use WithTableState;

    protected $queryString = [
        'search' => ['except' => ''],
        'perPage' => ['except' => 9],
    ];

    public function render()
    {
        $enrollments = CourseEnrollment::with([
                'course.studyProgram',
                'course.topics.materials',
                'course.topics.sessions',
                'course.topics.assessments',
            ])
            ->where('user_id', auth()->id())
            ->when($this->search, function ($query) {
                $query->whereHas('course', function ($courseQuery) {
                    $courseQuery->where('title', 'like', '%' . $this->search . '%');
                });
            })
            ->latest()
            ->paginate($this->perPage);

        $enrollments = $enrollments->through(function ($enrollment) {
            $totalTopics = $enrollment->course?->topics?->count() ?? 0;

            $completedTopics = TopicProgress::where('course_enrollment_id', $enrollment->id)
                ->where('status', 'completed')
                ->count();

            $percent = $totalTopics > 0
                ? (int) round(($completedTopics / $totalTopics) * 100)
                : 0;

            return [
                'enrollment' => $enrollment,
                'totalTopics' => $totalTopics,
                'completedTopics' => $completedTopics,
                'percent' => $percent,
            ];
        });

        return view('livewire.courses.my-courses', [
            'enrollments' => $enrollments,
        ])->layout('layouts.learning')->layout('layouts.learning');
    }
}