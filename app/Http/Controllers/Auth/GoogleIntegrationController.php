<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use Google\Client as GoogleClient;
use Illuminate\Http\Request;

class GoogleIntegrationController extends Controller
{
    private function getClient(): GoogleClient
    {
        $client = new GoogleClient();
        $client->setClientId(config('services.google.client_id'));
        $client->setClientSecret(config('services.google.client_secret'));
        $client->setRedirectUri(route('admin.google.callback'));
        $client->setAccessType('offline');
        $client->setPrompt('consent');
        $client->setScopes([
            'https://www.googleapis.com/auth/drive.file',
            'https://www.googleapis.com/auth/youtube.upload',
            'https://www.googleapis.com/auth/youtube.force-ssl',
        ]);
        return $client;
    }

    public function redirect()
    {
        return redirect($this->getClient()->createAuthUrl());
    }

    public function callback(Request $request)
    {
        $client = $this->getClient();
        $token = $client->fetchAccessTokenWithAuthCode($request->code);

        if (isset($token['error'])) {
            return redirect()->route('admin.settings.index')->with('error', 'Gagal menghubungkan Google: ' . $token['error']);
        }

        if (isset($token['refresh_token'])) {
            SystemSetting::updateOrCreate(
                ['key' => 'google_master_refresh_token'],
                ['value' => encrypt($token['refresh_token'])] 
            );

            return redirect()->route('admin.settings.index')->with('success', 'Google Drive berhasil dihubungkan sebagai pusat sistem!');
        }

        return redirect()->route('admin.settings.index')->with('error', 'Gagal mendapatkan hak akses offline.');
    }
}