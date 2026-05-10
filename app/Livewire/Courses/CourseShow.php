<?php

namespace App\Livewire\Courses;

use App\Models\Assessment;
use App\Models\AssessmentAttempt;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\TopicProgress;
use App\Services\CourseService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Str;
use Livewire\Component;
use Illuminate\Support\Collection;

class CourseShow extends Component
{
    use AuthorizesRequests;

    public Course $course;

    public bool $enrolled = false;
    public bool $isGuest = true;

    public ?Certificate $courseCertificate = null;
    public array $topicStatusMap = [];

    public ?Assessment $assessment = null;
    public ?array $assessmentMeta = null;

    public bool $showAssessmentModal = false;

    public string $topicSearch = '';
    public string $topicSort = 'sort_asc';
    public string $topicStatusFilter = 'all';
    public Collection $guestPreviewTopics;

    private function loadGuestPreviewTopics(): void
    {
        $this->guestPreviewTopics = $this->course
            ->topics()
            ->with([
                'materials' => fn ($query) => $query
                    ->select('id', 'topic_id', 'name', 'type')
                    ->orderBy('sort_order'),

                'videoSessions',
            ])
            ->orderBy('sort_order')
            ->limit(6)
            ->get()
            ->map(function ($topic) {

                return [
                    'name' => $topic->name,
                    'description' => str($topic->description)
                        ->limit(140),

                    'materials_count' => $topic->materials->count(),

                    'video_sessions_count' => $topic->videoSessions->count(),

                    'preview_materials' => $topic->materials
                        ->take(3)
                        ->map(fn ($material) => [
                            'name' => $material->title,
                            'type' => $material->type,
                        ])
                        ->values(),
                ];
            });
    }

    public function mount(string $slug): void
    {
        

        $this->course = Course::with([
            'studyProgram',
            'topics' => fn ($q) => $q->withCount(['materials', 'videoSessions'])
                ->orderBy('sort_order')
                ->orderBy('name'),
            'assessment.questions.options',
        ])->where('slug', $slug)->firstOrFail();

        $this->guestPreviewTopics = collect();

        if (!auth()->check()) {
            $this->loadGuestPreviewTopics();
        }

        $this->assessment = $this->course->assessment && $this->course->assessment->status === 'active'
            ? $this->course->assessment
            : null;

        $this->buildAssessmentMeta();

        if (!auth()->check()) {
            $this->isGuest = true;
            $this->enrolled = false;
            $this->topicStatusMap = [];
            return;
        }

        $user = auth()->user();
        $this->isGuest = false;

        $enrollment = $user->courseEnrollments()
            ->where('course_id', $this->course->id)
            ->first();

        $this->enrolled = (bool) $enrollment;

        if (!$this->enrolled) {
            $this->topicStatusMap = [];
            return;
        }

        $this->courseCertificate = Certificate::where('user_id', $user->id)
            ->where('type', 'course')
            ->where('course_id', $this->course->id)
            ->latest()
            ->first();

        $this->topicStatusMap = TopicProgress::where('course_enrollment_id', $enrollment->id)
            ->pluck('status', 'topic_id')
            ->toArray();
    }

    private function buildAssessmentMeta(): void
    {
        if (!$this->assessment) {
            $this->assessmentMeta = null;
            return;
        }

        $questionCount = $this->assessment->questions->count();
        $estimatedMinutes = $this->assessment->time_limit_minutes
            ?: max(5, $questionCount * 2);

        $this->assessmentMeta = [
            'title' => $this->assessment->title,
            'passing_grade' => $this->assessment->passing_grade,
            'time_limit_minutes' => $this->assessment->time_limit_minutes,
            'estimated_minutes' => $estimatedMinutes,
            'question_count' => $questionCount,
            'start_date' => $this->assessment->created_at?->format('d M Y'),
            'status' => $this->assessment->status,
            'instructions' => [
                'Baca setiap soal dengan teliti sebelum menjawab.',
                'Gunakan waktu secara efisien karena timer berjalan otomatis.',
                'Jawaban isian harus sesuai ejaan yang benar.',
                'Klik Submit hanya setelah kamu yakin.',
            ],
        ];
    }

    public function getFilteredTopicsProperty()
    {
        $topics = $this->course->topics->map(function ($topic) {
            $status = $this->topicStatusMap[$topic->id] ?? 'not_started';
            $percent = match ($status) {
                'completed' => 100,
                'in_progress' => 50,
                default => 0,
            };

            $topic->setAttribute('progress_status', $status);
            $topic->setAttribute('progress_percent', $percent);

            return $topic;
        });

        if ($this->topicSearch !== '') {
            $search = Str::lower($this->topicSearch);

            $topics = $topics->filter(function ($topic) use ($search) {
                return Str::contains(Str::lower($topic->name), $search)
                    || Str::contains(Str::lower((string) $topic->description), $search)
                    || Str::contains(Str::lower((string) $topic->category), $search);
            });
        }

        if ($this->topicStatusFilter !== 'all') {
            $topics = $topics->filter(fn ($topic) => $topic->progress_status === $this->topicStatusFilter);
        }

        $topics = match ($this->topicSort) {
            'sort_desc' => $topics->sortByDesc(fn ($topic) => [$topic->sort_order, $topic->name]),
            'name_asc' => $topics->sortBy(fn ($topic) => Str::lower($topic->name)),
            'name_desc' => $topics->sortByDesc(fn ($topic) => Str::lower($topic->name)),
            'progress_desc' => $topics->sortByDesc('progress_percent'),
            'progress_asc' => $topics->sortBy('progress_percent'),
            default => $topics->sortBy(fn ($topic) => [$topic->sort_order, $topic->name]),
        };

        return $topics->values();
    }

    public function getCompletedTopicsCountProperty(): int
    {
        return collect($this->topicStatusMap)
            ->filter(fn ($status) => $status === 'completed')
            ->count();
    }

    public function getInProgressTopicsCountProperty(): int
    {
        return collect($this->topicStatusMap)
            ->filter(fn ($status) => $status === 'in_progress')
            ->count();
    }

    public function getNotStartedTopicsCountProperty(): int
    {
        return $this->course->topics->count() - $this->completedTopicsCount - $this->inProgressTopicsCount;
    }

    public function getAssessmentUnlockedProperty(): bool
    {
        if (!$this->assessment || !$this->enrolled) {
            return false;
        }

        if ($this->course->topics->isEmpty()) {
            return false;
        }

        return $this->course->topics->every(function ($topic) {
            return ($this->topicStatusMap[$topic->id] ?? null) === 'completed';
        });
    }

    public function getActiveAttemptProperty()
    {
        if (!auth()->check() || !$this->assessment) {
            return null;
        }

        return AssessmentAttempt::where('assessment_id', $this->assessment->id)
            ->where('user_id', auth()->id())
            ->whereNull('submitted_at')
            ->first();
    }

    public function getHasPassedAssessmentProperty(): bool
    {
        if (!auth()->check() || !$this->assessment) {
            return false;
        }

        return AssessmentAttempt::where('assessment_id', $this->assessment->id)
            ->where('user_id', auth()->id())
            ->whereNotNull('submitted_at')
            ->where('passed', true)
            ->exists();
    }

    public function getCertificateEligibilityProperty(): array
    {
        $checks = [];
        $reasons = [];

        $checks[] = [
            'label' => 'Logged in',
            'done' => auth()->check(),
            'note' => auth()->check() ? 'User authenticated' : 'Login required',
        ];

        $checks[] = [
            'label' => 'Enrolled',
            'done' => $this->enrolled,
            'note' => $this->enrolled ? 'Course enrolled' : 'Enroll course first',
        ];

        $hasTopics = $this->course->topics->isNotEmpty();
        $checks[] = [
            'label' => 'Course has topics',
            'done' => $hasTopics,
            'note' => $hasTopics ? 'Topics available' : 'No topic yet',
        ];

        $allTopicsCompleted = $hasTopics
            && $this->completedTopicsCount === $this->course->topics->count();

        $checks[] = [
            'label' => 'All topics completed',
            'done' => $allTopicsCompleted,
            'note' => $allTopicsCompleted
                ? 'All topics completed'
                : 'Finish remaining topics',
        ];

        $assessmentOk = true;

        if ($this->assessment) {
            $assessmentOk = $this->hasPassedAssessment;
            $checks[] = [
                'label' => 'Assessment passed',
                'done' => $assessmentOk,
                'note' => $assessmentOk ? 'Assessment passed' : 'Pass assessment first',
            ];
        }

        $eligible = auth()->check()
            && $this->enrolled
            && $hasTopics
            && $allTopicsCompleted
            && $assessmentOk
            && !$this->courseCertificate;

        if (!auth()->check()) {
            $reasons[] = 'Silakan login untuk memeriksa sertifikat.';
        }

        if (auth()->check() && !$this->enrolled) {
            $reasons[] = 'Sertifikat hanya tersedia untuk peserta yang sudah enroll.';
        }

        if (!$hasTopics) {
            $reasons[] = 'Course ini belum memiliki topic.';
        }

        if ($hasTopics && !$allTopicsCompleted) {
            $reasons[] = 'Selesaikan seluruh topic untuk membuka sertifikat.';
        }

        if ($this->assessment && !$assessmentOk) {
            $reasons[] = 'Lulus assessment course terlebih dahulu.';
        }

        if ($this->courseCertificate) {
            $reasons[] = 'Sertifikat sudah diterbitkan.';
        }

        return [
            'eligible' => $eligible,
            'has_certificate' => (bool) $this->courseCertificate,
            'checks' => $checks,
            'reasons' => array_values(array_unique($reasons)),
        ];
    }

    public function openAssessmentModal(): void
    {
        if (!$this->assessment) {
            return;
        }

        $this->showAssessmentModal = true;
    }

    public function closeAssessmentModal(): void
    {
        $this->showAssessmentModal = false;
    }

    public function clearTopicFilters(): void
    {
        $this->reset(['topicSearch', 'topicSort', 'topicStatusFilter']);
        $this->topicSort = 'sort_asc';
        $this->topicStatusFilter = 'all';
    }

    public function enroll(CourseService $courseService)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $this->authorize('enroll', $this->course);

        try {
            $courseService->enrollUser(auth()->id(), $this->course->id);
            $this->enrolled = true;

            $enrollment = auth()->user()->courseEnrollments()
                ->where('course_id', $this->course->id)
                ->first();

            if ($enrollment) {
                $this->topicStatusMap = TopicProgress::where('course_enrollment_id', $enrollment->id)
                    ->pluck('status', 'topic_id')
                    ->toArray();
            }

            session()->flash('success', 'Course berhasil diikuti.');
        } catch (\Throwable $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function render()
    {
        $activeMaterial = null;
        $materialUrl = null;

        return view('livewire.courses.course-show', [
            'activeMaterial' => $activeMaterial,
            'materialUrl' => $materialUrl,
            'filteredTopics' => $this->filteredTopics,
            'assessment' => $this->assessment,
            'assessmentMeta' => $this->assessmentMeta,
            'certificateEligibility' => $this->certificateEligibility,
        ])->layout('layouts.learning');
    }
}