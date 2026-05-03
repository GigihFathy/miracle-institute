<?php

namespace App\Livewire\Certificates;

use App\Livewire\Concerns\WithTableState;
use App\Models\Certificate;
use Livewire\Component;

class CertificatePanel extends Component
{
    use WithTableState;

    public string $type = '';

    protected $queryString = [
        'type' => ['except' => ''],
        'search' => ['except' => ''],
        'perPage' => ['except' => 9],
    ];

    public function updatedType(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $certificates = Certificate::with(['course', 'topic'])
            ->where('user_id', auth()->id())
            ->when($this->type, fn ($q) => $q->where('type', $this->type))
            ->when($this->search, fn ($q) => $q->where('certificate_number', 'like', '%' . $this->search . '%'))
            ->latest()
            ->paginate($this->perPage);

        return view('livewire.certificates.certificate-panel', [
            'certificates' => $certificates,
        ])->layout('layouts.learning');
    }
}