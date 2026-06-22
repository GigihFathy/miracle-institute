<?php

namespace App\Services\Materials;

use App\Models\Material;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\ValidationException;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class MaterialAssetService
{
    public function __construct(
        private readonly GoogleDriveService $drive,
        public readonly YoutubeService $youtube
    ) {
    }

    public function sync(?Material $material, string $type, mixed $file, ?string $externalUrl, string $title): array
    {
        $type = strtolower(trim($type));

        if (in_array($type, ['pdf', 'ppt'], true)) {
            $upload = $this->resolveUpload($file);

            if ($upload) {
                $path = $this->drive->uploadDocument($upload, $title);
                $this->cleanupPrevious($material);

                return [
                    'path' => $path,
                    'external_url' => null,
                ];
            }

            if ($material && in_array($material->type, ['pdf', 'ppt'], true) && $material->path) {
                return [
                    'path' => $material->path,
                    'external_url' => null,
                ];
            }

            throw ValidationException::withMessages([
                'materialFile' => 'File PDF/PPT wajib diunggah.',
            ]);
        }

        if ($type === 'video') {
            $upload = $this->resolveUpload($file);

            if ($upload) {
                $url = $this->youtube->uploadVideo($upload, $title);
                $this->cleanupPrevious($material);

                return [
                    'path' => null,
                    'external_url' => $url,
                ];
            }

            if (!empty($externalUrl)) {
                $url = $this->youtube->normalizeVideoUrl($externalUrl);
                $this->cleanupPrevious($material);

                return [
                    'path' => null,
                    'external_url' => $url,
                ];
            }

            if ($material && $material->type === 'video' && $material->external_url) {
                return [
                    'path' => null,
                    'external_url' => $material->external_url,
                ];
            }

            throw ValidationException::withMessages([
                'materialExternalUrl' => 'Video wajib memakai URL YouTube atau file video.',
            ]);
        }

        throw ValidationException::withMessages([
            'type' => 'Tipe material tidak valid.',
        ]);
    }

    public function resolvePreviewUrl(?Material $material): ?string
    {
        if (!$material) {
            return null;
        }

        if ($material->type === 'video') {
            return $this->youtube->toEmbedUrl($material->external_url);
        }

        return $this->drive->toPreviewUrl($material->path);
    }

    public function delete(Material $material): void
    {
        if ($material->type === 'video' && $material->external_url) {
            $this->youtube->deleteByUrl($material->external_url);
            return;
        }

        if (in_array($material->type, ['pdf', 'ppt'], true) && $material->path) {
            $this->drive->deleteById($material->path);
        }
    }

    private function cleanupPrevious(?Material $material): void
    {
        if (!$material) {
            return;
        }

        try {
            $this->delete($material);
        } catch (\Throwable $e) {
            report($e);
        }
    }

    private function hasUpload(mixed $file): bool
    {
        return $this->resolveUpload($file) !== null;
    }

    private function resolveUpload(mixed $file): UploadedFile|TemporaryUploadedFile|null
    {
        if (is_array($file)) {
            $file = $file[0] ?? null;
        }

        if ($file instanceof UploadedFile || $file instanceof TemporaryUploadedFile) {
            return $file;
        }

        if (
            is_object($file)
            && method_exists($file, 'getRealPath')
            && method_exists($file, 'getClientOriginalExtension')
        ) {
            return $file;
        }

        return null;
    }
}
