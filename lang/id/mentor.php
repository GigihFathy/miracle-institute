<?php

return [
    'dashboard' => [
        'page_title' => 'Dashboard Mentor',
        'page_subtitle' => 'Ringkasan singkat untuk mengelola topic, material, dan progres pembelajaran.',

        'stats' => [
            'topics' => 'Topik',
            'topics_hint' => 'Topik yang kamu kelola',
            'materials' => 'Materi',
            'materials_hint' => 'Materi yang kamu upload',
            'students' => 'Siswa',
            'students_hint' => 'Siswa yang terhubung',
        ],

        'managed_courses' => [
            'title' => 'Course yang Dikelola',
            'subtitle' => 'Daftar course yang bisa kamu kelola.',
            'no_course' => 'Tidak Ada Course',
            'topic_count' => '{0} tidak ada topik|{1} :count topik|[2,*] :count topik',
            'active_badge' => 'Aktif',
            'hide' => 'Sembunyikan',
            'show' => 'Tampilkan',
            'manage' => 'Kelola',
            'empty' => 'Belum ada topic yang kamu kelola.',
        ],

        'recent_materials' => [
            'title' => 'Materi Terbaru',
            'subtitle' => 'Materi terakhir yang kamu tambahkan.',
            'empty' => 'Belum ada materi.',
        ],
    ],

    'topics' => [
        'index' => [
            'page_title' => 'Topik yang Dimoderasi',
            'page_subtitle' => 'Daftar topic yang bisa kamu kelola.',
            'back' => 'Kembali',
            'search_placeholder' => 'Cari topic...',
            'open' => 'Buka',
            'empty' => [
                'title' => 'Belum ada topic',
                'description' => 'Kamu belum menjadi mentor di topik mana pun.',
            ],
            'metrics' => [
                'materials' => 'Materi',
                'students' => 'Siswa',
                'active' => 'AKTIF',
                'inactive' => 'NONAKTIF',
                'assessment' => 'Assessment',
            ],
        ],
    ],

    'topic_workspace' => [
        'header' => 'Workspace Mentor',
        'subtitle' => 'Workspace ringkas untuk mengelola materi, session, attendance, collaborator, dan assessment.',
        'visit_topic' => 'Kunjungi Topic',
        'cards' => [
            'topic' => 'Topik',
            'course' => 'Course',
            'program' => 'Program',
        ],
    ],

    'topic_tabs' => [
        'overview' => [
            'title' => 'Ringkasan Topik',
            'no_description' => 'Belum ada deskripsi topic.',
            'access' => [
                'materials' => 'Akses Materi',
                'sessions' => 'Akses Sesi',
                'students' => 'Akses Student',
            ],
            'cards' => [
                'category' => 'Kategori',
                'visibility' => 'Visibilitas',
                'materials' => 'Materi',
                'session_status' => 'Status Sesi',
            ],
        ],

        'materials' => [
            'selected' => [
                'title' => 'Material Terpilih',
                'subtitle' => 'Preview dan detail data material yang dipilih.',
            ],
            'actions' => [
                'edit' => 'Edit',
                'delete' => 'Hapus',
                'watch_youtube' => 'Tonton di YouTube',
                'open_download' => 'Buka / Download Dokumen',
            ],
            'thumbnail_alt' => 'Thumbnail :name',
            'thumbnail_unavailable' => 'Thumbnail tidak tersedia',
            'youtube_hint' => 'Video akan dibuka di tab baru untuk menghindari error restriksi.',
            'no_preview' => 'Material ini belum memiliki preview.',
            'empty_selected' => [
                'title' => 'Belum Ada Material Terpilih',
                'subtitle' => 'Silakan pilih salah satu material dari daftar di sebelah kanan untuk melihat detail dan preview.',
            ],
            'list' => [
                'title' => 'Daftar Material',
                'actions' => [
                    'add' => 'Tambah Material',
                ],
                'limit_reached' => 'Limit tercapai',
                'empty' => 'Belum ada material.',
            ],
            'modal' => [
                'add_title' => 'Tambah Material',
                'edit_title' => 'Edit Material',
                'subtitle' => 'Gunakan external path untuk Google Drive atau YouTube.',
            ],
            'form' => [
                'name' => 'Nama Material',
                'name_placeholder' => 'Nama material',
                'type' => 'Tipe',
                'select' => 'Pilih',
                'no_types_left' => 'Semua tipe sudah dipakai pada topik ini.',
                'status' => 'Status',
                'status_active' => 'Active',
                'status_inactive' => 'Inactive',
                'file' => 'File Material',
                'external_url' => 'External URL',
                'external_url_placeholder' => 'YouTube URL / video ID',
                'sort_order' => 'Urutan',
                'cancel' => 'Batal',
                'save' => 'Simpan Material',
                'update' => 'Perbarui Material',
            ],
        ],

        'sessions' => [
            'title' => 'Sesi',
            'subtitle' => 'Session online dengan para student.',
            'actions' => [
                'edit' => 'Edit',
                'add' => 'Tambah Session',
            ],
            'zoom' => 'Zoom',
            'open_link' => 'Buka link',
            'empty' => 'Belum ada session.',
            'modal' => [
                'add_title' => 'Tambah Session',
                'edit_title' => 'Edit Session',
                'subtitle' => 'Session untuk topik terkini.',
            ],
            'form' => [
                'title' => 'Judul',
                'title_placeholder' => 'Judul session',
                'start_at' => 'Mulai',
                'end_at' => 'Selesai',
                'zoom_link' => 'Link Zoom',
                'status' => 'Status',
                'cancel' => 'Batal',
                'save' => 'Simpan Session',
                'update' => 'Perbarui Session',
            ],
            'status' => [
                'scheduled' => 'Terjadwal',
                'ongoing' => 'Berlangsung',
                'completed' => 'Selesai',
                'cancelled' => 'Dibatalkan',
            ],
        ],

        'attendances' => [
            'title' => 'Attendance',
            'subtitle' => 'Rekap kehadiran berdasarkan session topic.',
            'manager_badge' => 'Attendance Manager',
            'stats' => [
                'present' => 'Hadir :count',
                'late' => 'Terlambat :count',
                'absent' => 'Absen :count',
            ],
            'table' => [
                'student' => 'Student',
                'status' => 'Status',
                'check_in' => 'Check In',
                'empty_session' => 'Belum ada attendance.',
            ],
            'empty' => [
                'title' => 'Attendance belum tersedia',
                'description' => 'Session aktif belum dibuat atau belum ada data kehadiran student.',
            ],
        ],

        'students' => [
            'title' => 'Student',
            'subtitle' => 'Monitoring progress student pada topic ini.',
            'manager_badge' => 'Student Manager',
            'table' => [
                'student' => 'Student',
                'progress' => 'Progres',
                'status' => 'Status',
            ],
            'empty' => [
                'title' => 'Belum ada student',
                'description' => 'Enrollment student pada topic ini masih kosong.',
            ],
        ],

        'collaborators' => [
            'title' => 'Collaborator',
            'subtitle' => 'Mentor utama dan collaborator pada topic ini.',
            'actions' => [
                'invite' => 'Undang Collaborator',
                'edit' => 'Edit',
                'remove' => 'Hapus',
            ],
            'owner' => 'Owner',
            'no_custom_permissions' => 'Tidak ada permission kustom',
            'empty' => 'Belum ada collaborator.',
            'modal' => [
                'add_title' => 'Undang Collaborator',
                'edit_title' => 'Edit Collaborator',
                'subtitle' => 'Hanya akun Mentor/Disciples yang dapat ditambahkan.',
            ],
            'form' => [
                'search_user' => 'Cari User',
                'search_placeholder' => 'Nama atau email',
                'select_user' => 'Pilih User',
                'select_placeholder' => 'Pilih Mentor',
                'no_eligible_users' => 'Tidak ada mentor yang cocok atau sudah terhubung ke topic ini.',
                'user' => 'User',
                'permissions' => 'Permissions',
                'status' => 'Status',
                'status_active' => 'Active',
                'status_inactive' => 'Inactive',
                'cancel' => 'Batal',
                'save' => 'Simpan Collaborator',
                'update' => 'Perbarui Collaborator',
            ],
        ],
    ],
];