<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
        $storagePath = 'images/thumbnail/' . $filename;

        try {
            Storage::disk('public')->put($storagePath, file_get_contents($file->getRealPath()));
        } catch (\Throwable $exception) {
            report($exception);

            return back()->withErrors([
                'thumbnail' => 'Thumbnail gagal diupload. Pastikan storage symlink sudah dibuat dengan: php artisan storage:link',
            ])->withInput();
        }

        return back()->with('success', 'Thumbnail berhasil diupload dan masuk ke library sistem.');
    }
}
