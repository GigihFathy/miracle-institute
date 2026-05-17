<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    private const SUPPORTED_LOCALES = ['en', 'id'];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = (string) (
            $request->route('locale')
            ?? session('locale')
            ?? config('app.locale', config('app.fallback_locale', 'en'))
        );

        if (! in_array($locale, self::SUPPORTED_LOCALES, true)) {
            $locale = config('app.fallback_locale', 'en');
        }

        session()->put('locale', $locale);
        App::setLocale($locale);
        Carbon::setLocale($locale);

        URL::defaults([
            'locale' => $locale,
        ]);

        return $next($request);
    }
}