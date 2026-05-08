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

    public string $studyProgram = '';
    public string $sort = 'latest';

    protected $queryString = [
        'search' => ['except' => ''],
        'studyProgram' => ['except' => ''],
        'sort' => ['except' => 'latest'],
        'perPage' => ['except' => 9],
        'page' => ['except' => 1],
    ];

    public function updated($property): void
    {
        if (in_array($property, ['search', 'studyProgram', 'sort', 'perPage'])) {
            $this->resetPage();
        }
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

        $enrolledCourseIds = $user
            ? CourseEnrollment::where('user_id', $user->id)->pluck('course_id')->all()
            : [];

        $query = Course::query()
            ->with('studyProgram')
            ->withCount('topics')
            ->where('status', 'active');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('title', 'like', "%{$this->search}%")
                    ->orWhereHas('studyProgram', function ($sp) {
                        $sp->where('title', 'like', "%{$this->search}%");
                    });
            });
        }

        if ($this->studyProgram) {
            $query->whereHas('studyProgram', function ($sp) {
                $sp->where('slug', $this->studyProgram);
            });
        }

        if ($this->sort === 'title') {
            $query->orderBy('title');
        } elseif ($this->sort === 'topics') {
            $query->orderByDesc('topics_count');
        } else {
            $query->latest();
        }

        return view('livewire.courses.course-catalog', [
            'courses' => $query->paginate($this->perPage),
            'studyPrograms' => StudyProgram::orderBy('title')->get(),
            'enrolledCourseIds' => $enrolledCourseIds,
        ])->layout('layouts.learning');
    }
}