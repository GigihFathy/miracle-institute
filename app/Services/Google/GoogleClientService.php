<?php

namespace App\Services\Google;

use App\Models\User;
use Google\Client as GoogleClient;
use RuntimeException;
use Illuminate\Support\Facades\Crypt;

class GoogleClientService
{
    public function make(User $user): GoogleClient
    {
        $client = new GoogleClient();
        $client->setClientId(config('services.google.client_id'));
        $client->setClientSecret(config('services.google.client_secret'));
        $client->setRedirectUri(config('services.google.redirect'));
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

        $accessToken = $this->getDecryptedToken($user);

        $client->setAccessToken($accessToken);

        // Refresh token jika expired
        if ($client->isAccessTokenExpired()) {
            if (empty($user->google_refresh_token)) {
                throw new RuntimeException('Token Google kedaluwarsa dan refresh token tidak tersedia. Hubungkan ulang akun Google.');
            }

            $newToken = $client->fetchAccessTokenWithRefreshToken($user->google_refresh_token);

            if (!empty($newToken['error'])) {
                $message = $newToken['error_description'] ?? $newToken['error'];
                throw new RuntimeException("Gagal refresh token Google: $message");
            }

            $this->saveEncryptedToken($user, $newToken);
            $client->setAccessToken($newToken);
        }

        return $client;
    }

    private function getDecryptedToken(User $user): array
    {
        return [
            'access_token' => $user->google_access_token ? Crypt::decryptString($user->google_access_token) : '',
            'refresh_token' => $user->google_refresh_token ? Crypt::decryptString($user->google_refresh_token) : '',
            'expires_in' => $user->google_token_expires_at
                ? now()->diffInSeconds($user->google_token_expires_at, false)
                : 0,
            'created' => now()->timestamp,
        ];
    }

    private function saveEncryptedToken(User $user, array $token): void
    {
        $user->forceFill([
            'google_access_token' => $token['access_token'] ? Crypt::encryptString($token['access_token']) : $user->google_access_token,
            'google_refresh_token' => $token['refresh_token'] ? Crypt::encryptString($token['refresh_token']) : $user->google_refresh_token,
            'google_token_expires_at' => now()->addSeconds($token['expires_in'] ?? 3600),
        ])->save();
    }
}