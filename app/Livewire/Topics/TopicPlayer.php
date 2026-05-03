<?php

namespace App\Livewire\Topics;

use App\Models\Certificate;
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

    public ?Certificate $topicCertificate = null;
    public ?array $assessmentMeta = null;

    public ?\App\Models\Assessment $activeAssessment = null;

    public function mount(string $slug)
    {
        $this->topic = Topic::with([
            'course',
            'materials',
            'sessions',
            'assessments.questions.options',
        ])->where('slug', $slug)->firstOrFail();

        $this->authorize('access', $this->topic);

        $this->activeMaterialId = $this->topic->materials->first()?->id;

        $this->topicCertificate = Certificate::where('user_id', auth()->id())
            ->where('type', 'topic')
            ->where('topic_id', $this->topic->id)
            ->latest()
            ->first();

        $assessment = $this->topic->assessments->first();

        if ($assessment) {
            $estimatedMinutes = $assessment->time_limit_minutes
                ?: max(5, ($assessment->questions->count() ?: 0) * 2);

            $this->assessmentMeta = [
                'title' => $assessment->title,
                'passing_grade' => $assessment->passing_grade,
                'time_limit_minutes' => $assessment->time_limit_minutes,
                'estimated_minutes' => $estimatedMinutes,
                'question_count' => $assessment->questions->count(),
                'start_date' => $assessment->created_at?->format('d M Y'),
                'instructions' => [
                    'Baca setiap soal dengan teliti sebelum menjawab.',
                    'Gunakan waktu secara efisien karena timer berjalan otomatis.',
                    'Jawaban isian harus sesuai ejaan yang benar.',
                    'Klik Submit hanya setelah kamu yakin.',
                ],
            ];
        }

        $this->activeAssessment = $this->topic->assessments->first();
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
        if (! $this->activeMaterialId) {
            return;
        }

        $progressService->markMaterialViewed(auth()->id(), $this->activeMaterialId);
        session()->flash('success', 'Material marked as viewed.');
    }

    public function getActiveAttemptProperty()
    {
        return \App\Models\AssessmentAttempt::where('assessment_id', $this->activeAssessment?->id)
            ->where('user_id', auth()->id())
            ->whereNull('submitted_at')
            ->first();
    }

    public function render()
    {
        $activeMaterial = $this->topic->materials->firstWhere('id', $this->activeMaterialId);

        $materialUrl = null;
        if ($activeMaterial) {
            $materialUrl = $activeMaterial->external_url ?: (
                $activeMaterial->path ? Storage::disk('public')->url($activeMaterial->path) : null
            );
        }

        $activeAssessment = $this->topic->assessments->first();

        $topicStatus = null;
        $enrollment = auth()->user()
            ->courseEnrollments()
            ->where('course_id', $this->topic->course_id)
            ->first();

        if ($enrollment) {
            $topicStatus = TopicProgress::where('course_enrollment_id', $enrollment->id)
                ->where('topic_id', $this->topic->id)
                ->value('status');
        }

        return view('livewire.topics.topic-player', [
            'activeMaterial' => $activeMaterial,
            'materialUrl' => $materialUrl,
            'topicStatus' => $topicStatus,
        ])->layout('layouts.learning');
    }
}