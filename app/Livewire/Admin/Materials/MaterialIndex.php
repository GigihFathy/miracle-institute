<?php

namespace App\Livewire\Admin\Materials;

use App\Models\Material;
use App\Models\Topic;
use App\Models\User;
use Livewire\Component;

class MaterialIndex extends Component
{
    public array $openTopics = [];

    public ?string $editingId = null;
    public string $topic_id = '';
    public string $uploader_id = '';
    public string $name = '';
    public string $visibility = 'Public';
    public string $path = '';
    public string $external_url = '';
    public string $type = 'pdf';
    public string $status = 'active';
    public int $sort_order = 0;

    public string $search = '';

    public bool $showModal = false;

    protected function rules(): array
    {
        return [
            'topic_id' => 'required|exists:topics,id',
            'uploader_id' => 'required|exists:users,id',
            'name' => 'required|string|max:255',
            'path' => 'nullable|string|max:255',
            'external_url' => 'nullable|url|max:255',
            'type' => 'required|string|max:50',
            'visibility' => 'required|string|max:50',
            'status' => 'required|string|max:50',
            'sort_order' => 'nullable|integer|min:0',
        ];
    }

    public function toggleTopic(string $id)
    {
        if (in_array($id, $this->openTopics)) {
            $this->openTopics = array_diff($this->openTopics, [$id]);
        } else {
            $this->openTopics[] = $id;
        }
    }

    public function create(?string $topicId = null)
    {
        $this->resetForm();

        if ($topicId) {
            $this->topic_id = $topicId;
            $this->openTopics[] = $topicId;
        }

        $this->showModal = true;
    }

    public function edit(string $id)
    {
        $row = Material::findOrFail($id);

        $this->editingId = $row->id;
        $this->topic_id = $row->topic_id;
        $this->uploader_id = $row->uploader_id;
        $this->name = $row->name;
        $this->path = $row->path ?? '';
        $this->external_url = $row->external_url ?? '';
        $this->type = $row->type;
        $this->visibility = $row->visibility;
        $this->status = $row->status;
        $this->sort_order = $row->sort_order ?? 0;

        $this->openTopics[] = $row->topic_id;

        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        $this->validateMaterialType();

        $count = \App\Models\Material::where('topic_id', $this->topic_id)
            ->when($this->editingId, fn ($q) => $q->where('id', '!=', $this->editingId))
            ->count();

        if (!$this->editingId && $count >= 3) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'topic_id' => 'Setiap topic hanya boleh memiliki 3 material (PDF, PPT, Video).'
            ]);
        }

        \App\Models\Material::updateOrCreate(
            ['id' => $this->editingId],
            [
                'topic_id' => $this->topic_id,
                'uploader_id' => $this->uploader_id,
                'name' => $this->name,
                'path' => $this->path,
                'external_url' => $this->external_url,
                'type' => $this->type,
                'visibility' => $this->visibility,
                'status' => $this->status,
                'sort_order' => $this->sort_order,
            ]
        );

        $this->resetForm();
        $this->showModal = false;
    }

    public function delete(string $id)
    {
        Material::findOrFail($id)->delete();
    }

    public function render()
    {
        $topics = Topic::with(['materials' => function ($q) {
            if ($this->search) {
                $q->where('name', 'like', "%{$this->search}%");
            }
            $q->orderBy('sort_order');
        }])
        ->when($this->search, function ($q) {
            $q->where('name', 'like', "%{$this->search}%");
        })
        ->orderBy('name')
        ->get();

        return view('livewire.admin.materials.index', [
            'topics' => $topics,
            'users' => User::orderBy('first_name')->get(),
        ])->layout('layouts.admin');
    }

    private function validateMaterialType()
    {
        if (!$this->topic_id || !$this->type) return;

        $exists = \App\Models\Material::where('topic_id', $this->topic_id)
            ->where('type', $this->type)
            ->when($this->editingId, fn ($q) => $q->where('id', '!=', $this->editingId))
            ->exists();

        if ($exists) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'type' => 'Tipe material ini sudah ada di topic ini (harus unik).'
            ]);
        }
    }

    public function getAvailableTypesProperty()
    {
        $all = ['pdf', 'ppt', 'video'];

        $used = \App\Models\Material::where('topic_id', $this->topic_id)
            ->when($this->editingId, fn ($q) => $q->where('id', '!=', $this->editingId))
            ->pluck('type')
            ->toArray();

        return array_diff($all, $used);
    }

    private function resetForm()
    {
        $this->reset([
            'editingId',
            'topic_id',
            'uploader_id',
            'name',
            'path',
            'external_url',
            'type',
            'visibility',
            'status',
            'sort_order'
        ]);

        $this->visibility = 'Public';
        $this->type = 'pdf';
        $this->status = 'active';
        $this->sort_order = 0;
    }
}