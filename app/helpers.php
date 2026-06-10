<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Routing\UrlRoutable;

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
