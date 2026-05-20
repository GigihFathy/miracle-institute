<?php

namespace App\Livewire\Admin\Settings;

use App\Models\Company;
use App\Models\SystemSetting;
use Livewire\Component;
use Illuminate\Support\Facades\Cache;

class SettingsIndex extends Component
{
    public bool $isGoogleConnected = false;

    public string $name = '';
    public string $description = '';
    public string $address = '';
    public string $vision = '';
    public string $mission = '';
    public string $logo = '';
    public string $facebook = '';
    public string $instagram = '';
    public string $youtube = '';
    public string $whatsapp = '';

    public function mount(): void
    {
        $company = Company::first();

        if ($company) {
            $this->fill($company->only([
                'name','description','address','vision','mission','logo',
                'facebook','instagram','youtube','whatsapp',
            ]));
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
        SystemSetting::where('key', 'google_master_refresh_token')->delete();
        
        Cache::forget('google_master_access_token');
        
        $this->isGoogleConnected = false;

        $this->dispatch('toast', type: 'success', message: 'Koneksi Google Drive berhasil diputus.');
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
            'whatsapp' => 'nullable|url',
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
                'whatsapp' => $this->whatsapp,
            ]
        );

        $this->dispatch('toast', type: 'success', message: 'Pengaturan berhasil disimpan.');
    }

    public function render()
    {
        return view('livewire.admin.settings.index')->layout('layouts.admin');
    }
}