<?php

declare(strict_types=1);

use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\Auth\GoogleIntegrationController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\CourseThumbnailController;
use App\Http\Controllers\VideoSessionJoinController;




use App\Livewire\Admin\Assessments\AssessmentIndex as AdminAssessmentIndex;
use App\Livewire\Admin\Certificates\CertificateIndex;
use App\Livewire\Admin\Courses\CourseIndex;
use App\Livewire\Admin\Courses\ThumbnailIndex as AdminCourseThumbnailIndex;
use App\Livewire\Admin\Dashboard\DashboardIndex;
use App\Livewire\Admin\Materials\MaterialIndex;
use App\Livewire\Admin\Permissions\PermissionIndex;
use App\Livewire\Admin\Roles\RoleIndex;
use App\Livewire\Admin\Sessions\VideoSessionIndex;
use App\Livewire\Admin\Settings\SettingsIndex;
use App\Livewire\Admin\StudyPrograms\StudyProgramIndex;
use App\Livewire\Admin\Topics\TopicIndex;
use App\Livewire\Admin\Users\UserIndex;
use App\Livewire\Admin\Users\UserRoleManager;

use App\Livewire\Articles\ArticleIndex;
use App\Livewire\Articles\ArticleShow;

use App\Livewire\Assessments\AssessmentIndex;
use App\Livewire\Assessments\AssessmentResult;
use App\Livewire\Assessments\AssessmentTaker;

use App\Livewire\Auth\ForgotPassword;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\Auth\ResetPassword;
use App\Livewire\Auth\VerifyEmailNotice;

use App\Livewire\Courses\CourseCatalog;
use App\Livewire\Courses\CourseShow;

use App\Livewire\Dashboard\ExploreDashboard;
use App\Livewire\Dashboard\MyLearning;

use App\Livewire\Frontend\LandingPage;

use App\Livewire\Mentor\Dashboard\MentorDashboard;
// use App\Livewire\Mentor\Topics\TopicIndex as MentorTopicIndex;
use App\Livewire\Mentor\Topics\TopicWorkspace;
use App\Livewire\Profile\ProfilePage;

use App\Livewire\Topics\TopicPlayer;
use App\Services\RoleService;

use Google\Client as GoogleClient;

use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;


Route::middleware(['auth', 'role.redirect:admin'])->group(function () {
    Route::get('/admin/settings/google-connect', [GoogleIntegrationController::class, 'redirect'])->name('admin.google.connect');
    Route::get('/admin/settings/google-callback', [GoogleIntegrationController::class, 'callback'])->name('admin.google.callback');
});

Route::get('/id/{path?}', function (Request $request, ?string $path = null) {
    $target = '/' . ltrim((string) $path, '/');
    $target = $target === '/' ? '' : $target;

    $query = $request->getQueryString();

    return redirect()->to(($target ?: '/') . ($query ? '?' . $query : ''), 301);
})->where('path', '.*');

Route::get('/en/{path?}', function (Request $request, ?string $path = null) {
    $target = '/' . ltrim((string) $path, '/');
    $target = $target === '/' ? '' : $target;

    $query = $request->getQueryString();

    return redirect()->to(($target ?: '/') . ($query ? '?' . $query : ''), 301);
})->where('path', '.*');

/*
|--------------------------------------------------------------------------
| Non-localized Google OAuth callbacks
|--------------------------------------------------------------------------
|
| These routes keep OAuth callbacks working regardless of locale.
|
*/

Route::middleware('auth')->group(function (): void {
    Route::get('/auth/google/redirect', [GoogleAuthController::class, 'redirect'])
        ->name('google.redirect.fallback');

    Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])
        ->name('google.callback.fallback');
});

/*
|--------------------------------------------------------------------------
| Localized Routes
|--------------------------------------------------------------------------
*/

$registerLocalizedRoutes = function (bool $named): void {
    $nameRoute = static function ($route, string $name) use ($named) {
        return $named ? $route->name($name) : $route;
    };

        /*
        |--------------------------------------------------------------------------
        | Public Routes
        |--------------------------------------------------------------------------
        */

    $nameRoute(Route::get('/', function () {
        if (auth()->check()) {
            $activeRole = session('active_role');

            return match ($activeRole) {
                'admin' => redirect()->to(localized_route('admin.dashboard')),
                'disciples' => redirect()->to(localized_route('mentor.dashboard')),
                default => redirect()->to(localized_route('explore.dashboard')),
            };
        }

        return redirect()->to(localized_route('explore.dashboard'));
    }), 'home');

    $nameRoute(Route::get('/dashboard/explore', ExploreDashboard::class), 'explore.dashboard');

    $nameRoute(Route::get('/courses', CourseCatalog::class), 'courses.index');

    $nameRoute(
        Route::get('/courses/{slug}', CourseShow::class)
            ->middleware(['auth', 'set.active.role']),
        'courses.show'
    );

    $nameRoute(Route::get('/articles', ArticleIndex::class), 'articles.index');

    $nameRoute(Route::get('/articles/{article}', ArticleShow::class), 'articles.show');

        /*
        |--------------------------------------------------------------------------
        | Guest Routes
        |--------------------------------------------------------------------------
        */

    Route::middleware('guest')->group(function () use ($nameRoute): void {
        $nameRoute(Route::get('/login', Login::class), 'login');
        $nameRoute(Route::get('/register', Register::class), 'register');
        $nameRoute(Route::get('/forgot-password', ForgotPassword::class), 'password.request');
        $nameRoute(Route::get('/reset-password/{token}', ResetPassword::class), 'password.reset');
    });

        /*
        |--------------------------------------------------------------------------
        | Auth Routes
        |--------------------------------------------------------------------------
        */

    Route::middleware('auth')->group(function () use ($nameRoute): void {
        $nameRoute(
            Route::get('/sessions/{videoSession}/join', [VideoSessionJoinController::class, 'join'])
                ->middleware(['verified', 'throttle:10,1']),
            'sessions.join'
        );

        $nameRoute(Route::get('/auth/google/redirect', [GoogleAuthController::class, 'redirect']), 'google.redirect');
        $nameRoute(Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback']), 'google.callback');

        $nameRoute(Route::post('/logout', function (Request $request) {
            Auth::logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->to(localized_route('home'));
        }), 'logout');

        $nameRoute(Route::post('/role/switch', function (Request $request, RoleService $roleService) {
            $user = $request->user();

            if (! $user) {
                return redirect()->to(localized_route('login'));
            }

            $validated = $request->validate([
                'role_name' => ['required', 'string'],
            ]);

            $roleService->switchRole($user, $validated['role_name']);

            return redirect()->to(localized_route('redirect.by.role'));
        }), 'role.switch');

        $nameRoute(Route::get('/profile', ProfilePage::class), 'profile.show');
        $nameRoute(Route::get('/email/verify', VerifyEmailNotice::class), 'verification.notice');

        $nameRoute(
            Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
                $request->fulfill();

                event(new Verified($request->user()));

                return redirect()->to(localized_route('redirect.by.role'))
                    ->with('success', __('auth.email_verified_success'));
            })->middleware([
                'signed',
                'throttle:6,1',
            ]),
            'verification.verify'
        );

        $nameRoute(
            Route::post('/email/verification-notification', function (Request $request) {
                if ($request->user()->hasVerifiedEmail()) {
                    return back()->with('status', __('auth.email_already_verified'));
                }

                $request->user()->sendEmailVerificationNotification();

                return back()->with('status', __('auth.verification_link_resent'));
            })->middleware('throttle:6,1'),
            'verification.send'
        );
    });

        /*
        |--------------------------------------------------------------------------
        | Protected Routes
        |--------------------------------------------------------------------------
        */

    Route::middleware([
        'auth',
        'verified',
        'set.active.role',
    ])->group(function () use ($nameRoute, $named): void {
            /*
            |--------------------------------------------------------------------------
            | Safe Role Redirect
            |--------------------------------------------------------------------------
            */

        $nameRoute(Route::get('/redirect-by-role', function () {
            if (! auth()->check()) {
                return redirect()->to(localized_route('login'));
            }

            $activeRole = session('active_role');

            return match ($activeRole) {
                'admin' => redirect()->to(localized_route('admin.dashboard')),
                'disciples' => redirect()->to(localized_route('mentor.dashboard')),
                default => redirect()->to(localized_route('explore.dashboard')),
            };
        }), 'redirect.by.role');

            /*
            |--------------------------------------------------------------------------
            | Dashboard Redirect
            |--------------------------------------------------------------------------
            */

        $nameRoute(Route::get('/dashboard', function () {
            return redirect()->to(localized_route('redirect.by.role'));
        }), 'dashboard');

            /*
            |--------------------------------------------------------------------------
            | Certificates
            |--------------------------------------------------------------------------
            */

        $nameRoute(Route::get('/courses/{courseId}/claim-certificate', [
            CertificateController::class,
            'claimCourse',
        ]), 'certificates.course.claim');

        $nameRoute(Route::get('/certificates/{certificateId}/download', [
            CertificateController::class,
            'download',
        ]), 'certificates.download');

            /*
            |--------------------------------------------------------------------------
            | Mentor Routes
            |--------------------------------------------------------------------------
            */

        $mentorRoutes = Route::prefix('mentor')
            ->middleware([
                'role.redirect:disciples',
            ]);

        if ($named) {
            $mentorRoutes->name('mentor.');
        }

        $mentorRoutes->group(function () use ($nameRoute): void {
            $nameRoute(
                Route::get('/dashboard', MentorDashboard::class)
                    ->middleware('permission:manage_topics'),
                'dashboard'
            );

            $nameRoute(
                Route::get('/assessments/{courseFilter}', AdminAssessmentIndex::class)
                    ->whereUuid('courseFilter')
                    ->middleware('permission:manage_assessments'),
                'assessments.index'
            );

            $nameRoute(
                Route::get('/topics/{slug}', TopicWorkspace::class)
                    ->middleware('permission:manage_topics'),
                'topics.show'
            );
        });

            /*
            |--------------------------------------------------------------------------
            | Student / Learning Routes
            |--------------------------------------------------------------------------
            */

        Route::middleware([
            'role.redirect:student,disciples',
        ])->group(function () use ($nameRoute): void {
            $nameRoute(Route::get('/learning', MyLearning::class), 'learning.dashboard');
            $nameRoute(
                Route::get('/topics/{slug}', TopicPlayer::class)
                    ->middleware('topic.access:slug'),
                'topics.show'
            );
            $nameRoute(Route::get('/assessments', AssessmentIndex::class), 'assessments.index');
            $nameRoute(
                Route::get('/assessments/{assessment}', AssessmentTaker::class)
                    ->middleware('assessment.access:assessment'),
                'assessments.take'
            );
            $nameRoute(Route::get('/assessment-attempts/{attempt}/result', AssessmentResult::class), 'assessments.result');
        });

            /*
            |--------------------------------------------------------------------------
            | Admin Routes
            |--------------------------------------------------------------------------
            */

        $adminRoutes = Route::prefix('admin')
            ->middleware([
                'role.redirect:admin',
            ]);

        if ($named) {
            $adminRoutes->name('admin.');
        }

        $adminRoutes->group(function () use ($nameRoute): void {
                    $nameRoute(
                        Route::get('/dashboard', DashboardIndex::class)
                            ->middleware('permission:view_reports'),
                        'dashboard'
                    );

                    $nameRoute(
                        Route::get('/study-programs', StudyProgramIndex::class)
                            ->middleware('permission:manage_courses'),
                        'study-programs.index'
                    );

                    $nameRoute(
                        Route::get('/courses', CourseIndex::class)
                            ->middleware('permission:manage_courses'),
                        'courses.index'
                    );

                    $nameRoute(
                        Route::get('/courses/thumbnails', AdminCourseThumbnailIndex::class)
                            ->middleware('permission:manage_courses'),
                        'courses.thumbnails'
                    );

                    $nameRoute(
                        Route::post('/courses/thumbnails', [CourseThumbnailController::class, 'store'])
                            ->middleware('permission:manage_courses'),
                        'courses.thumbnails.store'
                    );

                    $nameRoute(Route::get('/topics', function () {
                        $courseFilter = request()->query('courseFilter');

                        if ($courseFilter) {
                            return redirect(localized_route('admin.topics.index', ['courseFilter' => $courseFilter]));
                        }

                        return redirect(localized_route('admin.courses.index'));
                    })->middleware('permission:manage_topics'), 'topics.legacy');

                    $nameRoute(
                        Route::get('/topics/{courseFilter}', TopicIndex::class)
                            ->whereUuid('courseFilter')
                            ->middleware('permission:manage_topics'),
                        'topics.index'
                    );

                    $nameRoute(Route::get('/materials', function () {
                        $topicFilter = request()->query('topicFilter');

                        if ($topicFilter) {
                            return redirect(localized_route('admin.materials.index', ['topicFilter' => $topicFilter]));
                        }

                        return redirect(localized_route('admin.topics.index'));
                    })->middleware('permission:manage_topics'), 'materials.legacy');

                    $nameRoute(
                        Route::get('/materials/{topicFilter}', MaterialIndex::class)
                            ->whereUuid('topicFilter')
                            ->middleware('permission:manage_topics'),
                        'materials.index'
                    );

                    $nameRoute(Route::get('/sessions', function () {
                        $topicFilter = request()->query('topicFilter');

                        if ($topicFilter) {
                            return redirect(localized_route('admin.sessions.index', ['topicFilter' => $topicFilter]));
                        }

                        return redirect(localized_route('admin.topics.index'));
                    })->middleware('permission:manage_topics'), 'sessions.legacy');

                    $nameRoute(
                        Route::get('/sessions/{topicFilter}', VideoSessionIndex::class)
                            ->whereUuid('topicFilter')
                            ->middleware('permission:manage_topics'),
                        'sessions.index'
                    );

                    $nameRoute(Route::get('/attendances', function () {
                        $topicFilter = request()->query('topicFilter');
                        $sessionFilter = request()->query('sessionFilter');

                        if ($topicFilter) {
                            return redirect(localized_route('admin.sessions.index', ['topicFilter' => $topicFilter]));
                        }

                        if ($sessionFilter) {
                            $session = \App\Models\VideoSession::query()->whereKey($sessionFilter)->first();

                            if ($session) {
                                return redirect(localized_route('admin.sessions.index', ['topicFilter' => $session->topic_id]));
                            }
                        }

                        return redirect(localized_route('admin.topics.index'));
                    })->middleware('permission:view_reports'), 'attendances.legacy');

                    $nameRoute(Route::get('/attendances/{topicFilter}', function (string $topicFilter) {
                        return redirect(localized_route('admin.sessions.index', ['topicFilter' => $topicFilter]));
                    })->whereUuid('topicFilter')->middleware('permission:view_reports'), 'attendances.index');

                    $nameRoute(
                        Route::get('/users', UserIndex::class)
                            ->middleware('permission:manage_users'),
                        'users.index'
                    );

                    $nameRoute(
                        Route::get('/users/{userId}/roles', UserRoleManager::class)
                            ->middleware('permission:manage_users'),
                        'users.roles'
                    );

                    $nameRoute(Route::get('/assessments', function () {
                        $courseFilter = request()->query('courseFilter');

                        if ($courseFilter) {
                            return redirect(localized_route('admin.assessments.index', ['courseFilter' => $courseFilter]));
                        }

                        return redirect(localized_route('admin.topics.index'));
                    })->middleware('permission:manage_assessments'), 'assessments.legacy');

                    $nameRoute(
                        Route::get('/assessments/{courseFilter}', AdminAssessmentIndex::class)
                            ->whereUuid('courseFilter')
                            ->middleware('permission:manage_assessments'),
                        'assessments.index'
                    );

                    // Question manager moved into assessments index; legacy route removed.

                    $nameRoute(Route::get('/certificates', function () {
                        $courseFilter = request()->query('courseFilter');

                        if ($courseFilter) {
                            return redirect(localized_route('admin.certificates.index', ['courseFilter' => $courseFilter]));
                        }

                        return redirect(localized_route('admin.courses.index'));
                    })->middleware('permission:manage_certificates'), 'certificates.legacy');

                    $nameRoute(
                        Route::get('/certificates/{courseFilter}', CertificateIndex::class)
                            ->whereUuid('courseFilter')
                            ->middleware('permission:manage_certificates'),
                        'certificates.index'
                    );

                    $nameRoute(
                        Route::get('/articles', \App\Livewire\Admin\Articles\ArticleIndex::class)
                            ->middleware('permission:view_reports'),
                        'articles.index'
                    );

                    $nameRoute(
                        Route::get('/articles/create', \App\Livewire\Admin\Articles\ArticleForm::class)
                            ->middleware('permission:view_reports'),
                        'articles.create'
                    );

                    $nameRoute(
                        Route::get('/articles/{article}/edit', \App\Livewire\Admin\Articles\ArticleForm::class)
                            ->middleware('permission:view_reports'),
                        'articles.edit'
                    );

                    $nameRoute(
                        Route::get('/settings', SettingsIndex::class)
                            ->middleware('permission:view_reports'),
                        'settings.index'
                    );

                    $nameRoute(
                        Route::get('/roles', RoleIndex::class)
                            ->middleware('permission:view_reports'),
                        'roles.index'
                    );

                    $nameRoute(
                        Route::get('/permissions', PermissionIndex::class)
                            ->middleware('permission:manage_users'),
                        'permissions.index'
                    );
                });
    });
};

Route::group([], fn () => $registerLocalizedRoutes(true));
