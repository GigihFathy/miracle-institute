<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Routing\UrlRoutable;

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
        $supportedLocales = ['en', 'id'];

        $locale = app()->getLocale();
        if (! is_string($locale) || ! in_array($locale, $supportedLocales, true)) {
            $locale = session('locale', config('app.fallback_locale', config('app.locale', 'en')));
        }

        if (! in_array($locale, $supportedLocales, true)) {
            $locale = config('app.fallback_locale', 'en');
        }

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

        if (! array_key_exists('locale', $parameters)) {
            $parameters = ['locale' => $locale] + $parameters;
        }

        return route($routeName, $parameters, $absolute);
    }
}