<?php

namespace App\Livewire\Topics;

use App\Models\Attendance;
use App\Models\Topic;
use App\Models\TopicProgress;
use App\Models\TopicUser;
use App\Models\VideoSession;
use App\Services\ProgressService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;

class TopicPlayer extends Component
{
    use AuthorizesRequests;

    public Topic $topic;

    public string $activeTab = 'materials';
    public ?string $activeMaterialId = null;
    public ?string $topicStatus = null;
    public bool $topicCompleted = false;

    public bool $isMentor = false;
    public bool $canOpenMentorWorkspace = false;
    public bool $canStudentInteract = false;

    public function mount(string $slug): void
    {
        $this->topic = Topic::query()
            ->with([
                'course',
                'materials' => fn ($q) => $q->orderBy('sort_order'),
                'videoSessions' => fn ($q) => $q->orderBy('start_at'),
            ])
            ->where('slug', $slug)
            ->firstOrFail();

        $this->authorize('access', $this->topic);

        $user = auth()->user();

        $this->isMentor = $user
            ? $user->hasRole('disciples')
            : false;

        $this->canOpenMentorWorkspace = $this->isMentor && $this->canOpenMentorWorkspaceForTopic();
        $this->canStudentInteract = auth()->check() && !$this->canOpenMentorWorkspace;

        $this->activeMaterialId = $this->topic->materials->first()?->id;

        $this->hydrateTopicCompletion();
    }

    private function canOpenMentorWorkspaceForTopic(): bool
    {
        if (!auth()->check()) {
            return false;
        }

        if ((string) $this->topic->teacher_id === (string) auth()->id()) {
            return true;
        }

        return TopicUser::query()
            ->where('topic_id', $this->topic->id)
            ->where('user_id', auth()->id())
            ->where('role_type', 'collaborator')
            ->where('status', 'active')
            ->exists();
    }

    private function hydrateTopicCompletion(): void
    {
        $enrollment = auth()->user()
            ?->courseEnrollments()
            ->where('course_id', $this->topic->course_id)
            ->first();

        if (!$enrollment) {
            $this->topicCompleted = false;
            $this->topicStatus = null;

            return;
        }

        $progress = TopicProgress::query()
            ->where('course_enrollment_id', $enrollment->id)
            ->where('topic_id', $this->topic->id)
            ->first();

        $this->topicStatus = $progress?->status;
        $this->topicCompleted = $this->topicStatus === 'completed';
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = in_array($tab, ['materials', 'sessions'], true)
            ? $tab
            : 'materials';
    }

    public function selectMaterial(string $materialId): void
    {
        $this->activeMaterialId = $materialId;
        $this->activeTab = 'materials';
    }

    public function completeTopic(): void
    {
        abort_unless($this->canStudentInteract, 403);

        $enrollment = auth()->user()
            ?->courseEnrollments()
            ->where('course_id', $this->topic->course_id)
            ->first();

        abort_unless($enrollment, 403);

        if ($this->topicCompleted) {
            session()->flash('success', 'Topik sudah selesai.');

            return;
        }

        $progress = TopicProgress::firstOrNew([
            'course_enrollment_id' => $enrollment->id,
            'topic_id' => $this->topic->id,
        ]);

        $progress->status = 'completed';
        $progress->started_at ??= now();
        $progress->completed_at = now();
        $progress->save();

        $this->topicStatus = 'completed';
        $this->topicCompleted = true;

        session()->flash('success', 'Topik berhasil ditandai sebagai selesai.');
    }

    public function markViewed(ProgressService $progressService): void
    {
        abort_unless($this->canStudentInteract, 403);

        if (!$this->activeMaterialId) {
            return;
        }

        $progressService->markMaterialViewed(auth()->id(), $this->activeMaterialId);
        $progressService->recalculateTopicCompletion(auth()->id(), $this->topic->id);

        $this->hydrateTopicCompletion();
        $this->dispatch('$refresh');

        session()->flash('success', 'Material marked as viewed.');
    }

    public function syncTopicCompletion(ProgressService $progressService): void
    {
        abort_unless($this->canStudentInteract, 403);

        $progressService->recalculateTopicCompletion(
            auth()->id(),
            $this->topic->id
        );

        $this->hydrateTopicCompletion();
        $this->dispatch('$refresh');

        session()->flash('success', 'Topic progress diperbarui.');
    }

    public function render()
    {
        $activeMaterial = $this->topic->materials->firstWhere('id', $this->activeMaterialId);

        $materialUrl = null;
        if ($activeMaterial) {
            $materialUrl = $activeMaterial->external_url ?: (
                $activeMaterial->path
                    ? Storage::disk('public')->url($activeMaterial->path)
                    : null
            );
        }

        $topicStatus = null;
        $topicCompleted = false;
        $sessionAttendances = collect();

        $enrollment = auth()->user()
            ?->courseEnrollments()
            ->where('course_id', $this->topic->course_id)
            ->first();

        if ($enrollment) {
            $progress = TopicProgress::where('course_enrollment_id', $enrollment->id)
                ->where('topic_id', $this->topic->id)
                ->first();

            $topicStatus = $progress?->status;
            $topicCompleted = $topicStatus === 'completed';

            $sessionAttendances = Attendance::query()
                ->where('user_id', auth()->id())
                ->whereIn('video_session_id', $this->topic->videoSessions->pluck('id'))
                ->get()
                ->keyBy('video_session_id');
        }

        $this->topicStatus = $topicStatus;
        $this->topicCompleted = $topicCompleted;

        $attendanceStats = [
            'present' => $sessionAttendances->where('status', 'present')->count(),
            'late' => $sessionAttendances->where('status', 'late')->count(),
            'absent' => $sessionAttendances->where('status', 'absent')->count(),
            'checked_in' => $sessionAttendances->whereIn('status', ['present', 'late'])->count(),
        ];

        $hasSessionEnded = VideoSession::query()
            ->where('topic_id', $this->topic->id)
            ->where('end_at', '<=', now())
            ->exists();

        return view('livewire.topics.topic-player', [
            'activeMaterial' => $activeMaterial,
            'materialUrl' => $materialUrl,
            'topicStatus' => $topicStatus,
            'topicCompleted' => $topicCompleted,
            'sessionAttendances' => $sessionAttendances,
            'attendanceStats' => $attendanceStats,
            'hasSessionEnded' => $hasSessionEnded,
            'hasMaterials' => $this->topic->materials->isNotEmpty(),
            'hasSessions' => $this->topic->videoSessions->isNotEmpty(),
            'canOpenMentorWorkspace' => $this->canOpenMentorWorkspace,
            'canStudentInteract' => $this->canStudentInteract,
            'isMentor' => $this->isMentor,
        ])->layout('layouts.learning');
    }
}