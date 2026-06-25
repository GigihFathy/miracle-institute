<?php

namespace App\Livewire\Admin\Courses;

use App\Models\Course;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Component;

class ThumbnailIndex extends Component
{
    public function delete(string $path): void
    {
        if (! Str::startsWith($path, 'images/thumbnail/')) {
            $this->dispatch('toast', type: 'error', message: 'Path thumbnail tidak valid.');
            return;
        }

        $usageCount = Course::where('poster', $path)->count();

        if ($usageCount > 0) {
            $this->dispatch('toast', type: 'error', message: "Thumbnail sedang dipakai {$usageCount} course dan tidak bisa dihapus.");

            return;
        }

        $storagePath = $path; // e.g. images/thumbnail/file.jpg
        $publicPath  = public_path($path);

        if (Storage::disk('public')->exists($storagePath)) {
            if (! Storage::disk('public')->delete($storagePath)) {
                $this->dispatch('toast', type: 'error', message: 'Thumbnail gagal dihapus dari server.');
                return;
            }
        } elseif (File::exists($publicPath)) {
            if (! File::delete($publicPath)) {
                $this->dispatch('toast', type: 'error', message: 'Thumbnail gagal dihapus dari server.');
                return;
            }
        } else {
            $this->dispatch('toast', type: 'error', message: 'File thumbnail tidak ditemukan di server.');
            return;
        }

        $this->dispatch('toast', type: 'success', message: 'Thumbnail berhasil dihapus.');
    }

    public function render()
    {
        $usageMap = Course::query()
            ->selectRaw('poster, COUNT(*) as aggregate')
            ->whereNotNull('poster')
            ->groupBy('poster')
            ->pluck('aggregate', 'poster');

        $thumbnails = collect(course_thumbnail_files())
            ->filter(fn ($file) => in_array(Str::lower($file->getExtension()), ['jpg', 'jpeg', 'png', 'webp'], true))
            ->sortByDesc(fn ($file) => $file->getMTime())
            ->map(function ($file) use ($usageMap) {
                $path = 'images/thumbnail/' . $file->getFilename();

                return [
                    'path' => $path,
                    'name' => $file->getFilename(),
                    'size_kb' => (int) ceil($file->getSize() / 1024),
                    'updated_at' => $file->getMTime(),
                    'usage_count' => (int) ($usageMap[$path] ?? 0),
                ];
            })
            ->values();

        return view('livewire.admin.courses.thumbnails', [
            'thumbnails' => $thumbnails,
            'stats' => [
                'total' => $thumbnails->count(),
                'used' => $thumbnails->where('usage_count', '>', 0)->count(),
                'unused' => $thumbnails->where('usage_count', 0)->count(),
            ],
        ])->layout('layouts.admin');
    }
}
