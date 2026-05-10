<section class="space-y-5">
    <div class="rounded-3xl border border-slate-200 bg-white p-5 sm:p-6">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
            <div>
                <h2 class="text-xl font-semibold tracking-tight text-slate-900">
                    Topic Overview
                </h2>

                <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-500">
                    {{ $topic->description ?: 'Belum ada deskripsi topic.' }}
                </p>
            </div>

            <div class="flex flex-wrap gap-2">
                @if($canManageMaterials)
                    <span class="inline-flex items-center rounded-full bg-slate-900 px-3 py-1 text-xs font-medium text-white">
                        Materials Access
                    </span>
                @endif

                @if($canManageSessions)
                    <span class="inline-flex items-center rounded-full bg-blue-600 px-3 py-1 text-xs font-medium text-white">
                        Sessions Access
                    </span>
                @endif

                @if($canManageStudents)
                    <span class="inline-flex items-center rounded-full bg-emerald-600 px-3 py-1 text-xs font-medium text-white">
                        Students Access
                    </span>
                @endif
            </div>
        </div>

        <div class="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-2xl border border-slate-200 bg-slate-50/70 p-4">
                <div class="text-xs font-medium uppercase tracking-wide text-slate-500">
                    Category
                </div>

                <div class="mt-2 text-sm font-semibold text-slate-900">
                    {{ strtoupper($topic->category ?? '-') }}
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-slate-50/70 p-4">
                <div class="text-xs font-medium uppercase tracking-wide text-slate-500">
                    Visibility
                </div>

                <div class="mt-2 text-sm font-semibold text-slate-900">
                    {{ strtoupper($topic->visibility) }}
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-slate-50/70 p-4">
                <div class="text-xs font-medium uppercase tracking-wide text-slate-500">
                    Materials
                </div>

                <div class="mt-2 text-sm font-semibold text-slate-900">
                    {{ $materialsCount }}
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-slate-50/70 p-4">
                <div class="text-xs font-medium uppercase tracking-wide text-slate-500">
                    Session Status
                </div>

                <div class="mt-2">
                    <span class="inline-flex rounded-full border border-slate-200 bg-white px-3 py-1 text-xs font-medium uppercase tracking-wide text-slate-700">
                        {{ str_replace('_', ' ', $sessionStatus) }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</section>