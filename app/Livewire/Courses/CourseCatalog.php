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

    // protected $queryString = [
    //     'search' => ['except' => ''],
    //     'studyProgram' => ['except' => ''],
    //     'sort' => ['except' => 'latest'],
    //     'perPage' => ['except' => 9],
    // ];

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
        $enrolledCourseIds = CourseEnrollment::where('user_id', $user->id)->pluck('course_id')->all();

        $query = Course::with('studyProgram')
            ->withCount('topics')
            ->where('status', 'active')
            ->when($this->search, fn ($q) => $q->where('title', 'like', '%' . $this->search . '%'))
            ->when($this->studyProgram, fn ($q) => $q->whereHas('studyProgram', fn ($sp) => $sp->where('slug', $this->studyProgram)));

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