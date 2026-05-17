<div class="space-y-6">
    <x-ui.page-header
        title="Assessments"
        subtitle="Kelola post-test, passing grade, dan randomisasi soal."
    >
        <button class="px-4 py-2 rounded-xl bg-slate-900 text-white text-sm">
            + New Assessment
        </button>
    </x-ui.page-header>

    <div class="rounded-2xl bg-white border p-4">
        <input type="search"
               wire:model.debounce.300ms="search"
               placeholder="Cari assessment..."
               class="w-full md:w-1/2 border rounded-xl px-4 py-2">
    </div>

    @if($assessments->isEmpty())
        <x-ui.empty-state
            title="Belum ada assessment"
            description="Buat assessment pertama untuk topic tertentu."
        />
    @else
        <div class="rounded-2xl bg-white border overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-left">
                    <tr>
                        <th class="p-4">Title</th>
                        <th class="p-4">Topic</th>
                        <th class="p-4">Passing Grade</th>
                        <th class="p-4">Status</th>
                        <th class="p-4">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($assessments as $assessment)
                        <tr class="border-t">
                            <td class="p-4">{{ $assessment->title }}</td>
                            <td class="p-4">{{ $assessment->topic?->name }}</td>
                            <td class="p-4">{{ $assessment->passing_grade }}</td>
                            <td class="p-4">{{ $assessment->status }}</td>
                            <td class="p-4">
                                <a href="{{ localized_route('admin.assessments.index', ['courseFilter' => $assessment->course_id]) }}"
                                   class="text-slate-900 underline">
                                    Manage Questions
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div>{{ $assessments->links() }}</div>
    @endif
</div>