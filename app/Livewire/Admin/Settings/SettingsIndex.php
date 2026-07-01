<?php

namespace App\Livewire\Admin\Settings;

use App\Models\Company;
use App\Models\SystemSetting;
use Livewire\Component;
use Illuminate\Support\Facades\Cache;

class SettingsIndex extends Component
{
    public bool $isGoogleConnected = false;
    public bool $showDisconnectGoogleModal = false;
    public string $disconnectGooglePassword = '';

    public string $name = '';
    public string $description = '';
    public string $address = '';
    public string $vision = '';
    public string $mission = '';
    public string $logo = '';
    public string $facebook = '';
    public string $instagram = '';
    public string $youtube = '';
    public string $email = '';

    public function mount(): void
    {
        $company = Company::first();

        if ($company) {
            $this->name        = $company->name        ?? '';
            $this->description = $company->description ?? '';
            $this->address     = $company->address     ?? '';
            $this->vision      = $company->vision      ?? '';
            $this->mission     = $company->mission     ?? '';
            $this->logo        = $company->logo        ?? '';
            $this->facebook    = $company->facebook    ?? '';
            $this->instagram   = $company->instagram   ?? '';
            $this->youtube     = $company->youtube     ?? '';
            $this->email       = $company->email       ?? '';
        }

        $this->checkGoogleConnection();
    }

    public function checkGoogleConnection()
    {
        $setting = SystemSetting::where('key', 'google_master_refresh_token')->first();
        $this->isGoogleConnected = $setting && !empty($setting->value);
    }

    public function connectGoogle()
    {
        return redirect()->route('admin.google.connect');
    }

    public function disconnectGoogle()
    {
        $this->validate([
            'disconnectGooglePassword' => ['required', 'string'],
        ], [
            'disconnectGooglePassword.required' => 'Password wajib diisi untuk memutus koneksi.',
        ]);

        if ($this->disconnectGooglePassword !== config('services.google.disconnect_password')) {
            $this->addError('disconnectGooglePassword', 'Password default tidak sesuai.');

            return;
        }

        SystemSetting::where('key', 'google_master_refresh_token')->delete();
        
        Cache::forget('google_master_access_token');
        
        $this->isGoogleConnected = false;
        $this->showDisconnectGoogleModal = false;
        $this->disconnectGooglePassword = '';
        $this->resetErrorBag('disconnectGooglePassword');

        $this->dispatch('toast', type: 'success', message: 'Koneksi Google Drive berhasil diputus.');
    }

    public function openDisconnectGoogleModal(): void
    {
        $this->disconnectGooglePassword = '';
        $this->resetErrorBag('disconnectGooglePassword');
        $this->showDisconnectGoogleModal = true;
    }

    public function closeDisconnectGoogleModal(): void
    {
        $this->showDisconnectGoogleModal = false;
        $this->disconnectGooglePassword = '';
        $this->resetErrorBag('disconnectGooglePassword');
    }

    public function save(): void
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'address' => 'nullable|string',
            'vision' => 'nullable|string',
            'mission' => 'nullable|string',
            'logo' => 'nullable|string',
            'facebook' => 'nullable|url',
            'instagram' => 'nullable|url',
            'youtube' => 'nullable|url',
            'email' => 'nullable|email',
        ]);

        Company::updateOrCreate(
            ['id' => 1],
            [
                'name' => $this->name,
                'description' => $this->description,
                'address' => $this->address,
                'vision' => $this->vision,
                'mission' => $this->mission,
                'logo' => $this->logo,
                'facebook' => $this->facebook,
                'instagram' => $this->instagram,
                'youtube' => $this->youtube,
                'email' => $this->email,
            ]
        );

        $this->dispatch('toast', type: 'success', message: 'Pengaturan berhasil disimpan.');
    }

    public function render()
    {
        return view('livewire.admin.settings.index')->layout('layouts.admin');
    }
}
