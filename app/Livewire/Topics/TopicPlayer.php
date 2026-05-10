<?php

namespace App\Livewire\Topics;

use App\Models\Attendance;
use App\Models\MaterialProgress;
use App\Models\Topic;
use App\Models\TopicProgress;
use App\Models\TopicUser;
use App\Models\VideoSession;
use App\Services\ProgressService;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
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

    public function confirmMaterialCompletion(ProgressService $progressService): void
    {
        abort_unless($this->canStudentInteract, 403);

        if (! $this->activeMaterialId) {
            session()->flash('error', 'Tidak ada material aktif.');
            return;
        }

        $result = $progressService->markMaterialCompleted(auth()->id(), $this->activeMaterialId);

        $this->hydrateTopicCompletion();

        $this->dispatch('material-complete-done');

        session()->flash(
            'success',
            $result['snapshot']['can_complete']
                ? 'Material selesai. Topik juga dinyatakan completed.'
                : 'Material selesai. Topik akan completed setelah semua materi dan syarat sesi terpenuhi.'
        );
    }

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

        $this->isMentor = $user ? $user->hasRole('disciples') : false;
        $this->canOpenMentorWorkspace = $this->isMentor && $this->canOpenMentorWorkspaceForTopic();
        $this->canStudentInteract = auth()->check() && ! $this->canOpenMentorWorkspace;

        $this->activeMaterialId = $this->topic->materials->first()?->id;

        $this->hydrateTopicCompletion();
    }

    private function canOpenMentorWorkspaceForTopic(): bool
    {
        if (! auth()->check()) {
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

        if (! $enrollment) {
            $this->topicCompleted = false;
            $this->topicStatus = null;

            return;
        }

        if ($this->canStudentInteract && auth()->check()) {
            app(ProgressService::class)->recalculateTopicCompletion(auth()->id(), $this->topic->id);
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

    public function sessionPhase(VideoSession $session): string
    {
        if (! $session->start_at || ! $session->end_at) {
            return 'invalid';
        }

        $now = now();

        if ($now->lt($session->start_at)) {
            return 'upcoming';
        }

        if ($now->betweenIncluded($session->start_at, $session->end_at)) {
            return 'live';
        }

        return 'ended';
    }

    public function sessionButtonText(VideoSession $session): string
    {
        return match ($this->sessionPhase($session)) {
            'upcoming' => 'Not Started',
            'live' => 'Join Session',
            'ended' => 'Completed',
            default => 'Unavailable',
        };
    }

    public function sessionBadgeClass(VideoSession $session): string
    {
        return match ($this->sessionPhase($session)) {
            'upcoming' => 'bg-amber-100 text-amber-700 border-amber-200',
            'live' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
            'ended' => 'bg-slate-100 text-slate-700 border-slate-200',
            default => 'bg-slate-100 text-slate-700 border-slate-200',
        };
    }

    public function sessionButtonClass(VideoSession $session): string
    {
        return match ($this->sessionPhase($session)) {
            'upcoming' => 'bg-slate-100 text-slate-400 border-slate-200 cursor-not-allowed',
            'live' => 'bg-slate-900 text-white border-slate-900 hover:bg-slate-700',
            'ended' => 'bg-slate-100 text-slate-400 border-slate-200 cursor-not-allowed',
            default => 'bg-slate-100 text-slate-400 border-slate-200 cursor-not-allowed',
        };
    }

    public function sessionCountdownLabel(VideoSession $session): string
    {
        if (! $session->start_at || ! $session->end_at) {
            return 'Session schedule belum lengkap.';
        }

        $now = now();

        if ($now->lt($session->start_at)) {
            $diff = $now->diffInSeconds($session->start_at);
            return 'Starts in ' . $this->formatDuration($diff);
        }

        if ($now->betweenIncluded($session->start_at, $session->end_at)) {
            $diff = $now->diffInSeconds($session->end_at);
            return 'Ends in ' . $this->formatDuration($diff);
        }

        return 'Session completed';
    }

    private function formatDuration(int $seconds): string
    {
        $hours = intdiv($seconds, 3600);
        $minutes = intdiv($seconds % 3600, 60);
        $secs = $seconds % 60;

        $parts = [];

        if ($hours > 0) {
            $parts[] = $hours . 'h';
        }

        if ($minutes > 0 || $hours > 0) {
            $parts[] = $minutes . 'm';
        }

        $parts[] = $secs . 's';

        return implode(' ', $parts);
    }

    public function joinSession(string $sessionId)
    {
        abort_unless(auth()->check(), 403);

        $session = VideoSession::query()
            ->where('topic_id', $this->topic->id)
            ->findOrFail($sessionId);

        abort_unless($this->sessionPhase($session) === 'live', 403);

        $user = auth()->user();
        $now = now();

        if (! $user->hasRole('student') && session('active_role') !== 'student') {
            return redirect()->away($session->zoom_link);
        }

        $status = $now->diffInMinutes($session->start_at, false) <= 45 ? 'present' : 'late';

        $lock = Cache::lock("attendance:join:{$session->id}:{$user->id}", 10);

        return $lock->block(3, function () use ($session, $user, $now, $status) {
            Attendance::firstOrCreate(
                [
                    'video_session_id' => $session->id,
                    'user_id' => $user->id,
                ],
                [
                    'status' => $status,
                    'check_in_at' => $now,
                    'clock_out_at' => null,
                    'ip_address' => request()->ip(),
                ]
            );

            return redirect()->away($session->zoom_link);
        });
    }

    public function completeTopic(ProgressService $progressService): void
    {
        abort_unless($this->canStudentInteract, 403);

        $snapshot = $progressService->topicCompletionSnapshot(auth()->id(), $this->topic->id);

        if (! $snapshot['can_complete']) {
            session()->flash('error', implode(' ', $snapshot['reasons']));

            return;
        }

        $progressService->syncTopicCompletion(auth()->id(), $this->topic->id);

        $this->hydrateTopicCompletion();

        session()->flash('success', 'Topik berhasil ditandai sebagai selesai.');
    }

    public function markViewed(ProgressService $progressService): void
    {
        abort_unless($this->canStudentInteract, 403);

        if (! $this->activeMaterialId) {
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

        $completionSnapshot = [
            'total_materials' => 0,
            'completed_materials' => 0,
            'incomplete_materials' => [],
            'all_materials_completed' => false,
            'total_sessions' => 0,
            'attended_sessions' => 0,
            'missing_sessions' => [],
            'all_sessions_attended' => false,
            'all_sessions_closed' => false,
            'can_complete' => false,
            'reasons' => [],
        ];

        $canMarkComplete = false;
        $activeMaterialProgress = null;

        if (auth()->check() && $this->canStudentInteract && $activeMaterial) {
            $enrolled = auth()->user()
                ?->courseEnrollments()
                ->where('course_id', $this->topic->course_id)
                ->exists();

            if ($enrolled) {
                $activeMaterialProgress = MaterialProgress::query()
                    ->where('user_id', auth()->id())
                    ->where('material_id', $activeMaterial->id)
                    ->first();

                $completionSnapshot = app(ProgressService::class)
                    ->topicCompletionSnapshot(auth()->id(), $this->topic->id);

                $canMarkComplete = ! ($activeMaterialProgress?->status === 'completed');
            }
        }

        $hasSessionEnded = $completionSnapshot['all_sessions_closed'] ?? false;

        return view('livewire.topics.topic-player', [
            'activeMaterialProgress' => $activeMaterialProgress,
            'completionSnapshot' => $completionSnapshot,
            'canMarkComplete' => $canMarkComplete,
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