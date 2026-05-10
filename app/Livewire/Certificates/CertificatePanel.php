<?php

namespace App\Livewire\Certificates;

use App\Livewire\Concerns\WithTableState;
use App\Models\Certificate;
use Livewire\Component;

class CertificatePanel extends Component
{
    use WithTableState;

    protected $queryString = [
        'search' => ['except' => ''],
        'perPage' => ['except' => 9],
    ];

    public function render()
    {
        $certificates = Certificate::with('course')
            ->where('user_id', auth()->id())
            ->when(
                $this->search,
                fn ($q) =>
                $q->where('certificate_number', 'like', '%' . $this->search . '%')
            )
            ->latest()
            ->paginate($this->perPage);

        return view('livewire.certificates.certificate-panel', [
            'certificates' => $certificates,
        ])->layout('layouts.learning');
    }
}