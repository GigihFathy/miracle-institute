<?php

declare(strict_types=1);

use App\Http\Middleware\Authenticate;
use Illuminate\Http\Request;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(
            at: '*',
            headers: Request::HEADER_X_FORWARDED_FOR
                | Request::HEADER_X_FORWARDED_HOST
                | Request::HEADER_X_FORWARDED_PORT
                | Request::HEADER_X_FORWARDED_PROTO
                | Request::HEADER_X_FORWARDED_PREFIX
                | Request::HEADER_X_FORWARDED_AWS_ELB
        );

        $middleware->web(append: [
            \App\Http\Middleware\SetLocale::class,
        ]);

        $middleware->redirectUsersTo(fn (): string => localized_route('redirect.by.role'));

        $middleware->alias([
            'auth' => Authenticate::class,

            'setlocale' => \App\Http\Middleware\SetLocale::class,
            'set.active.role' => \App\Http\Middleware\SetActiveRole::class,
            'role' => \App\Http\Middleware\EnsureRole::class,
            'course.enrolled' => \App\Http\Middleware\EnsureCourseEnrollment::class,
            'topic.access' => \App\Http\Middleware\EnsureTopicAccess::class,
            'assessment.access' => \App\Http\Middleware\EnsureAssessmentAccess::class,
            'attendance.window' => \App\Http\Middleware\EnsureAttendanceWindow::class,
            'permission' => \App\Http\Middleware\CheckPermission::class,
            'role.redirect' => \App\Http\Middleware\RedirectIfRoleMismatch::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->create();
