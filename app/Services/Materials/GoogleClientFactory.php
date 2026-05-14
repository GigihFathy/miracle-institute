<?php

namespace App\Services\Materials;

use App\Models\User;
use App\Services\Google\GoogleClientService;
use Google\Client as GoogleClient;
use RuntimeException;

class GoogleClientFactory
{
    public function makeForCurrentUser(): GoogleClient
    {
        /** @var User|null $user */
        $user = auth()->user();

        if (!$user) {
            throw new RuntimeException('User belum login.');
        }

        if (!$user->google_access_token) {
            throw new RuntimeException('Google belum terhubung. Silakan login / connect akun Google terlebih dahulu.');
        }

        $client = new GoogleClient();
        $client->setClientId((string) config('services.google.client_id'));
        $client->setClientSecret((string) config('services.google.client_secret'));
        $client->setRedirectUri((string) config('services.google.redirect'));
        $client->setAccessType('offline');
        $client->setPrompt('consent');
        $client->setScopes([
            'openid',
            'profile',
            'email',
            'https://www.googleapis.com/auth/drive.file',
            'https://www.googleapis.com/auth/youtube.upload',
            'https://www.googleapis.com/auth/youtube.force-ssl',
        ]);

        $client->setAccessToken([
            'access_token' => $user->google_access_token,
            'refresh_token' => $user->google_refresh_token,
            'expires_in' => $user->google_token_expires_at
                ? now()->diffInSeconds($user->google_token_expires_at, false)
                : 0,
            'created' => now()->timestamp,
        ]);

        if (!$client->isAccessTokenExpired()) {
            return $client;
        }

        if (!$user->google_refresh_token) {
            throw new RuntimeException('Access token Google sudah kedaluwarsa dan refresh token tidak tersedia. Hubungkan ulang akun Google.');
        }

        $newToken = $client->fetchAccessTokenWithRefreshToken($user->google_refresh_token);

        if (!empty($newToken['error'])) {
            $message = $newToken['error_description'] ?? $newToken['error'];

            if ($newToken['error'] === 'invalid_grant') {
                throw new RuntimeException('Refresh token Google tidak valid / sudah dicabut. Hubungkan ulang akun Google.');
            }

            throw new RuntimeException('Gagal refresh token Google: ' . $message);
        }

        $user->forceFill([
            'google_access_token' => $newToken['access_token'] ?? $user->google_access_token,
            'google_token_expires_at' => now()->addSeconds($newToken['expires_in'] ?? 3600),
            'google_refresh_token' => $newToken['refresh_token'] ?? $user->google_refresh_token,
        ])->save();

        $client->setAccessToken($newToken);

        return $client;
    }
}