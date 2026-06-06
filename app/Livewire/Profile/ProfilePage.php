<?php

namespace App\Livewire\Profile;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Livewire\Component;

class ProfilePage extends Component
{
    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public string $gender = '';
    public string $dob = '';
    public string $activeTab = 'account';
    public string $currentPassword = '';
    public string $password = '';
    public string $password_confirmation = '';

    public function mount(): void
    {
        $user = Auth::user();

        abort_unless($user, 401);

        $this->name = (string) $user->name;
        $this->email = (string) $user->email;
        $this->phone = (string) ($user->phone ?? '');
        $this->gender = (string) ($user->gender ?? '');
        $this->dob = $user->dob?->format('Y-m-d') ?? '';
    }

    protected function rules(): array
    {
        $userId = Auth::id();

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
            'phone' => ['required', 'string', 'regex:/^62[0-9]{8,13}$/'],
            'gender' => ['required', Rule::in(['male', 'female'])],
            'dob' => ['required', 'date', 'before_or_equal:today'],
        ];
    }

    protected function passwordRules(): array
    {
        return [
            'currentPassword' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
            'password_confirmation' => ['required'],
        ];
    }

    public function updated(string $property): void
    {
        if (!in_array($property, ['name', 'email', 'phone', 'gender', 'dob'], true)) {
            return;
        }

        $this->validateOnly($property);
    }

    public function save(): mixed
    {
        $validated = $this->validate();
        $user = Auth::user();

        abort_unless($user, 401);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?: null,
            'gender' => $validated['gender'] ?: null,
            'dob' => $validated['dob'] ?: null,
        ]);

        session()->flash('success', __('general.profile.flash.saved'));

        return null;
    }

    public function setActiveTab(string $tab): void
    {
        if (!in_array($tab, ['account', 'password'], true)) {
            return;
        }

        $this->activeTab = $tab;
        $this->resetValidation();
    }

    public function updatePassword(): mixed
    {
        $this->activeTab = 'password';

        $this->validate([
            'currentPassword' => ['required'],
            'password' => ['required'],
            'password_confirmation' => ['required'],
        ]);

        $validated = $this->validate($this->passwordRules());

        $user = Auth::user();

        abort_unless($user, 401);

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        Auth::logout();

        request()->session()->invalidate();
        request()->session()->regenerateToken();

        session()->flash('success', __('general.profile.flash.password_updated_login_again'));

        return redirect()->to(localized_route('login'));
    }

    public function render()
    {
        $user = Auth::user()?->loadMissing('roles');
        $activeRole = session('active_role');
        $layout = $activeRole === 'admin' ? 'layouts.app' : 'layouts.learning';

        return view('livewire.profile.profile-page', [
            'user' => $user,
            'activeRole' => $activeRole,
        ])->layout($layout);
    }
}
