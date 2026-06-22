<?php

namespace Database\Seeders;

use Database\Seeders\Concerns\TruncatesTables;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use TruncatesTables;

    public function run(): void
    {
        $this->truncateSeedTables([
            'role_user',
            'role_permission',
            'permissions',
            'roles',
            'assessment_answers',
            'assessment_attempts',
            'question_options',
            'questions',
            'assessments',
            'certificates',
            'attendances',
            'material_progresses',
            'topic_progresses',
            'course_enrollments',
            'materials',
            'sessions',
            'topics',
            'courses',
            'articles',
            'article_images',
            'tutorial_videos',
            'companies',
            'social_accounts',
            'users',
        ]);

        $this->call([
            Auth\RoleSeeder::class,
            Auth\PermissionSeeder::class,
            Auth\RolePermissionSeeder::class,
            Auth\UserSeeder::class,

            Learning\CourseSeeder::class,
            Learning\TopicSeeder::class,
            Learning\VideoSessionSeeder::class,
            Learning\MaterialSeeder::class,
            Learning\EnrollmentSeeder::class,
            Learning\ProgressSeeder::class,
            Learning\AttendanceSeeder::class,

            Assessment\AssessmentSeeder::class,
            Assessment\QuestionSeeder::class,
            Assessment\AttemptSeeder::class,

            Certificate\CertificateSeeder::class,
            Cms\CmsSeeder::class,
        ]);
    }
}
