<?php

namespace App\Services\Materials;

use App\Models\SystemSetting;
use Google\Client as GoogleClient;
use RuntimeException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;

class GoogleClientFactory
{
    public function makeForSystem(): GoogleClient
    {
        $client = new GoogleClient();
        $client->setClientId((string) config('services.google.client_id'));
        $client->setClientSecret((string) config('services.google.client_secret'));
        $client->setAccessType('offline');

        $cachedToken = Cache::get('google_master_access_token');

        if ($cachedToken) {
            $client->setAccessToken($cachedToken);
        }

        if ($client->isAccessTokenExpired()) {
            $setting = SystemSetting::where('key', 'google_master_refresh_token')->first();
            
            if (!$setting || empty($setting->value)) {
                throw new RuntimeException('Sistem belum terhubung dengan akun Google pusat. Harap hubungkan Google Drive dari halaman pengaturan admin.');
            }

            $masterRefreshToken = Crypt::decrypt($setting->value);

            $newToken = $client->fetchAccessTokenWithRefreshToken($masterRefreshToken);

            if (!empty($newToken['error'])) {
                $message = $newToken['error_description'] ?? $newToken['error'];
                throw new RuntimeException("Gagal refresh token master: $message. Pastikan akun admin tidak mencabut izin aplikasi.");
            }

            $client->setAccessToken($newToken);

            $expiresIn = $newToken['expires_in'] ?? 3600;
            Cache::put('google_master_access_token', $newToken, now()->addSeconds($expiresIn - 300));
        }

        return $client;
    }
}