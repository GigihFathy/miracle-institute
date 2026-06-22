<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
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
}
