<?php

return [
    'explore_dashboard' => [
        'defaults' => [
            'learner' => 'Learner',
            'instructor' => 'Instructor',
            'church_illustration' => 'Church illustration',
        ],

        'hero' => [
            'guest' => [
                'title' => 'Walking in Miracles, Growing as Disciples',
                'description' => 'Discover a faith journey that draws you closer to Jesus through discipleship, Bible learning, and a life-giving community.',
                'explore_journey' => 'Explore Journey',
                'login' => 'Login',
            ],

            'member' => [
                'welcome_back' => 'Welcome back to your spiritual journey',
                'keep_growing' => 'keep growing in faith',
                'description' => 'Continue your discipleship journey, learn the truth of God’s Word, and experience real spiritual growth every day.',
                'courses' => 'Courses',
                'completed' => 'Completed',
                'progress' => 'Progress',
                'explore_classes' => 'Explore Classes',
                'my_journey' => 'My Journey',
                'featured_program' => 'Featured Program',
                'featured_program_description' => 'Grow deeper in Christ through transformative biblical learning',
            ],
        ],

        'continue' => [
            'title' => 'Continue Your Journey',
            'description' => 'Continue where you left off in your discipleship journey.',
            'continue_where_left_off' => 'Continue where you left off',
            'progress' => 'Progress',
        ],

        'study_programs' => [
            'title' => 'Grow in faith and discover God’s purpose for your life',
            'description' => 'Explore discipleship programs, biblical teachings, and spiritual growth paths designed to strengthen your relationship with God.',
            'scroll_left' => 'Scroll categories left',
            'scroll_right' => 'Scroll categories right',
        ],

        'featured_teachings' => [
            'title' => 'Featured Teachings',
            'description' => 'Discover impactful teachings and discipleship classes prepared to strengthen your faith journey.',
            'open' => 'Open',
            'premium' => 'Premium',
            'bestseller' => 'Bestseller',
        ],

        'cta' => [
            'title' => 'Discover the Meaning of Faith Through Discipleship',
            'description' => 'Miracle Institute exists to help every person grow in faith, know Jesus more deeply, and live daily in God’s power and love.',
            'learn' => [
                'title' => 'Learn',
                'description' => 'Biblical truths and spiritual principles',
            ],
            'disciple' => [
                'title' => 'Disciple',
                'description' => 'Grow deeper in biblical truth',
            ],
            'community' => [
                'title' => 'Community',
                'description' => 'Walk together in faith',
            ],
            'impact' => [
                'title' => 'Impact',
                'description' => 'Become a light for others',
            ],
            'start_your_journey' => 'Start Your Journey',
        ],
    ],

    'my_learning' => [
        'page_title' => 'My Learning',
        'overview_title' => 'My Learning Overview',
        'overview_description' => 'A quick summary of your learning progress, including courses, topics, and certificates earned.',

        'metrics' => [
            'courses_enrolled' => 'Courses Enrolled',
            'courses_enrolled_hint' => 'Courses you are currently taking',
            'topics_completed' => 'Topics Completed',
            'topics_completed_hint' => 'Topics you have completed',
            'certificates' => 'Certificates',
            'certificates_hint' => 'Certificates you have earned',
        ],

        'tabs' => [
            'aria_label' => 'Learning sections',
            'courses' => 'Courses',
            'session' => 'Session',
            'certificate' => 'Certificate',
        ],

        'courses' => [
            'title' => 'Courses in progress',
            'search_placeholder' => 'Search course...',
            'filters' => [
                'all' => 'All',
                'in_progress' => 'In Progress',
                'completed' => 'Completed',
            ],
            'no_description' => 'No description available for this course.',
            'progress_text' => ':completed / :total topics completed',
            'reset_filters' => 'Reset Filters',
            'browse_courses' => 'Browse Courses',
            'empty' => [
                'filtered_title' => 'No matching courses',
                'filtered_description' => 'Try changing the search keyword or progress filter so the course appears again.',
                'no_courses_title' => 'No courses yet',
                'no_courses_description' => 'You are not enrolled in any course yet.',
            ],
        ],

        'sessions' => [
            'title' => 'Upcoming Sessions',
            'empty' => 'No scheduled sessions.',
        ],

        'certificates' => [
            'title' => 'Certificates',
            'default_course_certificate' => 'Course Certificate',
            'number_label' => 'Certificate Number',
            'issued_label' => 'Issued',
            'download' => 'Download Certificate',
            'empty_title' => 'No Certificates Yet',
            'empty_description' => 'Certificates will be available after you complete a course.',
        ],
    ],

    'course_catalog' => [
        'hero' => [
            'mentor' => [
                'title' => 'Guide, mentor, and oversee discipleship learning journeys.',
                'description' => 'Manage discipleship courses, monitor learning topics, and guide participants through structured materials.',
            ],
            'student' => [
                'title' => 'Grow through structured discipleship learning and mentoring.',
                'description' => 'Study discipleship materials systematically through topics, mentoring sessions, assessments, and learning progress.',
            ],
        ],

        'stats' => [
            'available_courses' => 'Available Courses',
            'study_programs' => 'Study Programs',
        ],

        'feature_panel' => [
            'title' => 'Learning centered on spiritual growth, mentoring, and consistency.',
            'description' => 'Each course is designed to help participants understand discipleship material step by step through structured learning and mentor guidance.',
            'structured' => [
                'label' => 'Structured',
                'value' => 'Learning Topics',
            ],
            'guided' => [
                'label' => 'Guided',
                'value' => 'Mentoring System',
            ],
        ],

        'filters' => [
            'search_placeholder' => 'Search course, topic, or keyword...',
            'all_study_programs' => 'All Study Programs',
        ],

        'sort' => [
            'newest' => 'Newest',
            'oldest' => 'Oldest',
            'latest' => 'Newest',
            'title' => 'Title',
            'topics' => 'Most Topics',
        ],

        'defaults' => [
            'no_description' => 'No description available for this course.',
        ],

        'badges' => [
            'topics' => '{0} No topics|{1} :count Topic|[2,*] :count Topics',
            'enrolled' => 'Enrolled',
        ],

        'actions' => [
            'open' => 'View Details',
            'enroll' => 'Enroll Now',
            'login' => 'Login',
        ],

        'empty' => [
            'title' => 'No courses found',
            'description' => 'Try changing filters or keywords.',
        ],
    ],

    'topbar_search' => [
        'placeholder' => 'Search courses...',
        'submit' => 'Search',
        'preview_title' => 'Matching courses',
        'empty' => 'No matching courses found. Try a different keyword.',
        'view_all_results' => 'See all results for ":query"',
        'all_courses' => 'all courses',
        'default_program' => 'Course',
    ],

    'course_show' => [
        'login_to_track' => 'Sign In to Start Learning',
        'enrolled' => 'Enrolled',
        'enroll' => 'Enroll in Course',
        'processing' => 'Processing...',
        'mentor_mode' => 'Mentor Mode',
        'guest_notice' => 'Sign in to save your learning progress, take the assessment, and access your certificate when it becomes available.',

        'course_access' => 'Course Progress',
        'course_access_description' => 'Track your assessment and certificate readiness here.',

        'assessment_label' => 'Assessment',
        'certificate_label' => 'Certificate',
        'issued' => 'Issued',
        'eligible' => 'Ready to Claim',
        'locked' => 'Not Available Yet',
        'not_published' => 'Not Published',
        'unlocked' => 'Ready to Start',
        'complete_topics_first' => 'Complete all topics first',
        'assessment_unavailable' => 'No assessment is available for this course yet.',
        'certificate_unavailable' => 'Your certificate will appear after all requirements are completed.',
        'certificate_ready' => 'Your course certificate is ready to be claimed.',
        'certificate_issued_description' => 'Your certificate is available and ready to download.',
        'assessment_ready_description' => 'All topics are complete. You can now take the assessment.',
        'assessment_pending_description' => 'Complete all topics to unlock the assessment.',
        'assessment_passed_badge' => 'Passed',
        'assessment_passed_description' => 'The assessment is complete and you have met the passing grade.',
        'assessment_completed_cta' => 'Assessment Completed',

        'resume_test' => 'Resume Assessment',
        'start_test' => 'Start Assessment',
        'download_certificate' => 'Download Certificate',
        'claim_certificate' => 'Claim Certificate',
        'continue_learning' => 'Continue Learning',
        'course_overview' => 'Course overview',
        'course_overview_description' => 'Follow the topics in order, continue to the assessment, then claim your certificate once all requirements are complete.',
        'topics_stat' => 'Topics',
        'progress_stat' => 'Progress',
        'assessment_stat' => 'Assessment',
        'completed_progress' => ':percent% complete',
        'assessment_available' => 'Available',
        'assessment_pending' => 'Waiting for progress',
        'assessment_completed' => 'Completed',

        'course_topics' => 'Course Topics',
        'topics_count' => '{0} No Topics|{1} :count Topic|[2,*] :count Topics',
        'completed_count' => ':count Completed',

        'status' => [
            'available' => 'Available',
            'review' => 'Review',
            'completed' => 'Completed',
            'in_progress' => 'In Progress',
            'not_started' => 'Not Started',
        ],

        'session_label' => 'Session :status',
        'no_session' => 'No Session',
        'session_status' => [
            'completed' => 'Completed',
            'ongoing' => 'Ongoing',
            'scheduled' => 'Scheduled',
            'cancelled' => 'Cancelled',
            'none' => 'No Session',
        ],

        'login_to_access_topic' => 'Login to access topic',
        'open_topic' => 'Study Topic',
        'topic_description_fallback' => 'The topic description will appear here once the course material is completed.',
        'topic_cta_guest' => 'Sign In to Learn',
        'topic_cta_enrolled' => 'Open Topic',

        'mentored_topics' => [
            'title' => 'Mentored Topics',
            'description' => 'Topics where you are Owner or Collaborator.',
            'empty_title' => 'No managed topics',
            'empty_description' => 'You have not been assigned as Owner or Collaborator on any topic in this course.',
        ],

        'role' => [
            'owner' => 'Owner',
            'collaborator' => 'Collaborator',
        ],

        'workspace' => 'Workspace',
        'manage_assessment' => 'Manage Assessment',

        'assessment_modal' => [
            'title' => 'Assessment Details',
            'assessment' => 'Assessment',
            'questions' => 'Questions',
            'passing_grade' => 'Passing Grade',
            'instructions' => 'Instructions',
        ],

        'locked_until_complete' => 'The assessment will be available after all topics are completed',
        'close' => 'Close',
    ],

    'topic_player' => [
        'stats' => [
            'materials' => 'Materials',
            'attendance_records' => 'Attendance Records',
            'sessions' => 'Sessions',
            'progress' => 'Progress',
        ],

        'progress' => [
            'review' => 'REVIEW',
        ],

        'tabs' => [
            'materials' => 'Materials',
            'sessions' => 'Sessions',
        ],

        'actions' => [
            'attend_to_complete' => 'Attend session to complete',
            'complete_unit' => 'Complete Unit',
            'locked_tooltip' => 'The button will be active after the session ends',
            'cancel' => 'Cancel',
            'confirm' => 'Confirm',
            'close' => 'Close',
        ],

        'loading' => [
            'select_material' => 'Loading material...',
            'processing' => 'Processing...',
        ],

        'materials' => [
            'title' => 'Materials',
            'subtitle' => 'All materials in this topic are collected in one place.',
            'thumbnail_alt' => 'Thumbnail :name',
            'doc_thumbnail_alt' => 'Document thumbnail',
            'document_label' => 'Document',
            'thumbnail_not_available' => 'Thumbnail not available',
            'watch_youtube' => 'Watch on YouTube',
            'youtube_hint' => 'The video will open in a new tab for the best viewing experience.',
            'open_download' => 'Open / download material',
            'preview_not_available' => [
                'title' => 'Preview not available',
                'description' => 'This material is registered, but the preview URL/file is invalid or unavailable.',
            ],
            'select_hint' => [
                'title' => 'Select a material to view details',
                'description' => 'Click one of the material cards above to open the preview or related file.',
            ],
            'empty' => [
                'title' => 'No materials available yet',
                'description' => 'The mentor has not prepared materials for this topic yet.',
            ],
            'no_materials' => [
                'title' => 'No materials available yet',
                'description' => 'The mentor has not prepared materials for this topic yet.',
            ],
            'complete_modal' => [
                'title' => 'Mark material as complete?',
                'description' => 'Material :name will be marked complete. The topic progress will be updated automatically.',
            ],
            'type_label' => 'Type: :type',
            'completed_badge' => 'MATERIAL COMPLETED',
            'mark_complete' => 'Mark Complete',
        ],

        'sessions' => [
            'title' => 'Sessions',
            'subtitle' => 'Check the session schedule and join only while the session window is active.',
            'status_label' => 'Status:',
            'check_in' => 'Check in',
            'check_out' => 'Check out',
            'read_only' => 'Read-only review',
            'join_modal' => [
                'title' => 'Join Session',
                'subtitle' => 'The student will be logged to attendance before being redirected to the meeting.',
            ],
            'meta' => [
                'title' => 'Title',
                'status' => 'Status',
                'start' => 'Start',
                'end' => 'End',
            ],
            'countdown' => 'Countdown',
            'clock_in_deadline' => 'Clock-in deadline: :time',
            'join_and_log' => 'Join & Log Attendance',
            'empty' => [
                'title' => 'No sessions scheduled yet',
                'description' => 'The mentor has not scheduled any session for this topic yet.',
            ],
            'states' => [
                'upcoming' => 'Scheduled',
                'scheduled' => 'Scheduled',
                'live' => 'Live',
                'ended' => 'Completed',
                'completed' => 'Completed',
                'invalid' => 'Unavailable',
                'unavailable' => 'Unavailable',
            ],
            'actions' => [
                'not_started' => 'Not Started',
                'join_session' => 'Join Session',
                'completed' => 'Completed',
                'unavailable' => 'Unavailable',
            ],
            'duration' => [
                'hours' => '{1} :count hour|[2,*] :count hours',
                'minutes' => '{1} :count minute|[2,*] :count minutes',
                'seconds' => '{1} :count second|[2,*] :count seconds',
            ],
            'countdown_invalid' => 'Session schedule is incomplete.',
            'completed_label' => 'Session completed',
            'starts_in' => 'Starts in',
            'ends_in' => 'Ends in',
        ],
    ],

    'articles' => [
        'index' => [
            'title' => 'Articles',
            'subtitle' => 'Editorial updates, learning notes, and curated insights.',
            'archive_label' => 'Active archive',
            'search_placeholder' => 'Search articles...',
            'empty' => 'No active articles yet.',
        ],
        'show' => [
            'back_to_articles' => 'Back to articles',
            'meta' => 'Meta',
            'default_author' => 'Editorial Team',
            'thumbnail_preview' => 'Thumbnail preview',
            'reading_note' => 'Reading note',
            'rendered_content' => 'Rendered Content',
            'related_title' => 'Related articles',
            'related_subtitle' => 'Other relevant articles to expand your reading.',
        ],
    ],


    'navigation' => [
        'dashboard' => 'Dashboard',
        'courses' => 'Courses',
        'articles' => 'Articles',
        'my_learning' => 'My Learning',
        'certificates' => 'Certificates',
        'login' => 'Login',
    ],

    'footer' => [
        'default_description' => 'A professional learning platform to improve your competence and career.',
        'navigation' => [
            'title' => 'Navigation',
        ],
        'contact' => [
            'title' => 'Contact',
            'whatsapp' => 'WhatsApp',
            'instagram' => 'Instagram',
        ],
        'social' => [
            'title' => 'Follow Us',
        ],
        'copyright' => '© :year RIG Edutech - BINUS University. All rights reserved.',
    ],

    'shared' => [
        'role_switcher' => [
            'title' => 'Switch Role',
            'active' => 'Active',
        ],
        'profile_dropdown' => [
            'title' => 'Account',
            'profile' => 'Profile',
            'my_learning' => 'My Learning',
            'mentor_dashboard' => 'Mentor Dashboard',
            'logout' => 'Logout',
        ],
        'language_switcher' => [
            'label' => 'Language',
        ],
    ],

    'profile' => [
        'hero' => [
            'badge' => 'Account Center',
            'title' => 'Manage your profile',
            'subtitle' => 'Keep your account details up to date so your learning experience stays personal and easy to manage.',
            'active_role' => 'Active role',
            'member_since' => 'Member since',
        ],
        'stats' => [
            'roles' => 'Roles',
            'status' => 'Status',
            'email' => 'Email',
        ],
        'status' => [
            'verified' => 'Verified',
            'unverified' => 'Not verified',
        ],
        'summary' => [
            'title' => 'Profile summary',
            'verification' => 'Verification',
            'empty' => 'Not set yet',
        ],
        'fields' => [
            'image' => 'Profile photo',
            'name' => 'Full name',
            'email' => 'Email address',
            'phone' => 'Phone number',
            'gender' => 'Gender',
            'dob' => 'Date of birth',
        ],
        'gender' => [
            'male' => 'Male',
            'female' => 'Female',
        ],
        'form' => [
            'title' => 'Edit information',
            'subtitle' => 'Update your personal details and profile photo from one place.',
            'select_gender' => 'Select gender',
            'image_help' => 'Upload a square photo for the best result.',
            'save' => 'Save changes',
        ],
        'flash' => [
            'saved' => 'Profile updated successfully.',
        ],
    ],

    'assessment_taker' => [
        'defaults' => [
            'course_assessment' => 'Course Assessment',
        ],
        'intro' => [
            'description' => 'This assessment is required for course completion after all topics have been studied. You may retake it until you reach the passing grade.',
        ],
        'meta' => [
            'attempt' => 'Attempt #:no',
            'passing' => 'Passing :grade%',
            'passing_label' => 'Passing',
            'randomized' => 'Randomized',
            'fixed_order' => 'Fixed Order',
        ],
        'metrics' => [
            'started' => 'Started',
            'questions' => 'Questions',
            'state' => 'State',
            'progress' => 'Progress',
            'answered' => 'Answered',
            'saved' => 'saved',
        ],
        'instructions' => [
            'title' => 'Instructions',
            'read_carefully' => 'Read each question carefully before answering.',
            'auto_save' => 'All answers are saved automatically.',
            'submit_when_ready' => 'Submit when you are ready to finish the attempt.',
        ],
        'actions' => [
            'resume_test' => 'Resume Test',
            'start_test' => 'Start Test',
            'previous' => 'Previous',
            'next' => 'Next',
            'submit_answers' => 'Submit Answers',
            'submit' => 'Submit',
            'cancel' => 'Cancel',
        ],
        'validation' => [
            'answer_all' => 'All questions must be answered before submitting the assessment.',
        ],
        'navigator' => [
            'title' => 'Navigator',
            'note' => 'All answers are saved automatically. If the attempt does not pass, you can retake it from the beginning.',
        ],
        'question' => [
            'label' => 'Question :current of :total',
            'type' => 'MCQ',
        ],
        'submit_modal' => [
            'label' => 'Confirm Submission',
            'title' => 'Submit your answers now?',
            'description' => 'Answers will be graded automatically. If you do not pass, you may retake the next attempt until you reach the passing grade.',
        ],
    ],

    'assessment_result' => [
        'title' => 'Assessment Result',
        'default_title' => 'Assessment',
        'course_attempt' => 'Course: :course · Attempt #:no',
        'status' => [
            'passed' => 'PASSED',
            'failed' => 'FAILED',
        ],
        'metrics' => [
            'correct' => 'Correct',
            'wrong' => 'Wrong',
            'unanswered' => 'Unanswered',
            'passing_grade' => 'Passing Grade',
        ],
        'notice' => [
            'passed' => 'Assessment completion achieved.',
            'failed' => 'Assessment not yet passed.',
            'passed_description' => 'If all course topics are completed, the certificate will be synchronized automatically by the backend.',
            'failed_description' => 'You may retake the assessment until you pass. Remedial attempts are available without limits while the course remains active.',
        ],
        'certificate' => [
            'title' => 'Certificate',
            'status' => 'Status: :status',
        ],
        'actions' => [
            'back_to_dashboard' => 'Back to Dashboard',
            'back_to_learning' => 'Back to My Learning',
            'back_to_course' => 'Back',
            'retry' => 'Retry Assessment',
            'view_certificates' => 'View Certificates',
        ],
    ],

    'certificate_panel' => [
        'hero' => [
            'badge' => 'Student Certificate Center',
            'title' => 'Your Learning Certificates',
            'subtitle' => 'All course learning certificates that have been successfully completed will appear on this page.',
        ],
        'filters' => [
            'search_placeholder' => 'Search certificate number...',
            'per_page' => ':count Per Page',
        ],
        'defaults' => [
            'course_certificate' => 'Course Certificate',
        ],
        'meta' => [
            'issued_date' => 'Issued Date',
            'certificate_id' => 'Certificate ID',
        ],
        'actions' => [
            'download' => 'Download Certificate',
        ],
        'empty' => [
            'title' => 'No Certificates Yet',
            'description' => 'Certificates will be available after you complete a course.',
        ],
    ],

    'session_join_button' => [
        'status' => 'Status:',
        'clock_in_deadline' => 'Clock-in deadline',
        'schedule_incomplete' => 'Session schedule is incomplete.',
        'attendance' => 'Attendance',
        'check_in' => 'Check in',
        'check_out' => 'Check out',
        'attendance_completed' => 'Attendance completed.',
        'actions' => [
            'join_session' => 'Join Session',
            'clock_out' => 'Clock Out',
        ],
    ],

    'topic_player' => [
        'stats' => [
            'materials' => 'Materials',
            'attendance_records' => 'Attendance Records',
            'sessions' => 'Sessions',
            'progress' => 'Progress',
        ],
        'progress' => [
            'review' => 'REVIEW',
        ],
        'tabs' => [
            'materials' => 'Materials',
            'sessions' => 'Sessions',
        ],
        'actions' => [
            'attend_to_complete' => 'Attend session to complete',
            'complete_unit' => 'Complete Unit',
            'locked_tooltip' => 'The button will be active after the session ends',
            'cancel' => 'Cancel',
            'confirm' => 'Confirm',
            'close' => 'Close',
        ],
        'loading' => [
            'select_material' => 'Loading material...',
            'processing' => 'Processing...',
        ],
        'materials' => [
            'title' => 'Materials',
            'subtitle' => 'All materials in this topic are collected in one place.',
            'thumbnail_alt' => 'Thumbnail :name',
            'doc_thumbnail_alt' => 'Document thumbnail',
            'document_label' => 'Document',
            'thumbnail_not_available' => 'Thumbnail not available',
            'watch_youtube' => 'Watch on YouTube',
            'youtube_hint' => 'The video will open in a new tab for the best viewing experience.',
            'open_download' => 'Open / download material',
            'preview_not_available' => [
                'title' => 'Preview not available',
                'description' => 'This material is registered, but the preview URL/file is invalid or unavailable.',
            ],
            'select_hint' => [
                'title' => 'Select a material to view details',
                'description' => 'Click one of the material cards above to open the preview or related file.',
            ],
            'empty' => [
                'title' => 'No materials available yet',
                'description' => 'The mentor has not prepared materials for this topic yet.',
            ],
            'no_materials' => [
                'title' => 'No materials available yet',
                'description' => 'The mentor has not prepared materials for this topic yet.',
            ],
            'complete_modal' => [
                'title' => 'Mark material as complete?',
                'description' => 'Material :name will be marked complete. The topic progress will be updated automatically.',
            ],
            'type_label' => 'Type: :type',
            'completed_badge' => 'MATERIAL COMPLETED',
            'mark_complete' => 'Mark Complete',
        ],
        'sessions' => [
            'title' => 'Sessions',
            'subtitle' => 'Check the session schedule and join only while the session window is active.',
            'status_label' => 'Status:',
            'check_in' => 'Check in',
            'check_out' => 'Check out',
            'read_only' => 'Read-only review',
            'join_modal' => [
                'title' => 'Join Session',
                'subtitle' => 'The student will be logged to attendance before being redirected to the meeting.',
            ],
            'meta' => [
                'title' => 'Title',
                'status' => 'Status',
                'start' => 'Start',
                'end' => 'End',
            ],
            'countdown' => 'Countdown',
            'clock_in_deadline' => 'Clock-in deadline: :time',
            'join_and_log' => 'Join & Log Attendance',
            'empty' => [
                'title' => 'No sessions scheduled yet',
                'description' => 'The mentor has not scheduled any session for this topic yet.',
            ],
            'states' => [
                'upcoming' => 'Scheduled',
                'scheduled' => 'Scheduled',
                'live' => 'Live',
                'ended' => 'Completed',
                'completed' => 'Completed',
                'invalid' => 'Unavailable',
                'unavailable' => 'Unavailable',
            ],
            'actions' => [
                'not_started' => 'Not Started',
                'join_session' => 'Join Session',
                'completed' => 'Completed',
                'unavailable' => 'Unavailable',
            ],
            'duration' => [
                'hours' => '{1} :count hour|[2,*] :count hours',
                'minutes' => '{1} :count minute|[2,*] :count minutes',
                'seconds' => '{1} :count second|[2,*] :count seconds',
            ],
            'countdown_invalid' => 'Session schedule is incomplete.',
            'completed_label' => 'Session completed',
            'starts_in' => 'Starts in',
            'ends_in' => 'Ends in',
        ],
    ],

    'navigation' => [
        'dashboard' => 'Dashboard',
        'courses' => 'Courses',
        'articles' => 'Articles',
        'my_learning' => 'My Learning',
        'certificates' => 'Certificates',
        'login' => 'Login',
    ],
];
