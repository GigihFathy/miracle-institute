<?php

namespace App\Livewire\Admin\Certificates;

use App\Livewire\Concerns\WithAdminTableState;
use App\Models\CertificateSignatory;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class SignatoryIndex extends Component
{
    use WithAdminTableState, WithFileUploads;

    public bool $showModal = false;

    public ?string $editingId = null;
    public string $name = '';
    public string $title = '';
    public string $active_from = '';
    public ?string $active_until = null;
    public int $sort_order = 0;

    public $signatureFile = null;
    public ?string $currentSignatureImage = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'perPage' => ['except' => 10],
    ];

    protected function rules(): array
    {
        return [
            'name'           => 'required|string|max:255',
            'title'          => 'required|string|max:255',
            'active_from'    => 'required|date',
            'active_until'   => 'nullable|date|after_or_equal:active_from',
            'sort_order'     => 'required|integer|min:0',
            'signatureFile'  => 'nullable|image|max:2048',
        ];
    }

    public function create(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function edit(string $id): void
    {
        $row = CertificateSignatory::findOrFail($id);

        $this->editingId             = $row->id;
        $this->name                  = $row->name;
        $this->title                 = $row->title;
        $this->active_from           = $row->active_from->format('Y-m-d');
        $this->active_until          = $row->active_until?->format('Y-m-d');
        $this->sort_order            = $row->sort_order;
        $this->currentSignatureImage = $row->signature_image;
        $this->signatureFile         = null;

        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate();

        $activeFrom  = $this->active_from;
        $activeUntil = $this->active_until ?: null;

        $overlapping = CertificateSignatory::query()
            ->when($this->editingId, fn ($q) => $q->where('id', '!=', $this->editingId))
            ->where(function ($q) use ($activeFrom) {
                $q->whereNull('active_until')
                    ->orWhere('active_until', '>=', $activeFrom);
            })
            ->when($activeUntil, fn ($q) => $q->where('active_from', '<=', $activeUntil))
            ->get();

        if ($overlapping->count() >= 2) {
            $this->addError('active_from', 'Sudah ada 2 penandatangan di periode yang sama. Maksimal 2 penandatangan per periode.');
            return;
        }

        if ($overlapping->where('sort_order', $this->sort_order)->isNotEmpty()) {
            $positionLabel = $this->sort_order === 0 ? 'Kiri' : 'Kanan';
            $this->addError('sort_order', "Posisi {$positionLabel} sudah digunakan di periode yang sama.");
            return;
        }

        $data = [
            'name'         => $this->name,
            'title'        => $this->title,
            'active_from'  => $this->active_from,
            'active_until' => $this->active_until ?: null,
            'sort_order'   => $this->sort_order,
        ];

        if ($this->signatureFile) {
            if ($this->currentSignatureImage) {
                Storage::disk('public')->delete($this->currentSignatureImage);
            }
            $data['signature_image'] = $this->signatureFile->store('certificate-signatures', 'public');
        }

        CertificateSignatory::updateOrCreate(
            ['id' => $this->editingId],
            $data
        );

        $this->resetForm();
        session()->flash('success', 'Penandatangan berhasil disimpan.');
    }

    public function delete(string $id): void
    {
        $row = CertificateSignatory::findOrFail($id);

        if ($row->signature_image) {
            Storage::disk('public')->delete($row->signature_image);
        }

        $row->delete();
        session()->flash('success', 'Penandatangan berhasil dihapus.');
    }

    public function render()
    {
        $rows = CertificateSignatory::query()
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%")
                ->orWhere('title', 'like', "%{$this->search}%"))
            ->orderBy('sort_order')
            ->orderBy('active_from')
            ->paginate($this->perPage);

        return view('livewire.admin.certificates.signatories', compact('rows'))
            ->layout('layouts.admin');
    }

    private function resetForm(): void
    {
        $this->reset(['editingId', 'name', 'title', 'active_from', 'active_until', 'sort_order', 'signatureFile', 'currentSignatureImage', 'showModal']);
        $this->sort_order = 0;
    }
}
