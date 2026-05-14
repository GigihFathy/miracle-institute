<?php

namespace App\Services\Materials;

use Google\Service\YouTube;
use Google\Service\YouTube\Video;
use Google\Service\YouTube\VideoSnippet;
use Google\Service\YouTube\VideoStatus;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use RuntimeException;

class YouTubeService
{
    public function __construct(
        private readonly GoogleClientFactory $clientFactory
    ) {
    }

    public function uploadVideo(UploadedFile $file, string $title, ?string $description = null): string
    {
        $client = $this->clientFactory->makeForCurrentUser();
        $youtube = new YouTube($client);

        $snippet = new VideoSnippet();
        $snippet->setTitle($title);
        $snippet->setDescription($description ?: $title);
        $snippet->setCategoryId('27');

        $status = new VideoStatus();
        $status->setPrivacyStatus((string) config('services.google.youtube_default_privacy', 'unlisted'));

        $video = new Video();
        $video->setSnippet($snippet);
        $video->setStatus($status);

        $response = $youtube->videos->insert('snippet,status', $video, [
            'data' => file_get_contents($file->getRealPath()),
            'mimeType' => $file->getMimeType() ?: 'video/mp4',
            'uploadType' => 'multipart',
            'fields' => 'id',
        ]);

        return $this->toWatchUrl((string) $response->id);
    }

    public function normalizeVideoUrl(string $input): string
    {
        $id = $this->extractVideoId($input);

        if (!$id) {
            throw new RuntimeException('URL atau ID YouTube tidak valid.');
        }

        return $this->toWatchUrl($id);
    }

    public function toEmbedUrl(?string $input): ?string
    {
        $id = $this->extractVideoId((string) $input);

        if (!$id) {
            return null;
        }

        return 'https://www.youtube.com/embed/' . $id;
    }

    public function deleteByUrl(?string $url): void
    {
        if (!$url) {
            return;
        }

        $id = $this->extractVideoId($url);

        if (!$id) {
            return;
        }

        try {
            $client = $this->clientFactory->makeForCurrentUser();
            $youtube = new YouTube($client);
            $youtube->videos->delete($id);
        } catch (\Throwable $e) {
            report($e);
        }
    }

    public function toWatchUrl(string $videoId): string
    {
        return 'https://www.youtube.com/watch?v=' . $videoId;
    }

    public function extractVideoId(string $input): ?string
    {
        $input = trim($input);

        if ($input === '') {
            return null;
        }

        if (preg_match('/^[A-Za-z0-9_-]{11}$/', $input)) {
            return $input;
        }

        $patterns = [
            '/v=([A-Za-z0-9_-]{11})/',
            '/youtu\.be\/([A-Za-z0-9_-]{11})/',
            '/embed\/([A-Za-z0-9_-]{11})/',
            '/shorts\/([A-Za-z0-9_-]{11})/',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $input, $matches)) {
                return $matches[1];
            }
        }

        return null;
    }
}