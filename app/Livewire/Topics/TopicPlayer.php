<?php

namespace App\Livewire\Topics;

use App\Models\Attendance;
use App\Models\Material;
use App\Models\MaterialProgress;
use App\Models\Topic;
use App\Models\TopicProgress;
use App\Models\TopicUser;
use App\Models\VideoSession;
use App\Services\ProgressService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
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

    public bool $showSessionModal = false;
    public ?string $selectedSessionId = null;

    public function mount(string $slug): void
    {
        $this->topic = Topic::query()
            ->with([
                'course',
                'materials' => fn ($q) => $q->orderBy('sort_order')->orderBy('created_at'),
                'videoSessions' => fn ($q) => $q->orderBy('start_at'),
            ])
            ->where('slug', $slug)
            ->firstOrFail();

        $this->authorize('access', $this->topic);

        $user = auth()->user();
        $activeRole = (string) session('active_role', '');

        $this->isMentor = $user && $activeRole === 'disciples' && $user->hasRole('disciples');
        $this->canOpenMentorWorkspace = $this->isMentor && $this->canOpenMentorWorkspaceForTopic();
        $this->canStudentInteract = auth()->check() && $activeRole === 'student';

        $this->activeMaterialId = $this->materialsQuery()->value('id');

        $this->hydrateTopicCompletion();
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = in_array($tab, ['materials', 'sessions'], true)
            ? $tab
            : 'materials';
    }

    public function selectMaterial(string $materialId): void
    {
        $exists = $this->materialsQuery()->whereKey($materialId)->exists();

        if (! $exists) {
            return;
        }

        $this->activeMaterialId = $materialId;
        $this->activeTab = 'materials';
    }

    public function openSessionModal(string $sessionId): void
    {
        $session = $this->topic->videoSessions->firstWhere('id', $sessionId);

        if (! $session) {
            return;
        }

        if ($this->sessionPhase($session) === 'invalid') {
            return;
        }

        $this->selectedSessionId = $sessionId;
        $this->showSessionModal = true;
    }

    public function closeSessionModal(): void
    {
        $this->showSessionModal = false;
        $this->selectedSessionId = null;
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

        session()->flash('success', 'Material marked as viewed.');
    }

    public function confirmMaterialCompletion(ProgressService $progressService): void
    {
        abort_unless($this->canStudentInteract, 403);

        if (! $this->activeMaterialId) {
            session()->flash('error', 'Tidak ada material aktif.');
            return;
        }

        $result = $progressService->markMaterialCompleted(auth()->id(), $this->activeMaterialId);

        $this->hydrateTopicCompletion();

        session()->flash(
            'success',
            $result['snapshot']['can_complete']
                ? 'Material selesai. Topik juga dinyatakan completed.'
                : 'Material selesai. Topik akan completed setelah semua materi dan syarat sesi terpenuhi.'
        );
    }

    public function syncTopicCompletion(ProgressService $progressService): void
    {
        abort_unless($this->canStudentInteract, 403);

        $progressService->recalculateTopicCompletion(auth()->id(), $this->topic->id);

        $this->hydrateTopicCompletion();

        session()->flash('success', 'Topic progress diperbarui.');
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
            'upcoming' => __('general.topic_player.sessions.actions.not_started'),
            'live' => __('general.topic_player.sessions.actions.join_session'),
            'ended' => __('general.topic_player.sessions.actions.completed'),
            default => __('general.topic_player.sessions.actions.unavailable'),
        };
    }

    public function sessionCountdownLabel(VideoSession $session): string
    {
        if (! $session->start_at || ! $session->end_at) {
            return __('general.topic_player.sessions.countdown_invalid');
        }

        $now = now();

        if ($now->lt($session->start_at)) {
            return __('general.topic_player.sessions.starts_in') . ' ' . $this->formatDuration(
                $now->diffInSeconds($session->start_at)
            );
        }

        if ($now->betweenIncluded($session->start_at, $session->end_at)) {
            return __('general.topic_player.sessions.ends_in') . ' ' . $this->formatDuration(
                $now->diffInSeconds($session->end_at)
            );
        }

        return __('general.topic_player.sessions.completed_label');
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

    public function joinSession(string $sessionId)
    {
        abort_unless(auth()->check(), 403);

        $session = VideoSession::query()
            ->where('topic_id', $this->topic->id)
            ->findOrFail($sessionId);

        abort_unless($this->sessionPhase($session) === 'live', 403);

        $user = auth()->user();

        if (! $user->hasRole('student') && session('active_role') !== 'student') {
            return redirect()->to(localized_route('sessions.join', [
                'videoSession' => $session->id,
            ]));
        }

        return redirect()->to(localized_route('sessions.join', [
            'videoSession' => $session->id,
        ]));
    }

    public function clockOutSession(string $sessionId): void
    {
        abort_unless(auth()->check(), 403);

        $session = VideoSession::query()
            ->where('topic_id', $this->topic->id)
            ->findOrFail($sessionId);

        $attendance = Attendance::query()
            ->where('video_session_id', $session->id)
            ->where('user_id', auth()->id())
            ->first();

        if (! $attendance) {
            session()->flash('error', 'Attendance belum tercatat.');

            return;
        }

        if ($attendance->clock_out_at) {
            session()->flash('info', 'Anda sudah melakukan clock out.');

            return;
        }

        if (! $this->canClockOut($session, $attendance)) {
            session()->flash('error', 'Clock out hanya bisa dilakukan saat session masih aktif.');

            return;
        }

        $lockedAttendance = Attendance::query()
            ->whereKey($attendance->id)
            ->lockForUpdate()
            ->first();

        if (! $lockedAttendance) {
            session()->flash('error', 'Attendance tidak ditemukan.');

            return;
        }

        if (! $lockedAttendance->clock_out_at) {
            $lockedAttendance->update([
                'clock_out_at' => now(),
            ]);
        }

        session()->flash('success', 'Clock out berhasil dicatat.');
    }

    public function render()
    {
        $materials = $this->materialsQuery()->get();

        $activeMaterial = $this->activeMaterialId
            ? $materials->firstWhere('id', $this->activeMaterialId)
            : $materials->first();

        if (! $activeMaterial && $materials->isNotEmpty()) {
            $activeMaterial = $materials->first();
            $this->activeMaterialId = $activeMaterial->id;
        }

        if ($activeMaterial && ! $this->activeMaterialId) {
            $this->activeMaterialId = $activeMaterial->id;
        }

        $materialCards = $this->buildMaterialCards($materials);

        $activeMaterialCard = $activeMaterial
            ? ($materialCards[(string) $activeMaterial->id] ?? null)
            : null;

        $materialPreviewUrl = $activeMaterialCard['preview_url'] ?? null;

        $topicStatus = null;
        $topicCompleted = false;
        $sessionAttendances = collect();

        $enrollment = auth()->user()
            ?->courseEnrollments()
            ->where('course_id', $this->topic->course_id)
            ->first();

        if ($enrollment) {
            $progress = TopicProgress::query()
                ->where('course_enrollment_id', $enrollment->id)
                ->where('topic_id', $this->topic->id)
                ->first();

            $topicStatus = $progress?->status;
            $topicCompleted = $topicStatus === 'completed';

            $sessionIds = $this->topic->videoSessions->pluck('id');

            if (auth()->check() && $sessionIds->isNotEmpty()) {
                $sessionAttendances = Attendance::query()
                    ->where('user_id', auth()->id())
                    ->whereIn('video_session_id', $sessionIds)
                    ->get()
                    ->keyBy('video_session_id');
            }
        }

        $this->topicStatus = $topicStatus;
        $this->topicCompleted = $topicCompleted;

        $selectedSession = $this->selectedSessionId
            ? $this->topic->videoSessions->firstWhere('id', $this->selectedSessionId)
            : null;

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
            'activeMaterialCard' => $activeMaterialCard,
            'materialCards' => $materialCards,
            'materialUrl' => $materialPreviewUrl,
            'materialPreviewUrl' => $materialPreviewUrl,
            'topicStatus' => $topicStatus,
            'topicCompleted' => $topicCompleted,
            'sessionAttendances' => $sessionAttendances,
            'attendanceStats' => $attendanceStats,
            'hasSessionEnded' => $hasSessionEnded,
            'hasMaterials' => $materials->isNotEmpty(),
            'hasSessions' => $this->topic->videoSessions->isNotEmpty(),
            'canOpenMentorWorkspace' => $this->canOpenMentorWorkspace,
            'canStudentInteract' => $this->canStudentInteract,
            'isMentor' => $this->isMentor,
            'materials' => $materials,
            'selectedSession' => $selectedSession,
            'showSessionModal' => $this->showSessionModal,
        ])->layout('layouts.learning');
    }

    private function materialsQuery()
    {
        $query = $this->topic->materials()
            ->with('uploader')
            ->orderBy('sort_order')
            ->orderBy('created_at');

        if (! $this->canOpenMentorWorkspace) {
            $query->where('status', 'active')
                ->where('visibility', 'public');
        }

        return $query;
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

    private function buildMaterialCards($materials): array
    {
        return $materials
            ->mapWithKeys(function ($material) {
                $youtubeId = $material->type === 'video'
                    ? $this->extractYoutubeVideoId((string) $material->external_url)
                    : null;

                $previewUrl = $this->resolveMaterialPreviewUrl($material, $youtubeId);
                $downloadUrl = $this->resolveMaterialDownloadUrl($material);

                $sourceValue = $material->type === 'video'
                    ? $material->external_url
                    : $material->path;

                $driveId = $this->extractGoogleDriveFileId((string) $sourceValue);

                $thumbnailUrl = null;

                if ($youtubeId) {
                    $thumbnailUrl = "https://img.youtube.com/vi/{$youtubeId}/hqdefault.jpg";
                } elseif ($driveId) {
                    $thumbnailUrl = "https://drive.google.com/thumbnail?id={$driveId}&sz=w800";
                }

                return [
                    (string) $material->id => [
                        'id' => (string) $material->id,
                        'name' => $material->name,
                        'type' => $material->type,
                        'status' => $material->status,
                        'sort_order' => (int) $material->sort_order,
                        'uploader_name' => $material->uploader?->name,
                        'is_video' => $material->type === 'video',
                        'is_document' => in_array($material->type, ['pdf', 'ppt'], true),
                        'youtube_id' => $youtubeId,
                        'thumbnail_url' => $thumbnailUrl,
                        'preview_url' => $previewUrl,
                        'download_url' => $downloadUrl,
                        'watch_url' => $youtubeId
                            ? "https://www.youtube.com/watch?v={$youtubeId}"
                            : null,
                        'source_value' => $sourceValue,
                        'has_preview' => filled($previewUrl),
                    ],
                ];
            })
            ->all();
    }

    private function resolveMaterialPreviewUrl($material, ?string $youtubeId = null): ?string
    {
        if (! $material) {
            return null;
        }

        if ($material->type === 'video') {
            $youtubeId = $youtubeId ?: $this->extractYoutubeVideoId((string) $material->external_url);

            if ($youtubeId) {
                return "https://www.youtube.com/embed/{$youtubeId}?rel=0&modestbranding=1";
            }

            return $this->toGoogleDrivePreviewUrl((string) $material->external_url);
        }

        if (in_array($material->type, ['pdf', 'ppt'], true)) {
            return $this->toGoogleDrivePreviewUrl((string) $material->path);
        }

        return null;
    }

    private function resolveMaterialDownloadUrl($material): ?string
    {
        if (! $material || ! in_array($material->type, ['pdf', 'ppt'], true)) {
            return null;
        }

        $source = trim((string) $material->path);

        if ($source === '') {
            return null;
        }

        $driveId = $this->extractGoogleDriveFileId($source);

        if ($driveId) {
            return "https://drive.google.com/uc?export=download&id={$driveId}";
        }

        return filter_var($source, FILTER_VALIDATE_URL)
            ? $source
            : null;
    }

    private function toGoogleDrivePreviewUrl(string $input): ?string
    {
        $input = trim($input);

        if ($input === '') {
            return null;
        }

        $fileId = $this->extractGoogleDriveFileId($input);

        if ($fileId) {
            return "https://drive.google.com/file/d/{$fileId}/preview";
        }

        return filter_var($input, FILTER_VALIDATE_URL)
            ? $input
            : null;
    }

    private function extractGoogleDriveFileId(string $input): ?string
    {
        $input = trim($input);

        if ($input === '') {
            return null;
        }

        if (preg_match('/^[A-Za-z0-9_-]{15,}$/', $input)) {
            return $input;
        }

        $patterns = [
            '/\/file\/d\/([A-Za-z0-9_-]{15,})/',
            '/[?&]id=([A-Za-z0-9_-]{15,})/',
            '/\/d\/([A-Za-z0-9_-]{15,})/',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $input, $matches)) {
                return $matches[1];
            }
        }

        return null;
    }

    private function extractYoutubeVideoId(string $input): ?string
    {
        $input = trim(html_entity_decode($input));

        if ($input === '') {
            return null;
        }

        if (preg_match('/^[A-Za-z0-9_-]{11}$/', $input)) {
            return $input;
        }

        $parts = parse_url($input);

        if (! empty($parts['query'])) {
            parse_str($parts['query'], $query);

            if (! empty($query['v']) && preg_match('/^[A-Za-z0-9_-]{11}$/', $query['v'])) {
                return $query['v'];
            }
        }

        $host = strtolower($parts['host'] ?? '');
        $path = trim($parts['path'] ?? '', '/');

        if ($path !== '') {
            $segments = explode('/', $path);

            if (str_contains($host, 'youtu.be') && isset($segments[0]) && preg_match('/^[A-Za-z0-9_-]{11}$/', $segments[0])) {
                return $segments[0];
            }

            foreach (['embed', 'shorts', 'live'] as $prefix) {
                $index = array_search($prefix, $segments, true);

                if ($index !== false && isset($segments[$index + 1]) && preg_match('/^[A-Za-z0-9_-]{11}$/', $segments[$index + 1])) {
                    return $segments[$index + 1];
                }
            }
        }

        $patterns = [
            '/v=([A-Za-z0-9_-]{11})/',
            '/youtu\.be\/([A-Za-z0-9_-]{11})/',
            '/embed\/([A-Za-z0-9_-]{11})/',
            '/shorts\/([A-Za-z0-9_-]{11})/',
            '/live\/([A-Za-z0-9_-]{11})/',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $input, $matches)) {
                return $matches[1];
            }
        }

        return null;
    }

    protected function formatDuration(int $seconds): string
    {
        $hours = intdiv($seconds, 3600);
        $minutes = intdiv($seconds % 3600, 60);
        $remainingSeconds = $seconds % 60;

        $parts = [];

        if ($hours > 0) {
            $parts[] = trans_choice(
                'general.topic_player.sessions.duration.hours',
                $hours,
                ['count' => $hours]
            );
        }

        if ($minutes > 0 || $hours > 0) {
            $parts[] = trans_choice(
                'general.topic_player.sessions.duration.minutes',
                $minutes,
                ['count' => $minutes]
            );
        }

        $parts[] = trans_choice(
            'general.topic_player.sessions.duration.seconds',
            $remainingSeconds,
            ['count' => $remainingSeconds]
        );

        return implode(' ', $parts);
    }

    protected function canClockOut(VideoSession $session, ?Attendance $attendance): bool
    {
        if (! $attendance || $attendance->clock_out_at) {
            return false;
        }

        if (! $session->start_at || ! $session->end_at) {
            return false;
        }

        $clockOutWindowStart = $session->end_at->copy()->subMinutes(15);

        return now()->betweenIncluded($clockOutWindowStart, $session->end_at);
    }
}
