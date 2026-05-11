<?php

use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\VideoSessionJoinController;

use App\Livewire\Admin\Assessments\AssessmentIndex as AdminAssessmentIndex;
use App\Livewire\Admin\Assessments\QuestionManager;
use App\Livewire\Admin\Attendances\AttendanceIndex;
use App\Livewire\Admin\Certificates\CertificateIndex;
use App\Livewire\Admin\Courses\CourseIndex;
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

use App\Livewire\Certificates\CertificatePanel;

use App\Livewire\Courses\CourseCatalog;
use App\Livewire\Courses\CourseShow;

use App\Livewire\Dashboard\ExploreDashboard;
use App\Livewire\Dashboard\MyLearning;

use App\Livewire\Frontend\LandingPage;

use App\Livewire\Mentor\Dashboard\MentorDashboard;
use App\Livewire\Mentor\Topics\TopicIndex as MentorTopicIndex;
use App\Livewire\Mentor\Topics\TopicWorkspace;

use App\Livewire\Topics\TopicPlayer;

use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

if (auth()->check() && auth()->user()->Str::lower(role) === 'admin') {
    Route::get('/', function () {
        return redirect()->route('admin.dashboard');
    });
}

Route::get('/', function () {
    return redirect()->route('explore.dashboard');
})->name('home');

Route::get('/dashboard/explore', ExploreDashboard::class)
    ->name('explore.dashboard');

Route::get('/courses', CourseCatalog::class)
    ->name('courses.index');

Route::get('/courses/{slug}', CourseShow::class)
    ->name('courses.show');

Route::get('/articles', ArticleIndex::class)
    ->name('articles.index');

Route::get('/articles/{article}', ArticleShow::class)
    ->name('articles.show');

/*
|--------------------------------------------------------------------------
| Guest Routes
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {

    Route::get('/login', Login::class)
        ->name('login');

    Route::get('/register', Register::class)
        ->name('register');

    Route::get('/forgot-password', ForgotPassword::class)
        ->name('password.request');

    Route::get('/reset-password/{token}', ResetPassword::class)
        ->name('password.reset');
});

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::get('/sessions/{videoSession}/join', [VideoSessionJoinController::class, 'join'])
        ->middleware(['auth', 'verified', 'throttle:10,1'])
        ->name('sessions.join');

    Route::post('/logout', function (Request $request) {

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');

    })->name('logout');

    Route::get('/email/verify', VerifyEmailNotice::class)
        ->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {

        $request->fulfill();

        event(new Verified($request->user()));

        return redirect()->route('redirect.by.role')
            ->with('success', 'Email berhasil diverifikasi.');

    })->middleware([
        'signed',
        'throttle:6,1'
    ])->name('verification.verify');

    Route::post('/email/verification-notification', function (Request $request) {

        if ($request->user()->hasVerifiedEmail()) {
            return back()->with('status', 'Email sudah terverifikasi.');
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('status', 'Link verifikasi sudah dikirim ulang.');

    })->middleware('throttle:6,1')
        ->name('verification.send');
});

/*
|--------------------------------------------------------------------------
| Protected Routes
|--------------------------------------------------------------------------
*/

Route::middleware([
    'auth',
    'verified',
    'set.active.role'
])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Safe Role Redirect
    |--------------------------------------------------------------------------
    */

    Route::get('/redirect-by-role', function () {

        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $activeRole = session('active_role');

        return match ($activeRole) {

            'admin' => redirect()->route('admin.dashboard'),

            'disciples' => redirect()->route('mentor.dashboard'),

            default => redirect()->route('explore.dashboard'),
        };

    })->name('redirect.by.role');

    /*
    |--------------------------------------------------------------------------
    | Dashboard Redirect
    |--------------------------------------------------------------------------
    */

    Route::get('/dashboard', function () {
        return redirect()->route('redirect.by.role');
    })->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | Certificates
    |--------------------------------------------------------------------------
    */

    Route::get('/courses/{course}/claim-certificate', [
        CertificateController::class,
        'claimCourse'
    ])->name('certificates.course.claim');

    Route::get('/certificates/{certificate}/download', [
        CertificateController::class,
        'download'
    ])->name('certificates.download');

    /*
    |--------------------------------------------------------------------------
    | Mentor Routes
    |--------------------------------------------------------------------------
    */

    Route::prefix('mentor')
        ->name('mentor.')
        ->middleware([
            'role.redirect:disciples'
        ])
        ->group(function () {

            Route::get('/dashboard', MentorDashboard::class)
                ->middleware('permission:manage_topics')
                ->name('dashboard');

            Route::get('/topics', MentorTopicIndex::class)
                ->middleware('permission:manage_topics')
                ->name('topics.index');

            Route::get('/topics/{slug}', TopicWorkspace::class)
                ->middleware('permission:manage_topics')
                ->name('topics.show');
        });

    /*
    |--------------------------------------------------------------------------
    | Student / Learning Routes
    |--------------------------------------------------------------------------
    */

    Route::middleware([
        'role.redirect:student,disciples'
    ])->group(function () {

        Route::get('/learning', MyLearning::class)
            ->name('learning.dashboard');

        Route::get('/topics/{slug}', TopicPlayer::class)
            ->middleware('topic.access:slug')
            ->name('topics.show');

        Route::get('/assessments', AssessmentIndex::class)
            ->name('assessments.index');

        Route::get('/assessments/{assessment}', AssessmentTaker::class)
            ->middleware('assessment.access:assessment')
            ->name('assessments.take');
            
        Route::get('/assessment-attempts/{attempt}/result', AssessmentResult::class)
            ->name('assessments.result');

        Route::get('/certificates', CertificatePanel::class)
            ->name('certificates.index');
    });

    /*
    |--------------------------------------------------------------------------
    | Admin Routes
    |--------------------------------------------------------------------------
    */

    Route::prefix('admin')
        ->name('admin.')
        ->middleware([
            'role.redirect:admin'
        ])
        ->group(function () {

            Route::get('/dashboard', DashboardIndex::class)
                ->middleware('permission:view_reports')
                ->name('dashboard');

            Route::get('/study-programs', StudyProgramIndex::class)
                ->middleware('permission:manage_courses')
                ->name('study-programs.index');

            Route::get('/courses', CourseIndex::class)
                ->middleware('permission:manage_courses')
                ->name('courses.index');

            Route::get('/topics', TopicIndex::class)
                ->middleware('permission:manage_topics')
                ->name('topics.index');

            Route::get('/materials', MaterialIndex::class)
                ->middleware('permission:manage_topics')
                ->name('materials.index');

            Route::get('/sessions', VideoSessionIndex::class)
                ->middleware('permission:manage_topics')
                ->name('sessions.index');

            Route::get('/attendances', AttendanceIndex::class)
                ->middleware('permission:view_reports')
                ->name('attendances.index');

            Route::get('/users', UserIndex::class)
                ->middleware('permission:manage_users')
                ->name('users.index');

            Route::get('/users/{userId}/roles', UserRoleManager::class)
                ->middleware('permission:manage_users')
                ->name('users.roles');

            Route::get('/assessments', AdminAssessmentIndex::class)
                ->middleware('permission:manage_assessments')
                ->name('assessments.index');

            Route::get('/assessments/{assessmentId}/questions', QuestionManager::class)
                ->middleware('permission:manage_assessments')
                ->name('assessments.questions');

            Route::get('/certificates', CertificateIndex::class)
                ->middleware('permission:manage_certificates')
                ->name('certificates.index');

            Route::get('/articles', \App\Livewire\Admin\Articles\ArticleIndex::class)
                ->middleware('permission:view_reports')
                ->name('articles.index');

            Route::get('/articles/create', \App\Livewire\Admin\Articles\ArticleForm::class)
                ->middleware('permission:view_reports')
                ->name('articles.create');

            Route::get('/articles/{article}/edit', \App\Livewire\Admin\Articles\ArticleForm::class)
                ->middleware('permission:view_reports')
                ->name('articles.edit');

            Route::get('/settings', SettingsIndex::class)
                ->middleware('permission:view_reports')
                ->name('settings.index');

            Route::get('/roles', RoleIndex::class)
                ->middleware('permission:view_reports')
                ->name('roles.index');

            Route::get('/permissions', PermissionIndex::class)
                ->middleware('permission:view_reports')
                ->name('permissions.index');
        });
});

/*
|--------------------------------------------------------------------------
| Google OAuth
|--------------------------------------------------------------------------
*/

Route::get('/auth/google/redirect', [
    GoogleAuthController::class,
    'redirect'
])->middleware('guest')
    ->name('auth.google.redirect');

Route::get('/auth/google/callback', [
    GoogleAuthController::class,
    'callback'
])->middleware('guest')
    ->name('auth.google.callback');