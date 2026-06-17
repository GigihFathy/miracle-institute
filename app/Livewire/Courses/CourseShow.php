<?php

namespace App\Livewire\Courses;

use App\Livewire\Concerns\InteractsWithMentorTopic;
use App\Models\Assessment;
use App\Models\AssessmentAttempt;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\MaterialProgress;
use App\Models\Topic;
use App\Models\TopicProgress;
use App\Services\CourseService;
use App\Services\LearningAccessRequirementService;
use App\Services\ProgressService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;

class CourseShow extends Component
{
    use WithPagination;
    use AuthorizesRequests;
    use InteractsWithMentorTopic;

    public Course $course;

    public bool $enrolled = false;
    public bool $isGuest = true;

    public ?Certificate $courseCertificate = null;

    public array $topicStatusMap = [];
    public array $materialProgressMap = [];

    public ?Assessment $assessment = null;
    public bool $hasStudentFinishedAssessment = false;
    public ?array $assessmentMeta = null;

    public bool $showAssessmentModal = false;
    public bool $showEnrollModal = false;
    public bool $showTopicAccessWarningModal = false;
    public string $topicAccessWarningName = '';

    public string $topicSearch = '';
    public string $topicSort = 'sort_asc';
    public string $topicStatusFilter = 'all';
    public int $topicsPerPage = 6;

    public string $activeTopicTab = 'general';
    public ?string $selectedMentorTopicId = null;
    public ?string $selectedMentorMaterialId = null;
    public ?string $selectedMentorSessionId = null;
    public ?string $selectedStudentTopicId = null;
    public ?string $selectedStudentMaterialId = null;
    public ?string $selectedStudentSessionId = null;

    public Collection $mentoredTopics;
    public bool $hasMentoredTopics = false;
    public Collection $mentorStudents;

    protected $paginationTheme = 'tailwind';

    public function mount(string $slug): void
    {
        $this->mentoredTopics = collect();
        $this->mentorStudents = collect();

        $this->course = Course::query()
            ->with([
                'enrollments.user',
                'enrollments.topicProgresses',
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

                $this->hydrateStudentProgressMaps($enrollment->id, $user->id);
            }
        }

        $this->hydrateMentoredTopics();
        $this->hydrateMentorStudents();

        if ($this->mentoredTopics->isNotEmpty()) {
            $this->selectedMentorTopicId = $this->mentoredTopics->first()->id;
            $this->selectedMentorMaterialId = $this->mentoredTopics->first()?->materials->sortBy('sort_order')->first()?->id;
            $this->selectedMentorSessionId = $this->resolveLatestSessionId($this->mentoredTopics->first());
        }

        $studentTopics = $this->studentTopics();

        if ($studentTopics->isNotEmpty()) {
            $this->selectedStudentTopicId = $studentTopics->first()->id;
            $this->selectedStudentMaterialId = $studentTopics->first()?->materials->sortBy('sort_order')->first()?->id;
            $this->selectedStudentSessionId = $this->resolveLatestSessionId($studentTopics->first());
        }

        $requestedTab = request()->query('tab');
        $requestedTopicId = request()->query('topic');
        $requestedSessionId = request()->query('session');

        if (auth()->check() && session('active_role') === 'disciples') {
            $this->activeTopicTab = 'overview';
        } elseif (auth()->check() && session('active_role') === 'student') {
            $this->activeTopicTab = 'overview';
        }

        if (is_string($requestedTab) && $requestedTab !== '') {
            $this->setTopicTab($requestedTab);
        }

        if (is_string($requestedTopicId) && $requestedTopicId !== '') {
            if (auth()->check() && session('active_role') === 'disciples') {
                $this->selectMentorTopic($requestedTopicId);
            } else {
                $this->selectStudentTopic($requestedTopicId);
            }
        }

        if (is_string($requestedSessionId) && $requestedSessionId !== '') {
            if (auth()->check() && session('active_role') === 'disciples') {
                $this->selectMentorSession($requestedSessionId);
            } else {
                $this->selectStudentSession($requestedSessionId);
            }
        }
    }

    public function setTopicTab(string $tab): void
    {
        $allowedTabs = auth()->check() && session('active_role') === 'disciples'
            ? ['overview', 'topics', 'students']
            : (auth()->check() && session('active_role') === 'student'
                ? ['overview', 'topics']
                : ['general', 'mentored']);

        if (!in_array($tab, $allowedTabs, true)) {
            return;
        }

        $this->activeTopicTab = $tab;
        $this->resetPage('topicsPage');
    }

    public function hydrateMentoredTopics(): void
    {
        $this->mentoredTopics = collect();
        $this->hasMentoredTopics = false;
        $this->selectedMentorTopicId = null;
        $this->selectedMentorMaterialId = null;

        if (!auth()->check() || session('active_role') !== 'disciples') {
            return;
        }

        $userId = auth()->id();

        $ownedTopics = $this->course->topics
            ->filter(fn (Topic $topic) => (string) $topic->teacher_id === (string) $userId)
            ->map(function (Topic $topic) {
                $topic->setAttribute('mentor_role', 'owner');
                $topic->setAttribute('can_manage_assessment', true);

                return $topic;
            });

        $this->mentoredTopics = $ownedTopics
            ->unique('id')
            ->sortBy(fn (Topic $topic) => [$topic->sort_order, $topic->name])
            ->values();

        $this->hasMentoredTopics = $this->mentoredTopics->isNotEmpty();

        if ($this->hasMentoredTopics) {
            $this->selectedMentorTopicId = $this->mentoredTopics->first()->id;
            $this->selectedMentorMaterialId = $this->mentoredTopics->first()?->materials->sortBy('sort_order')->first()?->id;
            $this->selectedMentorSessionId = $this->resolveLatestSessionId($this->mentoredTopics->first());
        }
    }

    public function selectMentorTopic(string $topicId): void
    {
        if ($this->mentoredTopics->contains(fn (Topic $topic) => (string) $topic->id === (string) $topicId)) {
            $this->selectedMentorTopicId = $topicId;
            $topic = $this->mentoredTopics->firstWhere('id', $topicId);
            $this->selectedMentorMaterialId = $topic?->materials->sortBy('sort_order')->first()?->id;
            $this->selectedMentorSessionId = $this->resolveLatestSessionId($topic);
        }
    }

    public function selectMentorSession(string $sessionId): void
    {
        $topic = $this->mentoredTopics->first(function (Topic $topic) use ($sessionId) {
            return $topic->videoSessions->contains(fn ($session) => (string) $session->id === (string) $sessionId);
        });

        if (! $topic) {
            return;
        }

        $this->selectedMentorTopicId = $topic->id;
        $this->selectedMentorMaterialId = $topic->materials->sortBy('sort_order')->first()?->id;
        $this->selectedMentorSessionId = $sessionId;
    }

    public function selectMentorMaterial(string $materialId): void
    {
        $topic = $this->mentoredTopics->firstWhere('id', $this->selectedMentorTopicId);

        if ($topic && $topic->materials->contains(fn ($material) => (string) $material->id === (string) $materialId)) {
            $this->selectedMentorMaterialId = $materialId;
        }
    }

    public function selectStudentTopic(string $topicId): void
    {
        $studentTopics = $this->studentTopics();

        if ($studentTopics->contains(fn (Topic $topic) => (string) $topic->id === (string) $topicId)) {
            $this->selectedStudentTopicId = $topicId;
            $topic = $studentTopics->firstWhere('id', $topicId);
            $this->selectedStudentMaterialId = $topic?->materials->sortBy('sort_order')->first()?->id;
            $this->selectedStudentSessionId = $this->resolveLatestSessionId($topic);
        }
    }

    public function selectStudentSession(string $sessionId): void
    {
        $topic = $this->studentTopics()->first(function (Topic $topic) use ($sessionId) {
            return $topic->videoSessions->contains(fn ($session) => (string) $session->id === (string) $sessionId);
        });

        if (! $topic) {
            return;
        }

        $this->selectedStudentTopicId = $topic->id;
        $this->selectedStudentMaterialId = $topic->materials->sortBy('sort_order')->first()?->id;
        $this->selectedStudentSessionId = $sessionId;
    }

    public function selectStudentMaterial(string $materialId): void
    {
        $topic = $this->studentTopics()->firstWhere('id', $this->selectedStudentTopicId);

        if ($topic && $topic->materials->contains(fn ($material) => (string) $material->id === (string) $materialId)) {
            $this->selectedStudentMaterialId = $materialId;
        }
    }

    public function markStudentMaterialComplete(string $materialId, ProgressService $progressService): void
    {
        abort_unless(auth()->check() && session('active_role') === 'student', 403);

        if (! $this->enrolled) {
            session()->flash('error', 'Kamu harus terdaftar pada course ini terlebih dahulu.');
            return;
        }

        $topic = $this->studentTopics()->firstWhere('id', $this->selectedStudentTopicId);
        $material = $topic?->materials->firstWhere('id', $materialId);

        if (! $topic || ! $material) {
            session()->flash('error', 'Material tidak ditemukan.');
            return;
        }

        if (! $this->topicHasCompletedSessions($topic)) {
            session()->flash('error', 'Material baru bisa diselesaikan setelah sesi topic ini selesai.');
            return;
        }

        $result = $progressService->markMaterialCompleted((string) auth()->id(), $materialId);

        $enrollment = auth()->user()?->courseEnrollments()
            ->where('course_id', $this->course->id)
            ->first();

        if ($enrollment) {
            $this->hydrateStudentProgressMaps($enrollment->id, (string) auth()->id());
        }

        $this->courseCertificate = Certificate::query()
            ->where('user_id', auth()->id())
            ->where('course_id', $this->course->id)
            ->latest()
            ->first();

        session()->flash(
            'success',
            $result['snapshot']['can_complete']
                ? 'Material selesai. Topik ini juga dinyatakan completed.'
                : 'Material berhasil diselesaikan.'
        );
        $this->dispatch('toast', type: 'success', message: session('success'));
    }

    public function hydrateMentorStudents(): void
    {
        $this->mentorStudents = collect();

        if (!auth()->check() || session('active_role') !== 'disciples') {
            return;
        }

        $this->mentorStudents = $this->course->enrollments
            ->filter(fn (CourseEnrollment $enrollment) => $enrollment->user !== null)
            ->unique('user_id')
            ->sortBy(fn (CourseEnrollment $enrollment) => Str::lower($enrollment->user?->name ?? ''))
            ->values();
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
        $topics = $this->studentTopics()->map(function (Topic $topic) {
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
        return $this->studentTopics()->count()
            - $this->completedTopicsCount
            - $this->inProgressTopicsCount;
    }

    public function getAssessmentUnlockedProperty(): bool
    {
        if (!$this->assessment || !$this->enrolled) {
            return false;
        }

        if ($this->studentTopics()->isEmpty()) {
            return false;
        }

        return $this->studentTopics()->every(function ($topic) {
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

        $hasTopics = $this->studentTopics()->isNotEmpty();
        $hasAssessmentQuestions = app(LearningAccessRequirementService::class)
            ->courseHasAssessmentQuestions($this->course);
        $checks[] = [
            'label' => 'Course has topics',
            'done' => $hasTopics,
            'note' => $hasTopics ? 'Topics available' : 'No topic yet',
        ];

        $checks[] = [
            'label' => 'Course has questions',
            'done' => $hasAssessmentQuestions,
            'note' => $hasAssessmentQuestions ? 'Questions available' : 'Add at least one question first',
        ];

        $allTopicsCompleted = $hasTopics && $this->completedTopicsCount === $this->studentTopics()->count();

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
            && $hasAssessmentQuestions
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

        if (! $hasAssessmentQuestions) {
            $reasons[] = 'Sertifikat belum tersedia karena course ini belum memiliki soal.';
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

    public function confirmEnroll(): void
    {
        if (!auth()->check()) {
            $this->redirectRoute('login');

            return;
        }

        $this->closeTopicAccessWarning();
        $this->showEnrollModal = true;
    }

    public function closeEnrollModal(): void
    {
        $this->showEnrollModal = false;
    }

    public function openTopicAccessWarning(string $topicName): void
    {
        $this->topicAccessWarningName = $topicName;
        $this->showTopicAccessWarningModal = true;
    }

    public function closeTopicAccessWarning(): void
    {
        $this->showTopicAccessWarningModal = false;
        $this->topicAccessWarningName = '';
    }

    public function clearTopicFilters(): void
    {
        $this->reset(['topicSearch', 'topicSort', 'topicStatusFilter']);
        $this->topicSort = 'sort_asc';
        $this->topicStatusFilter = 'all';
        $this->resetPage('topicsPage');
    }

    public function updatedTopicSearch(): void
    {
        $this->resetPage('topicsPage');
    }

    public function updatedTopicSort(): void
    {
        $this->resetPage('topicsPage');
    }

    public function updatedTopicStatusFilter(): void
    {
        $this->resetPage('topicsPage');
    }

    public function getPaginatedTopicsProperty(): LengthAwarePaginator
    {
        $isMentorWorkspace = auth()->check() && session('active_role') === 'disciples';

        $topics = ($isMentorWorkspace && $this->activeTopicTab === 'topics') || $this->activeTopicTab === 'mentored'
            ? $this->mentoredTopics->values()
            : $this->filteredTopics;

        $pageName = 'topicsPage';
        $currentPage = $this->getPage($pageName);
        $total = $topics->count();
        $items = $topics->forPage($currentPage, $this->topicsPerPage)->values();

        return new LengthAwarePaginator(
            $items,
            $total,
            $this->topicsPerPage,
            $currentPage,
            [
                'path' => request()->url(),
                'pageName' => $pageName,
            ]
        );
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
            $this->closeEnrollModal();

            $enrollment = auth()->user()->courseEnrollments()
                ->where('course_id', $this->course->id)
                ->first();

            if ($enrollment) {
                $this->hydrateStudentProgressMaps($enrollment->id, (string) auth()->id());
            }

            session()->flash('success', 'Course berhasil diikuti.');
            $this->dispatch('toast', type: 'success', message: 'Course berhasil diikuti.');
        } catch (\Throwable $e) {
            session()->flash('error', $e->getMessage());
            $this->dispatch('toast', type: 'error', message: $e->getMessage());
        }
    }

    public function render()
    {
        $activeTab = $this->activeTopicTab;

        if (auth()->check() && session('active_role') === 'disciples') {
            if (!in_array($activeTab, ['overview', 'topics', 'students'], true)) {
                $activeTab = 'overview';
            }
        } elseif (auth()->check() && session('active_role') === 'student') {
            if (!in_array($activeTab, ['overview', 'topics'], true)) {
                $activeTab = 'overview';
            }
        } elseif ($activeTab === 'mentored' && (!auth()->check() || session('active_role') !== 'disciples')) {
            $activeTab = 'general';
        }

        return view('livewire.courses.course-show', [
            'filteredTopics' => $this->filteredTopics,
            'paginatedTopics' => $this->paginatedTopics,
            'studentTopics' => $this->studentTopics(),
            'assessment' => $this->assessment,
            'assessmentMeta' => $this->assessmentMeta,
            'certificateEligibility' => $this->certificateEligibility,
            'mentoredTopics' => $this->mentoredTopics,
            'hasMentoredTopics' => $this->hasMentoredTopics,
            'mentorStudents' => $this->mentorStudents,
            'activeTab' => $activeTab,
        ])->layout('layouts.learning');
    }

    private function hydrateStudentProgressMaps(string $enrollmentId, string $userId): void
    {
        $this->topicStatusMap = TopicProgress::query()
            ->where('course_enrollment_id', $enrollmentId)
            ->pluck('status', 'topic_id')
            ->toArray();

        $materialIds = $this->studentTopics()
            ->flatMap(fn (Topic $topic) => $topic->materials->pluck('id'))
            ->filter()
            ->values();

        $this->materialProgressMap = MaterialProgress::query()
            ->where('user_id', $userId)
            ->whereIn('material_id', $materialIds)
            ->pluck('status', 'material_id')
            ->toArray();
    }

    private function topicIsVisibleToStudents(Topic $topic): bool
    {
        $requirements = app(LearningAccessRequirementService::class);

        return $requirements->topicIsPublished($topic)
            && $requirements->topicHasStudentAccessRequirements($topic);
    }

    private function studentTopics(): Collection
    {
        if (auth()->check() && session('active_role') === 'student') {
            return $this->course->topics
                ->filter(fn (Topic $topic) => $this->topicIsVisibleToStudents($topic))
                ->values();
        }

        return $this->course->topics;
    }

    private function resolveLatestSessionId(?Topic $topic): ?string
    {
        return $topic?->videoSessions
            ->sortByDesc('start_at')
            ->first()?->id;
    }
}
