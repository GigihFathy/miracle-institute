<?php

namespace App\Livewire\Dashboard;

use App\Livewire\Concerns\WithTableState;
use App\Models\Course;
use App\Models\StudyProgram;
use App\Models\TopicProgress;
use Livewire\Component;

class ExploreDashboard extends Component
{
    use WithTableState;

    public string $studyProgram = '';
    public string $sort = 'latest';

    protected $queryString = [
        'search' => ['except' => ''],
        'studyProgram' => ['except' => ''],
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

    public function updatedStudyProgram(): void
    {
        $this->resetPage();
    }

    public function updatedSort(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $featured = Course::with('studyProgram')
            ->withCount('topics')
            ->where('status', 'active')
            ->latest()
            ->take(6)
            ->get();

        $courses = Course::with('studyProgram')
            ->withCount('topics')
            ->where('status', 'active')
            ->when($this->search, fn ($q) =>
                $q->where('title', 'like', '%' . $this->search . '%')
            )
            ->when($this->studyProgram, fn ($q) =>
                $q->whereHas('studyProgram', fn ($sp) =>
                    $sp->where('slug', $this->studyProgram)
                )
            )
            ->when($this->sort === 'title', fn ($q) => $q->orderBy('title'))
            ->when($this->sort === 'topics', fn ($q) => $q->orderByDesc('topics_count'))
            ->when($this->sort === 'latest', fn ($q) => $q->latest())
            ->paginate($this->perPage);

        if (!$this->isGuest) {
            $this->continueCourses = \App\Models\CourseEnrollment::with('course')
                ->where('user_id', auth()->id())
                ->latest()
                ->take(3)
                ->get();
        }

        return view('livewire.dashboard.explore-dashboard', [
            'featured' => $featured,
            'courses' => $courses,
            'studyPrograms' => StudyProgram::orderBy('title')->get(),
        ])->layout('layouts.learning');
    }
}