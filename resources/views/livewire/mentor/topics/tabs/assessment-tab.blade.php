<section class="mentor-workspace-panel">
    <h2 class="mentor-workspace-heading">Assessment</h2>
    <p class="mentor-workspace-subheading">Ringkasan singkat assessment pada topik pembelajaran ini.</p>

    @if($assessment)
        <div class="mt-4 grid grid-cols-1 gap-3 text-sm sm:grid-cols-4">
            <div class="mentor-workspace-card p-4">
                <div class="text-xs text-[color:color-mix(in_oklab,#004777_60%,white)]">Title</div>
                <div class="mt-1 font-semibold text-[var(--mentor-primary)]">{{ $assessment->title }}</div>
            </div>
            <div class="mentor-workspace-card p-4">
                <div class="text-xs text-[color:color-mix(in_oklab,#004777_60%,white)]">Passing Grade</div>
                <div class="mt-1 font-semibold text-[var(--mentor-primary)]">{{ $assessment->passing_grade }}</div>
            </div>
            <div class="mentor-workspace-card p-4">
                <div class="text-xs text-[color:color-mix(in_oklab,#004777_60%,white)]">Questions</div>
                <div class="mt-1 font-semibold text-[var(--mentor-primary)]">{{ $assessment->question_limit ?? 'All' }}</div>
            </div>
        </div>

        <div class="mt-4">
            <a
                href="{{ localized_route('mentor.assessments.index', ['courseFilter' => $topic->course_id]) }}"
                class="admin-primary-button inline-flex items-center justify-center rounded-xl px-4 py-3 text-sm"
            >
                Kelola Assessment
            </a>
        </div>
    @else
        <div class="mentor-workspace-empty mt-4">
            Assessment belum tersedia.
        </div>
    @endif
</section>
