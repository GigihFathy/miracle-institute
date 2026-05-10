<?php

namespace App\Livewire\Mentor\Dashboard;

use App\Models\Assessment;
use App\Models\Material;
use App\Models\Topic;
use App\Models\TopicProgress;
use App\Models\TopicUser;
use Livewire\Component;

class MentorDashboard extends Component
{
    public function render()
    {
        $user = auth()->user();
        $userId = $user->id;

        $managedTopicIds = TopicUser::query()
            ->where('user_id', $userId)
            ->where('status', 'active')
            ->where(function ($q) {
                $q->where('role_type', 'owner')
                    ->orWhereHas('permissions', function ($p) {
                        $p->where('permission', 'manage_topics');
                    });
            })
            ->pluck('topic_id');

        $topicsQuery = Topic::query()
            ->with(['course.studyProgram'])
            ->where(function ($q) use ($userId, $managedTopicIds) {
                $q->where('teacher_id', $userId);

                if ($managedTopicIds->isNotEmpty()) {
                    $q->orWhereIn('id', $managedTopicIds);
                }
            });

        $topics = (clone $topicsQuery)->latest()->get();
        $topicIds = $topics->pluck('id');
        $courseIds = $topics->pluck('course_id')->filter()->unique()->values();

        $mentorStudentsCount = $topicIds->isEmpty()
            ? 0
            : TopicProgress::query()
                ->whereIn('topic_id', $topicIds)
                ->distinct()
                ->count('course_enrollment_id');

        $mentorMaterialsCount = Material::query()
            ->where('uploader_id', $userId)
            ->count();

        $mentorAssessmentsCount = $courseIds->isEmpty()
            ? 0
            : Assessment::query()
                ->whereIn('course_id', $courseIds)
                ->count();

        $latestTopics = (clone $topicsQuery)
            ->latest()
            ->take(6)
            ->get();

        $topicsByCourse = $topics->groupBy('course_id')->values();

        $latestMaterials = Material::query()
            ->with(['topic.course'])
            ->where('uploader_id', $userId)
            ->latest()
            ->take(5)
            ->get();

        return view('livewire.mentor.dashboard.mentor-dashboard', [
            'mentorTopicsCount' => $topics->count(),
            'mentorMaterialsCount' => $mentorMaterialsCount,
            'mentorStudentsCount' => $mentorStudentsCount,
            'mentorAssessmentsCount' => $mentorAssessmentsCount,
            'latestTopics' => $latestTopics,
            'topicsByCourse' => $topicsByCourse,
            'latestMaterials' => $latestMaterials,
        ])->layout('layouts.learning');
    }
}