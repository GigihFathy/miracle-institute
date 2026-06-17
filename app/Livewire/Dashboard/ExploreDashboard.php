<?php

namespace App\Livewire\Dashboard;

use App\Livewire\Concerns\WithTableState;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\TopicProgress;
use Livewire\Component;

class ExploreDashboard extends Component
{
    use WithTableState;

    public string $sort = 'latest';

    protected $queryString = [
        'search' => ['except' => ''],
        'sort' => ['except' => 'latest'],
        'perPage' => ['except' => 9],
    ];

    public bool $isGuest = true;
    public $stats = [];
    public $continueCourses = [];

    public function mount()
    {
        $this->isGuest = !auth()->check();

        if (!$this->isGuest) {
            $user = auth()->user();

            $this->stats = [
                'courses' => $user->courseEnrollments()->count(),
                'completed_topics' => TopicProgress::forUser($user->id)
                    ->where('status', 'completed')
                    ->count(),

                'in_progress' => TopicProgress::forUser($user->id)
                    ->where('status', 'in_progress')
                    ->count(),
            ];
        }
    }

    public function updatedSort(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $featured = Course::withCount('topics')
            ->where('status', 'active')
            ->latest()
            ->take(6)
            ->get();

        $courses = Course::withCount('topics')
            ->where('status', 'active')
            ->when($this->search, fn ($q) =>
                $q->where('title', 'like', '%' . $this->search . '%')
            )
            ->when($this->sort === 'title', fn ($q) => $q->orderBy('title'))
            ->when($this->sort === 'topics', fn ($q) => $q->orderByDesc('topics_count'))
            ->when($this->sort === 'latest', fn ($q) => $q->latest())
            ->paginate($this->perPage);

        if (!$this->isGuest) {
            $this->continueCourses = CourseEnrollment::with(['course.topics'])
                ->where('user_id', auth()->id())
                ->latest()
                ->take(12)
                ->get()
                ->map(function ($enrollment) {
                    $totalTopics = $enrollment->course?->topics?->count() ?? 0;
                    $completedTopics = TopicProgress::query()
                        ->where('course_enrollment_id', $enrollment->id)
                        ->where('status', 'completed')
                        ->count();

                    $progressPercentage = $totalTopics > 0
                        ? (int) round(($completedTopics / $totalTopics) * 100)
                        : 0;

                    $enrollment->setAttribute('total_topics', $totalTopics);
                    $enrollment->setAttribute('completed_topics', $completedTopics);
                    $enrollment->setAttribute('progress_percentage', $progressPercentage);

                    return $enrollment;
                })
                ->reject(fn ($enrollment) => (int) ($enrollment->progress_percentage ?? 0) >= 100)
                ->take(4)
                ->values();
        }

        return view('livewire.dashboard.explore-dashboard', [
            'featured' => $featured,
            'courses' => $courses,
        ])->layout('layouts.learning');
    }
}
