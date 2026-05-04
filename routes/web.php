<?php

use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\CertificateDownloadController;
use App\Http\Controllers\CertificateClaimController;
use App\Livewire\Auth\ForgotPassword;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\Auth\ResetPassword;
use App\Livewire\Auth\VerifyEmailNotice;
use App\Livewire\Assessments\AssessmentIndex;
use App\Livewire\Assessments\AssessmentTaker;
use App\Livewire\Assessments\AssessmentResult;
use App\Livewire\Articles\ArticleIndex;
use App\Livewire\Articles\ArticleShow;
use App\Livewire\Certificates\CertificatePanel;
use App\Livewire\Courses\CourseCatalog;
use App\Livewire\Courses\MyCourses;
use App\Livewire\Courses\CourseShow;
use App\Livewire\Dashboard\ExploreDashboard;
use App\Livewire\Dashboard\MyLearning;
use App\Livewire\Frontend\LandingPage;
use App\Livewire\Sessions\AttendanceButton;
use App\Livewire\Topics\TopicPlayer;
use App\Livewire\Mentor\Dashboard\MentorDashboard;
use App\Livewire\Mentor\Topics\TopicIndex as MentorTopicIndex;
use App\Livewire\Mentor\Topics\TopicWorkspace;
use App\Livewire\Admin\Assessments\AssessmentIndex as AdminAssessmentIndex;
use App\Livewire\Admin\Assessments\QuestionManager;
use App\Livewire\Admin\Certificates\CertificateIndex;
use App\Livewire\Admin\Courses\CourseIndex;
use App\Livewire\Admin\Materials\MaterialIndex;
use App\Livewire\Admin\StudyPrograms\StudyProgramIndex;
use App\Livewire\Admin\Topics\TopicIndex;
use App\Livewire\Admin\Users\UserRoleManager;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('explore.dashboard');
})->name('home');

Route::get('/dashboard/explore', ExploreDashboard::class)->name('explore.dashboard');

Route::get('/courses', CourseCatalog::class)->name('courses.index');
Route::get('/courses/{slug}', CourseShow::class)->name('courses.show');

Route::get('/articles', ArticleIndex::class)->name('articles.index');
Route::get('/articles/{article}', ArticleShow::class)->name('articles.show');

Route::middleware('guest')->group(function () {
    Route::get('/login', Login::class)->name('login');
    Route::get('/register', Register::class)->name('register');
    Route::get('/forgot-password', ForgotPassword::class)->name('password.request');
    Route::get('/reset-password/{token}', ResetPassword::class)->name('password.reset');
});

Route::middleware('auth')->group(function () {
    // if (!auth()->user()->hasRole(session('active_role'))) {
    //     return redirect()->route('dashboard');
    // }

    Route::post('/logout', function (Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    })->name('logout');

    Route::get('/email/verify', VerifyEmailNotice::class)->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();
        event(new Verified($request->user()));

        return redirect()->route('dashboard')->with('success', 'Email berhasil diverifikasi.');
    })->middleware(['signed', 'throttle:6,1'])->name('verification.verify');

    Route::post('/email/verification-notification', function (Request $request) {
        if ($request->user()->hasVerifiedEmail()) {
            return back()->with('status', 'Email sudah terverifikasi.');
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('status', 'Link verifikasi sudah dikirim ulang.');
    })->middleware('throttle:6,1')->name('verification.send');
});

Route::middleware(['auth', 'verified', 'set.active.role'])->group(function () {
    Route::get('/dashboard', function () {
        $activeRole = session('active_role');

        if ($activeRole === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        if ($activeRole === 'disciples') {
            return redirect()->route('mentor.dashboard');
        }

        return redirect()->route('explore.dashboard');
    })->name('dashboard');

    Route::prefix('mentor')
    ->name('mentor.')
    ->middleware(['role:disciples'])
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

    Route::middleware(['role:student,disciples'])->group(function () {

        Route::get('/learning', MyLearning::class)->name('learning.dashboard');

        Route::get('/topics/{slug}', TopicPlayer::class)
            ->middleware('topic.access:slug')
            ->name('topics.show');

        Route::get('/assessments', AssessmentIndex::class)->name('assessments.index');

        Route::get('/assessments/{assessment}', AssessmentTaker::class)
            ->middleware('assessment.access:assessment')
            ->name('assessments.take');

        Route::get('/assessments/{attempt}/result', AssessmentResult::class)
            ->name('assessments.result');

        Route::get('/certificates', CertificatePanel::class)
            ->name('certificates.index');

        Route::get('/certificates/{certificate}/download', CertificateDownloadController::class)
            ->name('certificates.download');

        Route::get('/certificates/course/{course}/claim', [CertificateClaimController::class, 'course'])
            ->name('certificates.course.claim');

        Route::get('/certificates/topic/{topic}/claim', [CertificateClaimController::class, 'topic'])
            ->name('certificates.topic.claim');
    });

    Route::prefix('admin')
    ->name('admin.')
    ->middleware(['role:admin'])
    ->group(function () {
        Route::get('/dashboard', \App\Livewire\Admin\Dashboard\DashboardIndex::class)
            ->middleware('permission:view_reports')
            ->name('dashboard');

        Route::get('/study-programs', \App\Livewire\Admin\StudyPrograms\StudyProgramIndex::class)
            ->middleware('permission:manage_courses')
            ->name('study-programs.index');

        Route::get('/courses', \App\Livewire\Admin\Courses\CourseIndex::class)
            ->middleware('permission:manage_courses')
            ->name('courses.index');

        Route::get('/topics', \App\Livewire\Admin\Topics\TopicIndex::class)
            ->middleware('permission:manage_topics')
            ->name('topics.index');

        Route::get('/materials', \App\Livewire\Admin\Materials\MaterialIndex::class)
            ->middleware('permission:manage_topics')
            ->name('materials.index');

        Route::get('/users', \App\Livewire\Admin\Users\UserIndex::class)
            ->middleware('permission:manage_users')
            ->name('users.index');

        Route::get('/users/{userId}/roles', \App\Livewire\Admin\Users\UserRoleManager::class)
            ->middleware('permission:manage_users')
            ->name('users.roles');

        Route::get('/assessments', \App\Livewire\Admin\Assessments\AssessmentIndex::class)
            ->middleware('permission:manage_assessments')
            ->name('assessments.index');

        Route::get('/assessments/{assessmentId}/questions', \App\Livewire\Admin\Assessments\QuestionManager::class)
            ->middleware('permission:manage_assessments')
            ->name('assessments.questions');

        Route::get('/certificates', \App\Livewire\Admin\Certificates\CertificateIndex::class)
            ->middleware('permission:manage_certificates')
            ->name('certificates.index');

        Route::get('/articles', \App\Livewire\Admin\Articles\ArticleIndex::class)
            ->middleware('permission:view_reports')
            ->name('articles.index');

        Route::get('/settings', \App\Livewire\Admin\Settings\SettingsIndex::class)
            ->name('settings.index');

        Route::get('/roles', \App\Livewire\Admin\Roles\RoleIndex::class)
            ->name('roles.index');

        Route::get('/permissions', \App\Livewire\Admin\Permissions\PermissionIndex::class)
            ->name('permissions.index');

        Route::get('/audit', \App\Livewire\Admin\Audit\AuditIndex::class)
            ->name('audit.index');
    });
});

Route::get('/auth/google/redirect', [GoogleAuthController::class, 'redirect'])->middleware('guest')->name('auth.google.redirect');
Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])->middleware('guest')->name('auth.google.callback');