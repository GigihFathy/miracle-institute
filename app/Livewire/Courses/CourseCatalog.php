<?php

namespace App\Livewire\Courses;

use App\Livewire\Concerns\WithTableState;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\StudyProgram;
use App\Services\CourseService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;

class CourseCatalog extends Component
{
    use WithTableState, AuthorizesRequests;

    public string $searchInput = '';
    public string $studyProgram = '';
    public string $sort = 'newest';

    protected $queryString = [
        'search' => ['except' => ''],
        'studyProgram' => ['except' => ''],
        'sort' => ['except' => 'newest'],
        'perPage' => ['except' => 12],
    ];

    public function mount(): void
    {
        $this->searchInput = $this->search;
    }

    public function submitSearch(): void
    {
        $this->search = trim($this->searchInput);
        $this->resetPage();
    }

    public function updatedSearchInput(): void
    {
        $this->search = trim($this->searchInput);
        $this->resetPage();
    }

    public function updatedStudyProgram(): void
    {
        $this->resetPage();
    }

    public function updatedSort(): void
    {
        $this->resetPage();
    }

    public function enroll(CourseService $courseService, string $courseId)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $course = Course::findOrFail($courseId);
        $this->authorize('enroll', $course);

        try {
            $courseService->enrollUser(auth()->id(), $courseId);
            session()->flash('success', 'Berhasil mendaftar course.');
            $this->dispatch('$refresh');
        } catch (\Throwable $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function render()
    {
        $user = auth()->user();

        $enrolledCourseIds = [];

        if ($user) {
            $enrolledCourseIds = CourseEnrollment::where('user_id', $user->id)
                ->pluck('course_id')
                ->all();
        }

        $query = Course::with('studyProgram')
            ->withCount('topics')
            ->where('status', 'active')
            ->when($this->search, function ($q) {
                $search = '%' . $this->search . '%';

                $q->where(function ($searchQuery) use ($search) {
                    $searchQuery->where('title', 'like', $search)
                        ->orWhere('description', 'like', $search)
                        ->orWhereHas('studyProgram', function ($studyProgramQuery) use ($search) {
                            $studyProgramQuery->where('title', 'like', $search);
                        });
                });
            })
            ->when($this->studyProgram, fn ($q) =>
                $q->whereHas('studyProgram', fn ($sp) => $sp->where('slug', $this->studyProgram))
            );

        match ($this->sort) {
            'oldest' => $query->oldest(),
            default => $query->latest(),
        };

        return view('livewire.courses.course-catalog', [
            'courses' => $query->paginate($this->perPage),
            'studyPrograms' => StudyProgram::orderBy('title')->get(),
            'enrolledCourseIds' => $enrolledCourseIds,
        ])->layout('layouts.learning');
    }
}
