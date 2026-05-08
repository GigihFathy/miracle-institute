<?php

namespace App\Livewire\Topics;

use App\Models\Attendance;
use App\Models\Topic;
use App\Models\TopicProgress;
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

    public function mount(string $slug): void
    {
        $this->topic = Topic::with([
            'course',
            'materials',
            'videoSessions',
        ])->where('slug', $slug)->firstOrFail();

        $this->authorize('access', $this->topic);

        $this->activeMaterialId = $this->topic->materials->first()?->id;
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    public function selectMaterial(string $materialId): void
    {
        $this->activeMaterialId = $materialId;
        $this->activeTab = 'materials';
    }

    public function markViewed(ProgressService $progressService): void
    {
        if (!$this->activeMaterialId) {
            return;
        }

        $progressService->markMaterialViewed(auth()->id(), $this->activeMaterialId);
        session()->flash('success', 'Material marked as viewed.');
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
        $sessionAttendances = collect();

        $enrollment = auth()->user()
            ?->courseEnrollments()
            ->where('course_id', $this->topic->course_id)
            ->first();

        if ($enrollment) {
            $topicStatus = TopicProgress::where('course_enrollment_id', $enrollment->id)
                ->where('topic_id', $this->topic->id)
                ->value('status');

            if (auth()->check()) {
                $sessionAttendances = Attendance::where('user_id', auth()->id())
                    ->whereIn('video_session_id', $this->topic->videoSessions->pluck('id'))
                    ->get()
                    ->keyBy('video_session_id');
            }
        }

        $this->topicStatus = $topicStatus;

        $attendanceStats = [
            'present' => $sessionAttendances->where('status', 'present')->count(),
            'late' => $sessionAttendances->where('status', 'late')->count(),
            'absent' => $sessionAttendances->where('status', 'absent')->count(),
            'checked_in' => $sessionAttendances->whereIn('status', ['present', 'late'])->count(),
        ];

        return view('livewire.topics.topic-player', [
            'activeMaterial' => $activeMaterial,
            'materialUrl' => $materialUrl,
            'topicStatus' => $topicStatus,
            'sessionAttendances' => $sessionAttendances,
            'attendanceStats' => $attendanceStats,
        ])->layout('layouts.learning');
    }
}