<?php

namespace App\Livewire\Courses;

use App\Livewire\Concerns\WithTableState;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Services\CourseService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;

class CourseCatalog extends Component
{
    use WithTableState, AuthorizesRequests;

    public string $searchInput = '';
    public string $sort = 'newest';
    public bool $showEnrollModal = false;
    public ?string $pendingCourseId = null;
    public ?string $pendingCourseTitle = null;

    protected $queryString = [
        'search' => ['except' => ''],
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

    public function updatedSort(): void
    {
        $this->resetPage();
    }

    public function confirmEnroll(string $courseId): void
    {
        if (!auth()->check()) {
            $this->redirectRoute('login');

            return;
        }

        $course = Course::query()
            ->select(['id', 'title'])
            ->findOrFail($courseId);

        $this->pendingCourseId = $course->id;
        $this->pendingCourseTitle = $course->title;
        $this->showEnrollModal = true;
    }

    public function closeEnrollModal(): void
    {
        $this->reset(['showEnrollModal', 'pendingCourseId', 'pendingCourseTitle']);
    }

    public function enroll(CourseService $courseService)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        if (!$this->pendingCourseId) {
            return;
        }

        $courseId = $this->pendingCourseId;
        $course = Course::findOrFail($courseId);
        $this->authorize('enroll', $course);

        try {
            $courseService->enrollUser(auth()->id(), $courseId);
            $this->closeEnrollModal();
            session()->flash('success', 'Berhasil mendaftar course.');
            $this->dispatch('toast', type: 'success', message: 'Berhasil mendaftar course.');
            $this->dispatch('$refresh');
        } catch (\Throwable $e) {
            session()->flash('error', $e->getMessage());
            $this->dispatch('toast', type: 'error', message: $e->getMessage());
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

        $query = Course::withCount('topics')
            ->where('status', 'active')
            ->when($this->search, function ($q) {
                $search = '%' . $this->search . '%';

                $q->where(function ($searchQuery) use ($search) {
                    $searchQuery->where('title', 'like', $search)
                        ->orWhere('description', 'like', $search);
                });
            });

        match ($this->sort) {
            'oldest' => $query->oldest(),
            default => $query->latest(),
        };

        return view('livewire.courses.course-catalog', [
            'courses' => $query->paginate($this->perPage),
            'enrolledCourseIds' => $enrolledCourseIds,
        ])->layout('layouts.learning');
    }
}
