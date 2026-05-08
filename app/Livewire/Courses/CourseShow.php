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

class CourseShow extends Component
{
    use AuthorizesRequests;

    public Course $course;

    public bool $enrolled = false;
    public bool $isGuest = true;
    public bool $showAssessmentModal = false;

    public ?Certificate $courseCertificate = null;
    public array $topicStatusMap = [];

    public ?Assessment $assessment = null;
    public ?array $assessmentMeta = null;

    public string $topicSearch = '';
    public string $topicSort = 'sort_asc';
    public string $topicStatusFilter = 'all';

    public function mount(string $slug): void
    {
        $this->course = Course::with([
            'studyProgram',
            'topics' => fn ($q) => $q->withCount(['materials', 'videoSessions'])
                ->orderBy('sort_order')
                ->orderBy('name'),
            'assessment.questions.options',
        ])->where('slug', $slug)->firstOrFail();

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

        $this->isGuest = false;

        $user = auth()->user();
        $enrollment = $user->courseEnrollments()
            ->where('course_id', $this->course->id)
            ->first();

        $this->enrolled = (bool) $enrollment;

        if ($this->enrolled) {
            $this->courseCertificate = Certificate::where('user_id', $user->id)
                ->where('type', 'course')
                ->where('course_id', $this->course->id)
                ->latest()
                ->first();

            $this->topicStatusMap = TopicProgress::where('course_enrollment_id', $enrollment->id)
                ->pluck('status', 'topic_id')
                ->toArray();
        }
    }

    public function getIsMentorProperty(): bool
    {
        return auth()->check() && session('active_role') === 'disciples';
    }

    public function getIsStudentProperty(): bool
    {
        return auth()->check() && ! $this->isMentor;
    }

    public function getCanUseStudentFeaturesProperty(): bool
    {
        return $this->isStudent && $this->enrolled;
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
        return max(0, $this->course->topics->count() - $this->completedTopicsCount - $this->inProgressTopicsCount);
    }

    public function getOverallProgressProperty(): int
    {
        $total = $this->course->topics->count();

        return $total > 0
            ? (int) round(($this->completedTopicsCount / $total) * 100)
            : 0;
    }

    public function getAssessmentUnlockedProperty(): bool
    {
        if (! $this->assessment || ! $this->enrolled) {
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
        if (! auth()->check() || ! $this->assessment) {
            return null;
        }

        return AssessmentAttempt::where('assessment_id', $this->assessment->id)
            ->where('user_id', auth()->id())
            ->whereNull('submitted_at')
            ->first();
    }

    public function getHasPassedAssessmentProperty(): bool
    {
        if (! auth()->check() || ! $this->assessment) {
            return false;
        }

        return AssessmentAttempt::where('assessment_id', $this->assessment->id)
            ->where('user_id', auth()->id())
            ->whereNotNull('submitted_at')
            ->where('passed', true)
            ->exists();
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

    public function getGuestPreviewTopicsProperty()
    {
        return $this->course->topics->take(3)->map(function ($topic) {
            return [
                'name' => $topic->name,
                'slug' => $topic->slug,
                'description' => $topic->description,
                'materials_count' => $topic->materials_count,
                'video_sessions_count' => $topic->video_sessions_count,
                'preview_materials' => $topic->materials->take(2)->map(function ($material) {
                    return [
                        'name' => $material->name,
                        'type' => $material->type,
                    ];
                })->values(),
            ];
        });
    }

    public function getCertificateEligibilityProperty(): array
    {
        $checks = [];
        $reasons = [];

        if ($this->isGuest) {
            return [
                'eligible' => false,
                'has_certificate' => false,
                'checks' => [],
                'reasons' => ['Login untuk memeriksa dan mengakses sertifikat.'],
            ];
        }

        if ($this->isMentor) {
            return [
                'eligible' => false,
                'has_certificate' => (bool) $this->courseCertificate,
                'checks' => [],
                'reasons' => ['Mentor mode bersifat manajerial dan tidak menggunakan claim certificate.'],
            ];
        }

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
            && ! $this->courseCertificate;

        if (! auth()->check()) {
            $reasons[] = 'Silakan login untuk memeriksa sertifikat.';
        }

        if (auth()->check() && ! $this->enrolled) {
            $reasons[] = 'Sertifikat hanya tersedia untuk peserta yang sudah enroll.';
        }

        if (! $hasTopics) {
            $reasons[] = 'Course ini belum memiliki topic.';
        }

        if ($hasTopics && ! $allTopicsCompleted) {
            $reasons[] = 'Selesaikan seluruh topic untuk membuka sertifikat.';
        }

        if ($this->assessment && ! $assessmentOk) {
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

    private function buildAssessmentMeta(): void
    {
        if (! $this->assessment) {
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

    public function clearTopicFilters(): void
    {
        $this->reset(['topicSearch', 'topicSort', 'topicStatusFilter']);
        $this->topicSort = 'sort_asc';
        $this->topicStatusFilter = 'all';
    }

    public function openAssessmentModal(): void
    {
        if (! $this->assessment) {
            return;
        }

        $this->showAssessmentModal = true;
    }

    public function closeAssessmentModal(): void
    {
        $this->showAssessmentModal = false;
    }

    public function enroll(CourseService $courseService)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        if ($this->isMentor) {
            session()->flash('error', 'Mentor tidak menggunakan fitur enroll.');
            return null;
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
        return view('livewire.courses.course-show', [
            'filteredTopics' => $this->filteredTopics,
            'guestPreviewTopics' => $this->guestPreviewTopics,
            'assessmentMeta' => $this->assessmentMeta,
            'certificateEligibility' => $this->certificateEligibility,
        ])->layout('layouts.learning');
    }
}