<?php

namespace App\Livewire\Mentor\Dashboard;

use App\Models\Course;
use App\Models\Material;
use App\Models\Topic;
use App\Models\TopicProgress;
use App\Models\TopicUser;
use App\Models\VideoSession;
use Livewire\Component;
use Livewire\WithPagination;

class MentorDashboard extends Component
{
    use WithPagination;

    public string $courseSearch = '';
    public string $view = 'overview';

    public function mount(): void
    {
        $view = request()->query('view', 'overview');
        $this->view = in_array($view, ['overview', 'sessions'], true) ? $view : 'overview';
    }

    public function updatedCourseSearch(): void
    {
        $this->resetPage();
    }

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
            ->with(['course'])
            ->where(function ($q) use ($userId, $managedTopicIds) {
                $q->where('teacher_id', $userId);

                if ($managedTopicIds->isNotEmpty()) {
                    $q->orWhereIn('id', $managedTopicIds);
                }
            });

        $topics = (clone $topicsQuery)->latest()->get();
        $topicIds = $topics->pluck('id');
        $mentorStudentsCount = $topicIds->isEmpty()
            ? 0
            : TopicProgress::query()
                ->whereIn('topic_id', $topicIds)
                ->distinct()
                ->count('course_enrollment_id');

        $mentorMaterialsCount = Material::query()
            ->where('uploader_id', $userId)
            ->count();

        $managedCourses = Course::query()
            ->with([
                'topics' => function ($query) use ($userId, $managedTopicIds) {
                    $query
                        ->where(function ($topicQuery) use ($userId, $managedTopicIds) {
                            $topicQuery->where('teacher_id', $userId);

                            if ($managedTopicIds->isNotEmpty()) {
                                $topicQuery->orWhereIn('id', $managedTopicIds);
                            }
                        })
                        ->latest();
                },
            ])
            ->whereHas('topics', function ($query) use ($userId, $managedTopicIds) {
                $query->where(function ($topicQuery) use ($userId, $managedTopicIds) {
                    $topicQuery->where('teacher_id', $userId);

                    if ($managedTopicIds->isNotEmpty()) {
                        $topicQuery->orWhereIn('id', $managedTopicIds);
                    }
                });
            })
            ->when(filled($this->courseSearch), function ($query) {
                $search = trim($this->courseSearch);

                $query->where(function ($courseQuery) use ($search) {
                    $courseQuery->where('title', 'like', '%' . $search . '%');
                });
            })
            ->latest()
            ->paginate(5);

        $latestMaterials = Material::query()
            ->with(['topic.course'])
            ->where('uploader_id', $userId)
            ->latest()
            ->take(5)
            ->get();

        $nearestUpcomingSession = $topicIds->isEmpty()
            ? null
            : VideoSession::query()
                ->with(['topic.course'])
                ->whereIn('topic_id', $topicIds)
                ->whereNotNull('start_at')
                ->where('start_at', '>=', now())
                ->orderBy('start_at')
                ->first();

        $calendarSessions = $topicIds->isEmpty()
            ? collect()
            : VideoSession::query()
                ->with(['topic.course'])
                ->whereIn('topic_id', $topicIds)
                ->whereNotNull('start_at')
                ->orderBy('start_at')
                ->get();

        return view('livewire.mentor.dashboard.mentor-dashboard', [
            'view' => $this->view,
            'mentorTopicsCount' => $topics->count(),
            'mentorMaterialsCount' => $mentorMaterialsCount,
            'mentorStudentsCount' => $mentorStudentsCount,
            'managedCourses' => $managedCourses,
            'latestMaterials' => $latestMaterials,
            'nearestUpcomingSession' => $nearestUpcomingSession,
            'calendarSessions' => $calendarSessions,
        ])->layout('layouts.learning');
    }
}
