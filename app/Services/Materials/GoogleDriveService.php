<?php

namespace App\Services\Materials;

use Google\Service\Drive;
use Google\Service\Drive\DriveFile;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use RuntimeException;

class GoogleDriveService
{
    public function __construct(
        private readonly GoogleClientFactory $clientFactory
    ) {
    }

    public function uploadDocument(UploadedFile $file, string $title): string
    {
        $folderId = (string) config('services.google.drive_root_folder');

        if ($folderId === '') {
            throw new RuntimeException('GOOGLE_DRIVE_ROOT_FOLDER belum diisi.');
        }

        $client = $this->clientFactory->makeForCurrentUser();
        $drive = new Drive($client);

        $driveFile = new DriveFile([
            'name' => Str::of($title)->slug('-')->limit(80, '') . '-' . Str::random(6) . '.' . $file->getClientOriginalExtension(),
            'parents' => [$folderId],
        ]);

        $created = $drive->files->create($driveFile, [
            'data' => file_get_contents($file->getRealPath()),
            'mimeType' => $file->getMimeType(),
            'uploadType' => 'multipart',
            'fields' => 'id',
        ]);

        return (string) $created->id;
    }

    public function deleteById(?string $fileId): void
    {
        if (!$fileId) {
            return;
        }

        try {
            $client = $this->clientFactory->makeForCurrentUser();
            $drive = new Drive($client);
            $drive->files->delete($fileId);
        } catch (\Throwable $e) {
            report($e);
        }
    }

    public function toPreviewUrl(?string $fileId): ?string
    {
        if (!$fileId) {
            return null;
        }

        return 'https://drive.google.com/file/d/' . $fileId . '/preview';
    }
}