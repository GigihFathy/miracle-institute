<?php

namespace App\Providers;

use App\Models\Assessment;
use App\Models\Attendance;
use App\Models\Course;
use App\Models\VideoSession;
use App\Models\Material;
use App\Models\Topic;
use App\Models\Certificate;
use App\Models\User;
use App\Models\Role;
use App\Models\AssessmentAttempt;
use App\Models\CourseEnrollment;
use App\Models\TopicProgress;
use App\Models\Permission;
use App\Models\StudyProgram;
use App\Policies\StudyProgramPolicy;
use App\Policies\PermissionPolicy;
use App\Policies\RolePolicy;
use App\Policies\UserPolicy;
use App\Policies\CertificatePolicy;
use App\Policies\AssessmentPolicy;
use App\Policies\AttendancePolicy;
use App\Policies\CoursePolicy;
use App\Policies\MaterialPolicy;
use App\Policies\TopicPolicy;

use App\Observers\CourseEnrollmentObserver;
use App\Observers\TopicProgressObserver;
use App\Observers\AssessmentAttemptObserver;
use App\Observers\AttendanceObserver;
use App\Observers\CertificateObserver;
use App\Observers\VideoSessionObserver;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Course::class, CoursePolicy::class);
        Gate::policy(Topic::class, TopicPolicy::class);
        Gate::policy(Material::class, MaterialPolicy::class);
        Gate::policy(VideoSession::class, AttendancePolicy::class);
        Gate::policy(Assessment::class, AssessmentPolicy::class);
        Gate::policy(Certificate::class, CertificatePolicy::class);
        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(Role::class, RolePolicy::class);
        Gate::policy(Permission::class, PermissionPolicy::class);
        Gate::policy(StudyProgram::class, StudyProgramPolicy::class);


        CourseEnrollment::observe(
            CourseEnrollmentObserver::class
        );

        TopicProgress::observe(
            TopicProgressObserver::class
        );

        AssessmentAttempt::observe(
            AssessmentAttemptObserver::class
        );

        Attendance::observe(
            AttendanceObserver::class
        );

        Certificate::observe(
            CertificateObserver::class
        );

        VideoSession::observe(
            VideoSessionObserver::class
        );

        // Admin | Disciple
        Gate::define('manage_users', fn ($user) => $user->hasPermission('manage_users'));
        Gate::define('manage_courses', fn ($user) => $user->hasPermission('manage_courses'));
        Gate::define('manage_topics', fn ($user) => $user->hasPermission('manage_topics'));
        Gate::define('manage_assessments', fn ($user) => $user->hasPermission('manage_assessments'));
        Gate::define('manage_certificates', fn ($user) => $user->hasPermission('manage_certificates'));
        Gate::define('view_reports', fn ($user) => $user->hasPermission('view_reports'));

        // Student
        Gate::define('take_assessment', fn ($user) => $user->hasPermission('take_assessment'));
        Gate::define('access_topic', fn ($user) => $user->hasPermission('access_topic'));
        Gate::define('enroll_course', fn ($user) => $user->hasPermission('enroll_course'));
    }
}
