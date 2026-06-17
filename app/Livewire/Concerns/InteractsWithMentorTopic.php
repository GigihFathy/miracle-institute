<?php

namespace App\Livewire\Concerns;

use App\Models\Topic;
use App\Models\TopicUser;

trait InteractsWithMentorTopic
{
    protected function loadTopic(string|int $topicId): Topic
    {
        return Topic::query()
            ->with([
                'course.studyProgram',
                'course.assessment',
            ])
            ->findOrFail($topicId);
    }

    public function hasWorkspacePermission(Topic $topic, string $permission): bool
    {
        if ((string) $topic->teacher_id === (string) auth()->id()) {
            return true;
        }

        $topicUser = $topic->collaborators()
            ->with('permissions')
            ->where('user_id', auth()->id())
            ->where('status', 'active')
            ->first();

        if (!$topicUser) {
            return false;
        }

        return $topicUser->permissions
            ->pluck('permission')
            ->contains($permission);
    }

    public function workspacePermissionLabels(): array
    {
        return [
            'manage_topics' => 'Manage Topic',
            'manage_materials' => 'Manage Materials',
            'manage_sessions' => 'Manage Sessions',
            'manage_assessments' => 'Manage Assessments',
            'manage_attendance' => 'Manage Attendance',
            'manage_students' => 'Manage Students',
            'publish_topics' => 'Publish Topic',
            'view_reports' => 'View Reports',
            'manage_certificates' => 'Manage Certificates',
        ];
    }

    protected function permissionLabels(): array
    {
        return [
            'manage_topics' => 'Kelola Topik',
            'manage_materials' => 'Kelola Materi',
            'manage_sessions' => 'Kelola Sesi',
            'manage_assessments' => 'Kelola Assessment',
            'manage_attendance' => 'Kelola Absensi',
            'manage_students' => 'Kelola Murid',
            'publish_topics' => 'Publikasi Topik',
            'view_reports' => 'Lihat Laporan',
            'manage_certificates' => 'Kelola Sertifikat',
        ];
    }

    protected function workspacePermissions(): array
    {
        return array_keys($this->permissionLabels());
    }

    protected function topicMembership(Topic $topic): ?TopicUser
    {
        return TopicUser::query()
            ->with('permissions')
            ->where('topic_id', $topic->id)
            ->where('user_id', auth()->id())
            ->where('status', 'active')
            ->first();
    }

    public function canAccessFeature(Topic $topic, string $permission): bool
    {
        return $this->hasWorkspacePermission($topic, $permission);
    }

    protected function canAccessTopic(Topic $topic, array $permissions = []): bool
    {
        $user = auth()->user();

        if ($user->hasRole('admin')) {
            return true;
        }

        if ((string) $topic->teacher_id === (string) $user->id) {
            return true;
        }

        $membership = $this->topicMembership($topic);

        if (!$membership) {
            return false;
        }

        if (empty($permissions)) {
            return true;
        }

        return $membership->permissions
            ->pluck('permission')
            ->intersect($permissions)
            ->isNotEmpty();
    }

    protected function canManageTopic(Topic $topic): bool
    {
        return $this->canAccessTopic($topic, [
            'manage_topics',
            'manage_materials',
            'manage_sessions',
            'manage_assessments',
            'manage_attendance',
            'manage_students',
            'view_reports',
            'manage_certificates',
        ]);
    }

    protected function canManageCollaborators(Topic $topic): bool
    {
        $user = auth()->user();

        if ($user->hasRole('admin')) {
            return true;
        }

        return (string) $topic->teacher_id === (string) $user->id;
    }
}
