<?php

namespace App\Livewire\Admin\Profile;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Livewire\Component;

class AdminProfileIndex extends Component
{
    public string $activeTab = 'account';

    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public string $gender = '';
    public string $dob = '';

    public string $currentPassword = '';
    public string $password = '';
    public string $password_confirmation = '';

    public function mount(): void
    {
        $user = Auth::user();

        $this->name   = (string) $user->name;
        $this->email  = (string) $user->email;
        $this->phone  = (string) ($user->phone ?? '');
        $this->gender = (string) ($user->gender ?? '');
        $this->dob    = $user->dob?->format('Y-m-d') ?? '';
    }

    public function setActiveTab(string $tab): void
    {
        if (!in_array($tab, ['account', 'password'], true)) {
            return;
        }

        $this->activeTab = $tab;
        $this->resetValidation();
    }

    public function save(): void
    {
        $userId = Auth::id();

        $validated = $this->validate([
            'name'   => ['required', 'string', 'max:35'],
            'email'  => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
            'phone'  => ['nullable', 'string', 'regex:/^62[0-9]{8,13}$/'],
            'gender' => ['nullable', Rule::in(['male', 'female'])],
            'dob'    => ['nullable', 'date', 'before_or_equal:' . now()->subYears(13)->format('Y-m-d')],
        ], [
            'dob.before_or_equal' => 'Tanggal lahir harus menunjukkan usia minimal 13 tahun.',
        ]);

        Auth::user()->update([
            'name'   => $validated['name'],
            'email'  => $validated['email'],
            'phone'  => $validated['phone'] ?: null,
            'gender' => $validated['gender'] ?: null,
            'dob'    => $validated['dob'] ?: null,
        ]);

        $this->dispatch('toast', type: 'success', message: 'Profil berhasil diperbarui.');
    }

    public function updatePassword(): mixed
    {
        $this->activeTab = 'password';

        $this->validate([
            'currentPassword'      => ['required', 'current_password'],
            'password'             => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
            'password_confirmation' => ['required'],
        ]);

        Auth::user()->update([
            'password' => Hash::make($this->password),
        ]);

        Auth::logout();

        if (request()->hasSession()) {
            request()->session()->invalidate();
            request()->session()->regenerateToken();
        } else {
            Session::flush();
        }

        session()->flash('success', 'Kata sandi diperbarui. Silakan login kembali.');

        return redirect()->to(localized_route('login'));
    }

    public function render()
    {
        return view('livewire.admin.profile.index')->layout('layouts.admin');
    }
}
