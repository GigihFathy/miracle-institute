<?php

namespace App\Livewire\Courses;

use App\Models\Assessment;
use App\Models\AssessmentAttempt;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\Topic;
use App\Models\TopicProgress;
use App\Models\TopicUser;
use App\Services\CourseService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Livewire\Component;

class CourseShow extends Component
{
    use AuthorizesRequests;

    public Course $course;

    public bool $enrolled = false;
    public bool $isGuest = true;

    public ?Certificate $courseCertificate = null;

    public array $topicStatusMap = [];

    public ?Assessment $assessment = null;
    public bool $hasStudentFinishedAssessment = false;
    public ?array $assessmentMeta = null;

    public bool $showAssessmentModal = false;

    public string $topicSearch = '';
    public string $topicSort = 'sort_asc';
    public string $topicStatusFilter = 'all';

    public string $activeTopicTab = 'general';

    public Collection $mentoredTopics;
    public bool $hasMentoredTopics = false;

    public function mount(string $slug): void
    {
        $this->mentoredTopics = collect();

        $this->course = Course::query()
            ->with([
                'studyProgram',
                'topics' => function ($q) {
                    $q->with([
                        'materials',
                        'videoSessions',
                    ])
                        ->withCount([
                            'materials',
                            'videoSessions',
                        ])
                        ->orderBy('sort_order')
                        ->orderBy('name');
                },
                'assessment.questions.options',
            ])
            ->where('slug', $slug)
            ->firstOrFail();

        $this->assessment = $this->course->assessment && $this->course->assessment->status === 'active'
            ? $this->course->assessment
            : null;
        
        $this->hasStudentFinishedAssessment = $this->assessment
            ? AssessmentAttempt::query()
                ->where('assessment_id', $this->assessment->id)
                ->where('user_id', auth()->id())
                ->whereNotNull('submitted_at')
                ->where('passed', true)
                ->exists()
            : false;

        $this->buildAssessmentMeta();

        $this->isGuest = !auth()->check();

        if (!$this->isGuest) {
            $user = auth()->user();

            $enrollment = $user->courseEnrollments()
                ->where('course_id', $this->course->id)
                ->first();

            $this->enrolled = (bool) $enrollment;

            if ($this->enrolled) {
                $this->courseCertificate = Certificate::query()
                    ->where('user_id', $user->id)
                    ->where('course_id', $this->course->id)
                    ->latest()
                    ->first();

                $this->topicStatusMap = TopicProgress::query()
                    ->where('course_enrollment_id', $enrollment->id)
                    ->pluck('status', 'topic_id')
                    ->toArray();
            }
        }

        $this->hydrateMentoredTopics();
    }

    public function setTopicTab(string $tab): void
    {
        if (!in_array($tab, ['general', 'mentored'], true)) {
            return;
        }

        $this->activeTopicTab = $tab;
    }

    public function hydrateMentoredTopics(): void
    {
        $this->mentoredTopics = collect();
        $this->hasMentoredTopics = false;

        if (!auth()->check() || session('active_role') !== 'disciples') {
            return;
        }

        $userId = auth()->id();

        $ownedTopics = $this->course->topics
            ->filter(fn (Topic $topic) => (string) $topic->teacher_id === (string) $userId)
            ->map(function (Topic $topic) {
                $topic->setAttribute('mentor_role', 'owner');

                return $topic;
            });

        $collaboratorTopicIds = TopicUser::query()
            ->whereIn('topic_id', $this->course->topics->pluck('id')->all())
            ->where('user_id', $userId)
            ->where('status', 'active')
            ->where('role_type', 'collaborator')
            ->pluck('topic_id')
            ->all();

        $collaboratingTopics = $this->course->topics
            ->filter(fn (Topic $topic) => in_array($topic->id, $collaboratorTopicIds, true) && (string) $topic->teacher_id !== (string) $userId)
            ->map(function (Topic $topic) {
                $topic->setAttribute('mentor_role', 'collaborator');

                return $topic;
            });

        $this->mentoredTopics = $ownedTopics
            ->concat($collaboratingTopics)
            ->unique('id')
            ->sortBy(fn (Topic $topic) => [$topic->sort_order, $topic->name])
            ->values();

        $this->hasMentoredTopics = $this->mentoredTopics->isNotEmpty();
    }

    private function buildAssessmentMeta(): void
    {
        if (!$this->assessment) {
            $this->assessmentMeta = null;

            return;
        }

        $questionCount = $this->assessment->questions->count();

        $this->assessmentMeta = [
            'title' => $this->assessment->title,
            'passing_grade' => $this->assessment->passing_grade,
            'question_count' => $questionCount,
            'status' => $this->assessment->status,
            'instructions' => [
                'Baca setiap soal dengan teliti sebelum menjawab.',
                'Gunakan waktu secara efisien karena timer berjalan otomatis.',
                'Jawaban isian harus sesuai ejaan yang benar.',
                'Klik Submit hanya setelah kamu yakin.',
            ],
        ];
    }

    private function topicHasCompletedSessions(Topic $topic): bool
    {
        if ($topic->videoSessions->isEmpty()) {
            return false;
        }

        return $topic->videoSessions->every(function ($session) {
            return $session->status === 'completed';
        });
    }

    private function topicSessionStatus(Topic $topic): string
    {
        if ($topic->videoSessions->isEmpty()) {
            return 'no_session';
        }

        if ($topic->videoSessions->every(fn ($session) => $session->status === 'completed')) {
            return 'completed';
        }

        if ($topic->videoSessions->contains(fn ($session) => $session->status === 'ongoing')) {
            return 'ongoing';
        }

        if ($topic->videoSessions->contains(fn ($session) => $session->status === 'scheduled')) {
            return 'scheduled';
        }

        return 'cancelled';
    }

    public function getFilteredTopicsProperty()
    {
        $topics = $this->course->topics->map(function (Topic $topic) {
            $status = $this->isGuest
                ? 'available'
                : ($this->topicStatusMap[$topic->id] ?? 'not_started');

            $percent = match ($status) {
                'completed' => 100,
                'in_progress' => 50,
                default => 0,
            };

            $topic->setAttribute('progress_status', $status);
            $topic->setAttribute('progress_percent', $percent);
            $topic->setAttribute('session_status', $this->topicSessionStatus($topic));
            $topic->setAttribute('materials_count', $topic->materials_count ?? $topic->materials->count());
            $topic->setAttribute('sessions_count', $topic->video_sessions_count ?? $topic->videoSessions->count());

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

        if (!$this->isGuest && $this->topicStatusFilter !== 'all') {
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
        return $this->course->topics->count()
            - $this->completedTopicsCount
            - $this->inProgressTopicsCount;
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

        return AssessmentAttempt::query()
            ->where('assessment_id', $this->assessment->id)
            ->where('user_id', auth()->id())
            ->whereNull('submitted_at')
            ->first();
    }

    public function getHasPassedAssessmentProperty(): bool
    {
        if (!auth()->check() || !$this->assessment) {
            return false;
        }

        return AssessmentAttempt::query()
            ->where('assessment_id', $this->assessment->id)
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

        $allTopicsCompleted = $hasTopics && $this->completedTopicsCount === $this->course->topics->count();

        $checks[] = [
            'label' => 'All topics completed',
            'done' => $allTopicsCompleted,
            'note' => $allTopicsCompleted ? 'All topics completed' : 'Finish remaining topics',
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
                $this->topicStatusMap = TopicProgress::query()
                    ->where('course_enrollment_id', $enrollment->id)
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
        $activeTab = $this->activeTopicTab;

        if ($activeTab === 'mentored' && (!auth()->check() || session('active_role') !== 'disciples')) {
            $activeTab = 'general';
        }

        return view('livewire.courses.course-show', [
            'filteredTopics' => $this->filteredTopics,
            'assessment' => $this->assessment,
            'assessmentMeta' => $this->assessmentMeta,
            'certificateEligibility' => $this->certificateEligibility,
            'mentoredTopics' => $this->mentoredTopics,
            'hasMentoredTopics' => $this->hasMentoredTopics,
            'activeTab' => $activeTab,
        ])->layout('layouts.learning');
    }
}