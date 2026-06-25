<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Routing\UrlRoutable;
use Illuminate\Support\Facades\File;

spl_autoload_register(static function (string $class): void {
    static $vendorPath = null;

    if ($vendorPath === null) {
        $vendorPath = dirname(__DIR__) . '/vendor';
    }

    if (str_starts_with($class, 'Google\\Auth\\')) {
        $relativeClass = substr($class, strlen('Google\\Auth\\'));
        $file = $vendorPath . '/google/auth/src/' . str_replace('\\', '/', $relativeClass) . '.php';

        if (is_file($file)) {
            require_once $file;
        }

        return;
    }

    if (str_starts_with($class, 'Google\\Service\\')) {
        $relativeClass = substr($class, strlen('Google\\Service\\'));
        $serviceFile = $vendorPath . '/google/apiclient-services/src/' . str_replace('\\', '/', $relativeClass) . '.php';

        if (is_file($serviceFile)) {
            require_once $serviceFile;
            return;
        }

        $coreServiceFile = $vendorPath . '/google/apiclient/src/Service/' . str_replace('\\', '/', $relativeClass) . '.php';

        if (is_file($coreServiceFile)) {
            require_once $coreServiceFile;
            return;
        }
    }

    if (str_starts_with($class, 'Google\\')) {
        $relativeClass = substr($class, strlen('Google\\'));
        $file = $vendorPath . '/google/apiclient/src/' . str_replace('\\', '/', $relativeClass) . '.php';

        if (is_file($file)) {
            require_once $file;
        }

        return;
    }

    $psrPrefixes = [
        'Psr\\Cache\\' => $vendorPath . '/psr/cache/src/',
        'Psr\\Clock\\' => $vendorPath . '/psr/clock/src/',
        'Psr\\Container\\' => $vendorPath . '/psr/container/src/',
        'Psr\\EventDispatcher\\' => $vendorPath . '/psr/event-dispatcher/src/',
        'Psr\\Http\\Client\\' => $vendorPath . '/psr/http-client/src/',
        'Psr\\Http\\Message\\' => $vendorPath . '/psr/http-message/src/',
        'Psr\\Http\\Factory\\' => $vendorPath . '/psr/http-factory/src/',
        'Psr\\Log\\' => $vendorPath . '/psr/log/src/',
        'Psr\\SimpleCache\\' => $vendorPath . '/psr/simple-cache/src/',
    ];

    foreach ($psrPrefixes as $prefix => $baseDir) {
        if (! str_starts_with($class, $prefix)) {
            continue;
        }

        $relativeClass = substr($class, strlen($prefix));
        $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

        if (is_file($file)) {
            require_once $file;
        }

        return;
    }
});

if (! function_exists('activity_log')) {
    function activity_log(string $action, ?int $userId = null, array $payload = []): void
    {
        \App\Models\ActivityLog::create([
            'user_id' => $userId ?? (auth()->check() ? auth()->id() : null),
            'action'  => $action,
            'payload'  => json_encode($payload, JSON_THROW_ON_ERROR),
        ]);
    }
}

if (! function_exists('localized_route')) {
    function localized_route(
        string $routeName,
        mixed $parameters = [],
        bool $absolute = true
    ): string {
        if (
            $parameters instanceof Model ||
            $parameters instanceof UrlRoutable ||
            is_string($parameters) ||
            is_int($parameters)
        ) {
            $parameters = [$parameters];
        } elseif ($parameters === null) {
            $parameters = [];
        } elseif (! is_array($parameters)) {
            $parameters = [$parameters];
        }

        unset($parameters['locale']);

        return route($routeName, $parameters, $absolute);
    }
}

if (! function_exists('course_thumbnail_relative_path')) {
    function course_thumbnail_relative_path(?string $path): ?string
    {
        $path = trim((string) $path);

        if ($path === '') {
            return null;
        }

        $filename = basename(str_replace('\\', '/', $path));

        if ($filename === '' || $filename === '.' || $filename === '..') {
            return null;
        }

        return 'images/thumbnail/' . $filename;
    }
}

if (! function_exists('course_thumbnail_directories')) {
    function course_thumbnail_directories(): array
    {
        return [
            storage_path('app/public/images/thumbnail'),
            public_path('images/thumbnail'),
        ];
    }
}

if (! function_exists('course_thumbnail_path_candidates')) {
    function course_thumbnail_path_candidates(?string $path): array
    {
        $relativePath = course_thumbnail_relative_path($path);

        if (! $relativePath) {
            return [];
        }

        $filename = basename($relativePath);

        return array_values(array_unique(array_map(
            static fn (string $directory): string => rtrim($directory, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $filename,
            course_thumbnail_directories()
        )));
    }
}

if (! function_exists('course_thumbnail_existing_path')) {
    function course_thumbnail_existing_path(?string $path): ?string
    {
        foreach (course_thumbnail_path_candidates($path) as $candidate) {
            if (is_file($candidate)) {
                return $candidate;
            }
        }

        return null;
    }
}

if (! function_exists('course_thumbnail_url')) {
    function course_thumbnail_url(?string $path): ?string
    {
        $relativePath = course_thumbnail_relative_path($path);

        if (! $relativePath) {
            return null;
        }

        // File uploaded to storage disk (new behaviour)
        if (\Illuminate\Support\Facades\Storage::disk('public')->exists($relativePath)) {
            return \Illuminate\Support\Facades\Storage::disk('public')->url($relativePath);
        }

        // Fallback: legacy files in public/images/thumbnail
        if (is_file(public_path($relativePath))) {
            return asset($relativePath);
        }

        // Default to storage URL even if file not found yet
        return \Illuminate\Support\Facades\Storage::disk('public')->url($relativePath);
    }
}

if (! function_exists('course_thumbnail_files')) {
    function course_thumbnail_files(): array
    {
        $files = [];

        foreach (course_thumbnail_directories() as $directory) {
            if (! File::exists($directory)) {
                continue;
            }

            foreach (File::files($directory) as $file) {
                if (! $file->isFile()) {
                    continue;
                }

                $filename = $file->getFilename();

                if (! array_key_exists($filename, $files) || $file->getMTime() > $files[$filename]->getMTime()) {
                    $files[$filename] = $file;
                }
            }
        }

        return array_values($files);
    }
}
