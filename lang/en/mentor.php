<?php

return [
    'dashboard' => [
        'page_title' => 'Mentor Dashboard',
        'page_subtitle' => 'A quick summary for managing topics, materials, and learning progress.',

        'stats' => [
            'topics' => 'Topics',
            'topics_hint' => 'Topics you manage',
            'materials' => 'Materials',
            'materials_hint' => 'Materials you uploaded',
            'students' => 'Students',
            'students_hint' => 'Connected students',
        ],

        'managed_courses' => [
            'title' => 'Managed Courses',
            'subtitle' => 'List of courses you can manage.',
            'search_placeholder' => 'Search managed courses...',
            'no_course' => 'No Course',
            'topic_count' => '{0} no topic|{1} :count topic|[2,*] :count topics',
            'active_badge' => 'Active',
            'hide' => 'Hide',
            'show' => 'Show',
            'manage' => 'Manage',
            'empty' => 'No topics yet under your management.',
            'not_found' => 'No managed courses match your search.',
        ],

        'recent_materials' => [
            'title' => 'Recent Materials',
            'subtitle' => 'The latest materials you added.',
            'empty' => 'No materials yet.',
        ],
    ],

    'topics' => [
        'index' => [
            'page_title' => 'Mentored Topics',
            'page_subtitle' => 'List of topics you can manage.',
            'back' => 'Back',
            'search_placeholder' => 'Search topic...',
            'open' => 'Open',
            'empty' => [
                'title' => 'No topics yet',
                'description' => 'You are not a mentor in any topic yet.',
            ],
            'metrics' => [
                'materials' => 'Materials',
                'students' => 'Students',
                'active' => 'ACTIVE',
                'inactive' => 'INACTIVE',
                'assessment' => 'Assessment',
            ],
        ],
    ],

    'topic_workspace' => [
        'header' => 'Mentor Workspace',
        'subtitle' => 'A compact workspace to manage materials, sessions, attendance, collaborators, and assessment.',
        'visit_topic' => 'Visit Topic',
        'cards' => [
            'topic' => 'Topic',
            'course' => 'Course',
            'program' => 'Program',
        ],
    ],

    'topic_tabs' => [
        'overview' => [
            'title' => 'Topic Overview',
            'no_description' => 'No topic description yet.',
            'access' => [
                'materials' => 'Materials Access',
                'sessions' => 'Sessions Access',
                'students' => 'Students Access',
            ],
            'cards' => [
                'category' => 'Category',
                'visibility' => 'Visibility',
                'materials' => 'Materials',
                'session_status' => 'Session Status',
            ],
        ],

        'materials' => [
            'selected' => [
                'title' => 'Selected Material',
                'subtitle' => 'Preview and detail information for the selected material.',
            ],
            'actions' => [
                'edit' => 'Edit',
                'delete' => 'Delete',
                'watch_youtube' => 'Watch on YouTube',
                'open_download' => 'Open / Download Document',
            ],
            'thumbnail_alt' => 'Thumbnail :name',
            'thumbnail_unavailable' => 'Thumbnail not available',
            'youtube_hint' => 'Video will open in a new tab to avoid restriction errors.',
            'no_preview' => 'This material does not have a preview yet.',
            'empty_selected' => [
                'title' => 'No Material Selected Yet',
                'subtitle' => 'Please select a material from the list on the right to view details and preview.',
            ],
            'list' => [
                'title' => 'Materials List',
                'actions' => [
                    'add' => 'Add Material',
                ],
                'limit_reached' => 'Limit reached',
                'empty' => 'No materials yet.',
            ],
            'modal' => [
                'add_title' => 'Add Material',
                'edit_title' => 'Edit Material',
                'subtitle' => 'Use external path for Google Drive or YouTube.',
            ],
            'form' => [
                'name' => 'Material Name',
                'name_placeholder' => 'Material name',
                'type' => 'Type',
                'select' => 'Select',
                'no_types_left' => 'All types are already used in this topic.',
                'status' => 'Status',
                'status_active' => 'Active',
                'status_inactive' => 'Inactive',
                'file' => 'Material File',
                'external_url' => 'External URL',
                'external_url_placeholder' => 'YouTube URL / video ID',
                'sort_order' => 'Sort Order',
                'cancel' => 'Cancel',
                'save' => 'Save Material',
                'update' => 'Update Material',
            ],
        ],

        'sessions' => [
            'title' => 'Sessions',
            'subtitle' => 'Online sessions with students.',
            'actions' => [
                'edit' => 'Edit',
                'add' => 'Add Session',
            ],
            'zoom' => 'Zoom',
            'open_link' => 'Open link',
            'empty' => 'No session yet.',
            'modal' => [
                'add_title' => 'Add Session',
                'edit_title' => 'Edit Session',
                'subtitle' => 'Session for the current topic.',
            ],
            'form' => [
                'title' => 'Title',
                'title_placeholder' => 'Session title',
                'start_at' => 'Start At',
                'end_at' => 'End At',
                'zoom_link' => 'Zoom Link',
                'status' => 'Status',
                'cancel' => 'Cancel',
                'save' => 'Save Session',
                'update' => 'Update Session',
            ],
            'status' => [
                'scheduled' => 'Scheduled',
                'ongoing' => 'Ongoing',
                'completed' => 'Completed',
                'cancelled' => 'Cancelled',
            ],
        ],

        'attendances' => [
            'title' => 'Attendances',
            'subtitle' => 'Attendance recap based on topic sessions.',
            'manager_badge' => 'Attendance Manager',
            'filters' => [
                'search_placeholder' => 'Search student or email...',
                'all_status' => 'All statuses',
            ],
            'stats' => [
                'present' => 'Present :count',
                'late' => 'Late :count',
                'absent' => 'Absent :count',
            ],
            'status' => [
                'present' => 'Present',
                'late' => 'Late',
                'absent' => 'Absent',
            ],
            'table' => [
                'session' => 'Session',
                'student' => 'Student',
                'status' => 'Status',
                'check_in' => 'Check In',
                'check_out' => 'Check Out',
                'empty_session' => 'No attendance yet.',
            ],
            'empty' => [
                'title' => 'Attendance not available yet',
                'description' => 'No active session has been created or no student attendance data exists yet.',
            ],
        ],

        'students' => [
            'title' => 'Students',
            'subtitle' => 'Track student progress for this topic.',
            'manager_badge' => 'Student Manager',
            'table' => [
                'student' => 'Student',
                'progress' => 'Progress',
                'status' => 'Status',
            ],
            'empty' => [
                'title' => 'No students yet',
                'description' => 'Student enrollment for this topic is still empty.',
            ],
        ],

        'collaborators' => [
            'title' => 'Collaborators',
            'subtitle' => 'Main mentor and collaborators for this topic.',
            'actions' => [
                'invite' => 'Invite Collaborator',
                'edit' => 'Edit',
                'remove' => 'Remove',
            ],
            'owner' => 'Owner',
            'no_custom_permissions' => 'No custom permissions',
            'empty' => 'No collaborator yet.',
            'modal' => [
                'add_title' => 'Invite Collaborator',
                'edit_title' => 'Edit Collaborator',
                'subtitle' => 'Only Mentor/Disciples accounts can be added.',
            ],
            'form' => [
                'search_user' => 'Search User',
                'search_placeholder' => 'Name or email',
                'select_user' => 'Select User',
                'select_placeholder' => 'Choose Mentor',
                'no_eligible_users' => 'No matching mentors or they are already connected to this topic.',
                'user' => 'User',
                'permissions' => 'Permissions',
                'status' => 'Status',
                'status_active' => 'Active',
                'status_inactive' => 'Inactive',
                'cancel' => 'Cancel',
                'save' => 'Save Collaborator',
                'update' => 'Update Collaborator',
            ],
        ],
    ],
];
