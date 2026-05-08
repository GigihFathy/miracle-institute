<div class="space-y-5 lg:px-36">
    <x-ui.page-header
        title="Certificates"
        subtitle="Lihat dan unduh sertifikat topic/course kamu."
    />

    <div class="rounded-2xl bg-white border p-4 grid grid-cols-1 md:grid-cols-3 gap-3">
        <input type="search"
               wire:model.debounce.300ms="search"
               placeholder="Cari nomor sertifikat..."
               class="border rounded-xl px-4 py-2">

        <select wire:model="type" class="border rounded-xl px-4 py-2">
            <option value="">All types</option>
            <option value="topic">Topic</option>
            <option value="course">Course</option>
        </select>

        <select wire:model="perPage" class="border rounded-xl px-4 py-2">
            <option value="9">9 / halaman</option>
            <option value="12">12 / halaman</option>
            <option value="24">24 / halaman</option>
        </select>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        @forelse($certificates as $certificate)
            <div class="rounded-2xl bg-white border p-5 space-y-3">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <h3 class="font-semibold">{{ $certificate->certificate_number }}</h3>
                        <p class="text-sm text-slate-500">{{ ucfirst($certificate->type) }}</p>
                    </div>
                    <span class="text-xs px-2 py-1 rounded-full bg-slate-100">{{ $certificate->status }}</span>
                </div>

                <p class="text-sm text-slate-600">
                    @if($certificate->type === 'topic')
                        Topic: {{ $certificate->topic?->name }}
                    @else
                        Course: {{ $certificate->course?->title }}
                    @endif
                </p>

                @if($certificate->file_path)
                    <x-ui.button href="{{ route('certificates.download', $certificate->id) }}" variant="primary" size="md">Download</x-ui.button>
                @endif
            </div>
        @empty
            <x-ui.empty-state
                title="Belum ada sertifikat"
                description="Sertifikat akan muncul setelah kamu menyelesaikan topic atau course."
            />
        @endforelse
    </div>

    <div>{{ $certificates->links() }}</div>
</div>