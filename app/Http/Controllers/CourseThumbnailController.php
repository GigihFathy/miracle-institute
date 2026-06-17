<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class CourseThumbnailController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'thumbnail' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ]);

        $file = $validated['thumbnail'];
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $extension = $file->getClientOriginalExtension();
        $filename = Str::slug($originalName) . '-' . Str::lower(Str::random(6)) . '.' . Str::lower($extension);
        $targetDirectory = public_path('images/thumbnail');
        $targetPath = $targetDirectory . DIRECTORY_SEPARATOR . $filename;

        try {
            File::ensureDirectoryExists($targetDirectory);
            File::put($targetPath, File::get($file->getRealPath()));
        } catch (\Throwable $exception) {
            report($exception);

            return back()->withErrors([
                'thumbnail' => 'Thumbnail gagal diupload ke public/images/thumbnail. Periksa permission folder server.',
            ])->withInput();
        }

        return back()->with('success', 'Thumbnail berhasil diupload dan masuk ke library sistem.');
    }

    public function show(string $path): BinaryFileResponse
    {
        $relativePath = course_thumbnail_relative_path($path);

        abort_unless($relativePath, 404);

        $fullPath = public_path($relativePath);

        if (! File::exists($fullPath)) {
            $fullPath = $this->findClosestThumbnailMatch(basename($relativePath));
        }

        abort_unless(File::exists($fullPath), 404);

        return response()->file($fullPath, [
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }

    private function findClosestThumbnailMatch(string $requestedFilename): ?string
    {
        $directory = public_path('images/thumbnail');

        if (! File::exists($directory)) {
            return null;
        }

        $requestedName = pathinfo($requestedFilename, PATHINFO_FILENAME);
        $requestedExtension = Str::lower(pathinfo($requestedFilename, PATHINFO_EXTENSION));
        $requestedNormalized = $this->normalizeThumbnailName($requestedName);

        foreach (File::files($directory) as $file) {
            if (! $file->isFile()) {
                continue;
            }

            $extension = Str::lower($file->getExtension());

            if (! in_array($extension, ['jpg', 'jpeg', 'png', 'webp'], true)) {
                continue;
            }

            $candidateName = pathinfo($file->getFilename(), PATHINFO_FILENAME);
            $candidateNormalized = $this->normalizeThumbnailName($candidateName);

            if (
                $candidateNormalized === $requestedNormalized
                || Str::startsWith($candidateNormalized, $requestedNormalized)
                || Str::startsWith($requestedNormalized, $candidateNormalized)
            ) {
                if ($requestedExtension === '' || $requestedExtension === $extension) {
                    return $file->getPathname();
                }
            }
        }

        return null;
    }

    private function normalizeThumbnailName(string $value): string
    {
        return (string) Str::of($value)
            ->lower()
            ->replace(['_', '-', ' '], '')
            ->replaceMatches('/[^a-z0-9]/', '');
    }
}
