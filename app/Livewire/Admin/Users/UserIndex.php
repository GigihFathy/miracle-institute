<?php

namespace App\Livewire\Admin\Users;

use App\Livewire\Concerns\WithAdminTableState;
use App\Models\User;
use App\Models\Role;
use Livewire\Component;

class UserIndex extends Component
{
    use WithAdminTableState;

    public $roleFilter = '';
    public $sort = 'latest';
    public bool $showStudentRecapModal = false;
    public ?User $selectedStudent = null;
    public array $studentRecapRows = [];

    public function toggleActive(string $userId): void
    {
        abort_if($userId === auth()->id(), 403);

        $user = User::findOrFail($userId);
        $user->update(['is_active' => ! $user->is_active]);

        $this->dispatch('toast',
            type: $user->is_active ? 'success' : 'warning',
            message: $user->is_active
                ? __('admin.users.actions.activated', ['name' => $user->full_name])
                : __('admin.users.actions.deactivated', ['name' => $user->full_name]),
        );
    }

    public function openStudentRecap(string $userId): void
    {
        $student = User::with('roles')->findOrFail($userId);

        abort_unless($student->hasRole('student'), 404);

        $enrollments = $student->courseEnrollments()
            ->with([
                'course.topics.videoSessions',
                'topicProgresses',
            ])
            ->latest('enrolled_at')
            ->get();

        $attendances = $student->attendances()
            ->whereHas('videoSession.topic.course')
            ->with('videoSession.topic.course')
            ->get()
            ->groupBy(function ($attendance) {
                return $attendance->videoSession?->topic?->course_id;
            });

        $this->selectedStudent = $student;
        $this->studentRecapRows = $enrollments->map(function ($enrollment) use ($attendances) {
            $course = $enrollment->course;
            $topics = $course?->topics ?? collect();
            $topicIds = $topics->pluck('id');
            $sessionIds = $topics->flatMap(fn ($topic) => $topic->videoSessions->pluck('id'))->values();
            $completedTopics = $enrollment->topicProgresses
                ->where('status', 'completed')
                ->count();
            $totalTopics = $topics->count();
            $courseAttendances = collect($attendances->get($course?->id, collect()))
                ->filter(fn ($attendance) => $sessionIds->contains($attendance->video_session_id));

            return [
                'course_title' => $course?->title ?? '-',
                'enrollment_status' => $enrollment->status,
                'enrolled_at' => optional($enrollment->enrolled_at)?->format('d M Y H:i') ?? '-',
                'completed_at' => optional($enrollment->completed_at)?->format('d M Y H:i') ?? '-',
                'topics_completed' => $completedTopics,
                'topics_total' => $totalTopics,
                'progress_percent' => $totalTopics > 0 ? (int) round(($completedTopics / $totalTopics) * 100) : 0,
                'sessions_total' => $sessionIds->count(),
                'attendance_present' => $courseAttendances->where('status', 'present')->count(),
                'attendance_late' => $courseAttendances->where('status', 'late')->count(),
                'attendance_absent' => $courseAttendances->whereIn('status', ['online', 'absent'])->count(),
            ];
        })->all();

        $this->showStudentRecapModal = true;
    }

    public function verifyUser(string $userId): void
    {
        $user = User::findOrFail($userId);

        if ($user->hasVerifiedEmail()) {
            return;
        }

        $user->markEmailAsVerified();

        if ($this->selectedStudent && (string) $this->selectedStudent->id === $userId) {
            $this->selectedStudent = $user->fresh(['roles']);
        }

        $this->dispatch('toast', type: 'success', message: 'Email ' . $user->full_name . ' berhasil diverifikasi.');
    }

    public function closeStudentRecapModal(): void
    {
        $this->showStudentRecapModal = false;
        $this->selectedStudent = null;
        $this->studentRecapRows = [];
    }

    public function render()
    {
        $query = User::with('roles')

            // SEARCH
            ->when($this->search, function ($q) {
                $q->where(function ($inner) {
                    $inner->where('name', 'like', "%{$this->search}%")
                        ->orWhere('email', 'like', "%{$this->search}%");
                });
            })

            // FILTER ROLE
            ->when($this->roleFilter, function ($q) {
                $q->whereHas('roles', function ($r) {
                    $r->where('name', $this->roleFilter);
                });
            });

        // SORTING
        $query = match ($this->sort) {
            'name_asc' => $query->orderBy('name'),
            'name_desc' => $query->orderByDesc('name'),
            'email_asc' => $query->orderBy('email'),
            'email_desc' => $query->orderByDesc('email'),
            default => $query->latest(),
        };

        return view('livewire.admin.users.index', [
            'rows' => $query->paginate($this->perPage),
            'roles' => Role::all()->sortBy('name')->values(),
        ])->layout('layouts.admin');
    }
}
