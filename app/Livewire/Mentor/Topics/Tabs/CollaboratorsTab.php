<?php

namespace App\Livewire\Mentor\Topics\Tabs;

use App\Livewire\Concerns\InteractsWithMentorTopic;
use App\Models\Topic;
use App\Models\TopicUser;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class CollaboratorsTab extends Component
{
    use InteractsWithMentorTopic;

    public Topic $topic;

    public bool $showCollaboratorModal = false;
    public ?string $editingCollaboratorId = null;

    public string $collaboratorSearch = '';
    public ?string $collaboratorUserId = null;
    public array $collaboratorPermissions = [];
    public string $collaboratorStatus = 'active';

    public function mount(string $topicId): void
    {
        $this->topic = $this->loadTopic($topicId);

        abort_unless($this->canManageCollaborators($this->topic), 403);
    }

    public function openCollaboratorModal(): void
    {
        abort_unless($this->canManageCollaborators($this->topic), 403);

        $this->resetCollaboratorForm();
        $this->showCollaboratorModal = true;
    }

    public function editCollaborator(string $topicUserId): void
    {
        abort_unless($this->canManageCollaborators($this->topic), 403);

        $topicUser = TopicUser::query()
            ->with(['user', 'permissions'])
            ->where('topic_id', $this->topic->id)
            ->findOrFail($topicUserId);

        $this->editingCollaboratorId = $topicUser->id;
        $this->collaboratorUserId = $topicUser->user_id;
        $this->collaboratorPermissions = $topicUser->permissions->pluck('permission')->values()->all();
        $this->collaboratorStatus = $topicUser->status;
        $this->showCollaboratorModal = true;
    }

    public function closeCollaboratorModal(): void
    {
        $this->showCollaboratorModal = false;
    }

    private function isEligibleMentor(User $user): bool
    {
        return $user->hasRole('disciples') && ! $user->hasRole('student');
    }

    public function saveCollaborator(): void
    {
        abort_unless($this->canManageCollaborators($this->topic), 403);

        $this->validate([
            'collaboratorUserId' => ['required', Rule::exists('users', 'id')],
            'collaboratorPermissions' => ['required', 'array', 'min:1'],
            'collaboratorPermissions.*' => [Rule::in($this->workspacePermissions())],
            'collaboratorStatus' => ['required', Rule::in(['active', 'inactive'])],
        ]);

        $user = User::query()->findOrFail($this->collaboratorUserId);

        if (! $this->isEligibleMentor($user)) {
            throw ValidationException::withMessages([
                'collaboratorUserId' => 'Hanya akun Mentor/Disciples yang dapat menjadi collaborator.',
            ]);
        }

        if ((string) $this->collaboratorUserId === (string) $this->topic->teacher_id) {
            throw ValidationException::withMessages([
                'collaboratorUserId' => 'Owner tidak perlu dijadikan collaborator.',
            ]);
        }

        DB::transaction(function () {
            $topicUser = $this->editingCollaboratorId
                ? TopicUser::query()->where('topic_id', $this->topic->id)->findOrFail($this->editingCollaboratorId)
                : TopicUser::query()
                    ->where('topic_id', $this->topic->id)
                    ->where('user_id', $this->collaboratorUserId)
                    ->first();

            if (! $topicUser) {
                $topicUser = TopicUser::create([
                    'topic_id' => $this->topic->id,
                    'user_id' => $this->collaboratorUserId,
                    'role_type' => 'collaborator',
                    'status' => $this->collaboratorStatus,
                    'invited_by' => auth()->id(),
                    'joined_at' => now(),
                ]);
            } else {
                $topicUser->update([
                    'status' => $this->collaboratorStatus,
                    'invited_by' => auth()->id(),
                    'joined_at' => $topicUser->joined_at ?? now(),
                ]);

                $topicUser->permissions()->delete();
            }

            foreach ($this->collaboratorPermissions as $permission) {
                $topicUser->permissions()->create([
                    'permission' => $permission,
                    'granted_by' => auth()->id(),
                ]);
            }
        });

        $this->closeCollaboratorModal();
        $this->resetCollaboratorForm();

        session()->flash('success', $this->editingCollaboratorId ? 'Collaborator berhasil diperbarui.' : 'Collaborator berhasil ditambahkan.');
    }

    public function removeCollaborator(string $topicUserId): void
    {
        abort_unless($this->canManageCollaborators($this->topic), 403);

        $topicUser = TopicUser::query()
            ->where('topic_id', $this->topic->id)
            ->where('role_type', 'collaborator')
            ->findOrFail($topicUserId);

        DB::transaction(function () use ($topicUser) {
            $topicUser->permissions()->delete();
            $topicUser->delete();
        });

        session()->flash('success', 'Collaborator berhasil dihapus.');
    }

    private function resetCollaboratorForm(): void
    {
        $this->reset([
            'editingCollaboratorId',
            'collaboratorSearch',
            'collaboratorUserId',
            'collaboratorPermissions',
            'collaboratorStatus',
        ]);

        $this->collaboratorStatus = 'active';
        $this->collaboratorPermissions = [
            'manage_topics',
            'manage_materials',
        ];
    }

    public function render()
    {
        $owner = User::query()->find($this->topic->teacher_id);

        $collaborators = TopicUser::query()
            ->with(['user', 'permissions'])
            ->where('topic_id', $this->topic->id)
            ->where('role_type', 'collaborator')
            ->latest()
            ->get();

        $eligibleUsers = User::query()
            ->select(['id', 'name', 'email'])
            ->where('id', '!=', auth()->id())
            ->where('id', '!=', $this->topic->teacher_id)
            ->whereHas('roles', fn ($q) => $q->where('name', 'disciples'))
            ->whereDoesntHave('roles', fn ($q) => $q->where('name', 'student'))
            ->when($this->editingCollaboratorId, fn ($query) => $query, function ($query) use ($collaborators) {
                $query->whereNotIn('id', $collaborators->pluck('user_id')->filter()->values()->all());
            })
            ->when($this->collaboratorSearch, function ($query) {
                $query->where(function ($sub) {
                    $sub->where('name', 'like', '%' . $this->collaboratorSearch . '%')
                        ->orWhere('email', 'like', '%' . $this->collaboratorSearch . '%');
                });
            })
            ->orderBy('name')
            ->limit(10)
            ->get();

        return view('livewire.mentor.topics.tabs.collaborators-tab', [
            'owner' => $owner,
            'collaborators' => $collaborators,
            'eligibleUsers' => $eligibleUsers,
            'permissionLabels' => $this->permissionLabels(),
        ]);
    }
}