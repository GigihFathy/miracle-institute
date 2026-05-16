<?php

return [
    'sections' => [
        'general' => [
            [
                'label' => 'Dashboard',
                'route' => 'dashboard',
                'roles' => ['student', 'disciples', 'admin'],
                'ability' => null,
            ],
            [
                'label' => 'Courses',
                'route' => 'courses.index',
                'roles' => ['student', 'disciples', 'admin'],
                'ability' => null,
            ],
            [
                'label' => 'Certificates',
                'route' => 'certificates.index',
                'roles' => ['student', 'disciples', 'admin'],
                'ability' => null,
            ],
            [
                'label' => 'Articles',
                'route' => 'articles.index',
                'roles' => ['student', 'disciples', 'admin'],
                'ability' => null,
            ],
        ],

        'learning' => [
            [
                'label' => 'My Learning',
                'route' => 'learning.dashboard',
                'roles' => ['student', 'disciples'],
                'ability' => null,
            ],
            [
                'label' => 'Course Catalog',
                'route' => 'courses.index',
                'roles' => ['student', 'disciples'],
                'ability' => null,
            ],
            [
                'label' => 'Assessment',
                'route' => 'assessments.index',
                'roles' => ['student', 'disciples'],
                'ability' => null,
            ],
        ],

        'admin' => [
            [
                'label' => 'Admin Dashboard',
                'route' => 'admin.dashboard',
                'roles' => ['admin'],
                'ability' => 'view_reports',
            ],
            [
                'label' => 'Study Programs',
                'route' => 'admin.study-programs.index',
                'roles' => ['admin'],
                'ability' => 'manage_courses',
            ],
            [
                'label' => 'Courses',
                'route' => 'admin.courses.index',
                'roles' => ['admin'],
                'ability' => 'manage_courses',
            ],
            [
                'label' => 'Topics',
                'route' => 'admin.topics.index',
                'roles' => ['admin'],
                'ability' => 'manage_topics',
            ],
            [
                'label' => 'Users & Roles',
                'route' => 'admin.users.index',
                'roles' => ['admin'],
                'ability' => 'manage_users',
            ],
            [
                'label' => 'Assessments',
                'route' => 'admin.assessments.index',
                'roles' => ['admin'],
                'ability' => 'manage_assessments',
            ],
            [
                'label' => 'Certificates',
                'route' => 'admin.certificates.index',
                'roles' => ['admin'],
                'ability' => 'manage_certificates',
            ],
            [
                'label' => 'Articles',
                'route' => 'admin.articles.index',
                'roles' => ['admin'],
                'ability' => 'view_reports',
            ],
            [
                'label' => 'Settings',
                'route' => 'admin.settings.index',
                'roles' => ['admin'],
                'ability' => 'view_reports',
            ],
        ],
    ],
];