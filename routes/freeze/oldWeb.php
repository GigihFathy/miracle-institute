<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CertificateController;
use App\Livewire\Frontend\LandingPage;
use App\Livewire\Dashboard\MyLearning;
use App\Livewire\Courses\CourseCatalog;
use App\Livewire\Courses\CourseShow;
use App\Livewire\Topics\TopicPlayer;
use App\Livewire\Sessions\AttendanceButton;
use App\Livewire\Assessments\AssessmentTaker;

// Archived route snapshot. Keep references aligned with current classes to avoid stale IDE errors.

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'set.active.role'])->group(function () {

    Route::get('/certificates/{certificateId}/download', [CertificateController::class, 'download'])
    ->name('certificates.download');

    Route::get('/dashboard', function () {
        $activeRole = session('active_role');

        return match ($activeRole) {
            'admin' => redirect()->route('admin.dashboard'),
            'disciples' => redirect()->route('learning.dashboard'),
            default => redirect()->route('learning.dashboard'),
        };
    })->name('dashboard');

    Route::middleware(['role:student,disciples'])->group(function () {
        Route::get('/learning', MyLearning::class)
        ->name('learning.dashboard');

        // Route::get('/courses', CourseCatalog::class)->name('courses.index');
        Route::get('/courses', \App\Livewire\Courses\CourseCatalog::class)
        ->name('courses.index');

        Route::get('/courses/{slug}', CourseShow::class)->name('courses.show');

        Route::get('/topics/{slug}', TopicPlayer::class)
            ->middleware('topic.access:slug')
            ->name('topics.show');

        // Route::get('/sessions/{session}', AttendanceButton::class)
        //     ->middleware(['attendance.window'])
        //     ->name('sessions.show');
        Route::get('/sessions/{session}', \App\Livewire\Sessions\AttendanceButton::class)
        ->middleware('attendance.window')
        ->name('sessions.show');

        // Route::get('/assessments/{assessment}', AssessmentTaker::class)
        //     ->middleware('assessment.access:assessment')
        //     ->name('assessments.take');
        Route::get('/assessments/{assessment}', \App\Livewire\Assessments\AssessmentTaker::class)
        ->middleware('assessment.access:assessment')
        ->name('assessments.take');

        // Archived note: certificate panel no longer exists as a standalone page.
        // Certificates are now accessed from My Learning / download routes.

        Route::view('/articles', 'articles.index')->name('articles.index');
    });

    Route::prefix('admin')
        ->name('admin.')
        ->middleware(['role:admin'])
        ->group(function () {
            Route::view('/dashboard', 'admin.dashboard')->name('dashboard');
            // Route::view('/courses', 'admin.courses.index')->name('courses.index');
            Route::get('/courses', \App\Livewire\Admin\Courses\CourseIndex::class);
            // Route::view('/topics', 'admin.topics.index')->name('topics.index');
            Route::get('/topics', \App\Livewire\Admin\Topics\TopicIndex::class);
            Route::view('/sessions', 'admin.sessions.index')->name('sessions.index');
            // Route::view('/materials', 'admin.materials.index')->name('materials.index');
            Route::get('/materials', \App\Livewire\Admin\Materials\MaterialIndex::class)
            ->name('materials.index');
            Route::view('/users', 'admin.users.index')->name('users.index');
            Route::get('/users/{id}/roles', \App\Http\Livewire\Admin\Users\UserRoleManager::class);
            Route::get('/assessments', \App\Livewire\Admin\Assessments\AssessmentIndex::class);
            Route::get('/assessments/{id}/questions', \App\Livewire\Admin\Assessments\QuestionManager::class);
            // Route::view('/certificates', 'admin.certificates.index')->name('certificates.index');
            Route::get('/certificates', \App\Livewire\Admin\Certificates\CertificateIndex::class);
            Route::view('/articles', 'admin.articles.index')->name('articles.index');
            Route::view('/settings', 'admin.settings.index')->name('settings.index');
            
        });
});
