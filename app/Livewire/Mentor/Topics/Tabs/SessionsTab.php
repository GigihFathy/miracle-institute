<?php

namespace App\Livewire\Mentor\Topics\Tabs;

use App\Livewire\Concerns\InteractsWithMentorTopic;
use App\Models\Topic;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class SessionsTab extends Component
{
    use InteractsWithMentorTopic;

    public Topic $topic;

    public bool $showSessionModal = false;
    public ?string $editingSessionId = null;

    public string $sessionTitle = '';
    public string $sessionZoomLink = '';
    public ?string $sessionStartAt = null;
    public ?string $sessionEndAt = null;
    public string $sessionStatus = 'scheduled';

    public function mount(string $topicId): void
    {
        $this->topic = $this->loadTopic($topicId);

        abort_unless($this->canAccessTopic($this->topic, ['manage_sessions', 'manage_topics']), 403);
    }

    public function editSession(): void
    {
        abort_unless($this->canAccessTopic($this->topic, ['manage_sessions', 'manage_topics']), 403);

        $session = $this->topic->videoSessions()->latest('start_at')->first();

        $this->editingSessionId = $session?->id;

        $this->sessionTitle = $session?->title ?? '';
        $this->sessionZoomLink = $session?->zoom_link ?? '';
        $this->sessionStartAt = $session?->start_at?->format('Y-m-d\TH:i') ?? now()->addDay()->format('Y-m-d\TH:i');
        $this->sessionEndAt = $session?->end_at?->format('Y-m-d\TH:i') ?? now()->addDay()->addHour()->format('Y-m-d\TH:i');
        $this->sessionStatus = $session?->status ?? 'scheduled';

        $this->showSessionModal = true;
    }

    public function closeSessionModal(): void
    {
        $this->showSessionModal = false;
    }

    public function saveSession(): void
    {
        abort_unless($this->canAccessTopic($this->topic, ['manage_sessions', 'manage_topics']), 403);

        $session = $this->topic->videoSessions()->latest('start_at')->first();
        $now = now();
        $start = Carbon::parse($this->sessionStartAt);
        $end = Carbon::parse($this->sessionEndAt);

        $this->validate([
            'sessionTitle' => ['required', 'string', 'max:255'],
            'sessionZoomLink' => ['required', 'url', 'max:2048'],
            'sessionStartAt' => ['required', 'date'],
            'sessionEndAt' => ['required', 'date', 'after:sessionStartAt'],
            'sessionStatus' => ['required', Rule::in(['scheduled', 'ongoing', 'completed', 'cancelled'])],
        ]);

        if ($this->sessionStatus === 'scheduled' && $start->lte($now)) {
            throw ValidationException::withMessages([
                'sessionStartAt' => 'Session scheduled harus dimulai di masa depan.',
            ]);
        }

        if ($this->sessionStatus === 'ongoing' && ($start->gt($now) || $end->lte($now))) {
            throw ValidationException::withMessages([
                'sessionStatus' => 'Status ongoing hanya valid saat sesi sedang berlangsung.',
            ]);
        }

        if ($this->sessionStatus === 'completed' && $end->gte($now)) {
            throw ValidationException::withMessages([
                'sessionStatus' => 'Status completed hanya valid untuk sesi yang sudah selesai.',
            ]);
        }

        if ($this->sessionStatus === 'cancelled' && $end->lt($start)) {
            throw ValidationException::withMessages([
                'sessionEndAt' => 'Tanggal selesai tidak valid.',
            ]);
        }

        $payload = [
            'title' => $this->sessionTitle,
            'zoom_link' => $this->sessionZoomLink,
            'start_at' => $this->sessionStartAt,
            'end_at' => $this->sessionEndAt,
            'reminder_sent_at' => null,
            'status' => $this->sessionStatus,
        ];

        if ($session) {
            $session->update($payload);
        } else {
            $this->topic->videoSessions()->create($payload);
        }

        $this->closeSessionModal();
        $this->resetSessionForm();

        session()->flash('success', $session ? 'Session berhasil diperbarui.' : 'Session berhasil dibuat.');
    }

    private function resetSessionForm(): void
    {
        $this->reset([
            'editingSessionId',
            'sessionTitle',
            'sessionZoomLink',
            'sessionStartAt',
            'sessionEndAt',
            'sessionStatus',
        ]);

        $this->sessionStatus = 'scheduled';
        $this->sessionStartAt = now()->addDay()->format('Y-m-d\TH:i');
        $this->sessionEndAt = now()->addDay()->addHour()->format('Y-m-d\TH:i');
    }

    public function render()
    {
        return view('livewire.mentor.topics.tabs.sessions-tab', [
            'session' => $this->topic->videoSessions()->latest('start_at')->first(),
        ]);
    }
}