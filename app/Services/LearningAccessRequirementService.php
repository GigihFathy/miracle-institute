<?php

namespace App\Services;

use App\Models\Course;
use App\Models\Topic;
use RuntimeException;

class LearningAccessRequirementService
{
    public function ensureTopicCanBePublished(Topic $topic): void
    {
        if (! $this->topicHasStudentAccessRequirements($topic)) {
            throw new RuntimeException(
                'Topik wajib memiliki minimal 1 sesi sebelum bisa diaktifkan dan diakses oleh murid.'
            );
        }
    }

    public function ensureCourseCanBeActivated(Course $course): void
    {
        if (! $this->courseHasAssessmentQuestions($course)) {
            throw new RuntimeException(
                'Course wajib memiliki minimal 1 soal sebelum bisa aktif atau sertifikatnya diunduh.'
            );
        }

        if (! $this->courseHasPublishedTopicWithMaterials($course)) {
            throw new RuntimeException(
                'Course wajib memiliki minimal 1 topik terbit yang berisi materi sebelum bisa aktif.'
            );
        }
    }

    public function ensureCourseCanIssueCertificate(Course $course): void
    {
        if (! $this->courseHasAssessmentQuestions($course)) {
            throw new RuntimeException(
                'Sertifikat course belum tersedia karena course ini belum memiliki soal.'
            );
        }

        $assessment = $course->assessment()->first();

        if ($assessment && $assessment->status === 'active' && $assessment->available_from && now()->lt($assessment->available_from)) {
            throw new RuntimeException(
                'Sertifikat belum dapat diklaim karena soal baru tersedia mulai ' . $assessment->available_from->format('d M Y H:i') . '.'
            );
        }
    }

    public function topicHasStudentAccessRequirements(Topic $topic): bool
    {
        return $topic->videoSessions()
            ->where('status', '!=', 'draft')
            ->exists();
    }

    public function topicIsPublished(Topic $topic): bool
    {
        return in_array($topic->status, ['published', 'active'], true);
    }

    public function courseHasAssessmentQuestions(Course $course): bool
    {
        return $course->assessment()
            ->whereHas('questions')
            ->exists();
    }

    public function courseHasPublishedTopicWithMaterials(Course $course): bool
    {
        return $course->topics()
            ->whereIn('status', ['published', 'active'])
            ->whereHas('materials')
            ->exists();
    }
}
