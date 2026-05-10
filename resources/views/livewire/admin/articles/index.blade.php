<div class="space-y-6">

    <x-ui.page-header
        title="Articles"
        subtitle="Kelola artikel sebagai CMS sederhana: thumbnail, status aktif/nonaktif, dan klik pembaca."
    >
        <div class="flex gap-2">
            <a href="{{ route('admin.articles.create') }}"
               class="px-4 py-2 rounded-xl bg-slate-900 text-white text-sm">
                + New Article
            </a>
        </div>
    </x-ui.page-header>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="rounded-2xl bg-white border p-5">
            <div class="text-xs text-slate-500">Total</div>
            <div class="text-3xl font-bold mt-1">{{ number_format($stats['total']) }}</div>
        </div>

        <div class="rounded-2xl bg-emerald-50/30 border p-5">
            <div class="text-xs text-slate-500">Active</div>
            <div class="text-3xl font-bold mt-1 text-emerald-600">{{ number_format($stats['active']) }}</div>
        </div>

        <div class="rounded-2xl bg-slate-50 border p-5">
            <div class="text-xs text-slate-500">Draft</div>
            <div class="text-3xl font-bold mt-1">{{ number_format($stats['draft']) }}</div>
        </div>

        <div class="rounded-2xl bg-rose-50/30 border p-5">
            <div class="text-xs text-slate-500">Clicks</div>
            <div class="text-3xl font-bold mt-1 text-rose-600">{{ number_format($stats['clicks']) }}</div>
        </div>
    </div>

    <div class="rounded-2xl bg-white border p-4 space-y-3">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <input wire:model.live="search"
                   class="w-full border rounded-xl px-4 py-2"
                   placeholder="Search article, author, or content...">

            <select wire:model.live="statusFilter" class="border rounded-xl px-4 py-2">
                <option value="">All status</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
                <option value="draft">Draft</option>
            </select>

            <select wire:model.live="perPage" class="border rounded-xl px-4 py-2">
                <option value="10">10 / page</option>
                <option value="25">25 / page</option>
                <option value="50">50 / page</option>
            </select>
        </div>
    </div>

    <div class="rounded-2xl bg-white border overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-left">
                <tr>
                    <th class="p-4">Thumbnail</th>
                    <th class="p-4">Title</th>
                    <th class="p-4">Author</th>
                    <th class="p-4">Status</th>
                    <th class="p-4">Clicks</th>
                    <th class="p-4">Updated</th>
                    <th class="p-4">Action</th>
                </tr>
            </thead>

            <tbody>
                @forelse($rows as $row)
                    <tr class="border-t align-top">
                        <td class="p-4">
                            @if($row->image_url)
                                <img src="{{ $row->image_url }}"
                                     alt="{{ $row->title }}"
                                     class="w-24 h-16 rounded-xl object-cover border">
                            @else
                                <div class="w-24 h-16 rounded-xl border bg-slate-100 flex items-center justify-center text-xs text-slate-400">
                                    No image
                                </div>
                            @endif
                        </td>

                        <td class="p-4">
                            <div class="font-medium text-slate-900">{{ $row->title }}</div>
                            <div class="text-xs text-slate-500 line-clamp-2">
                                {!!\Illuminate\Support\Str::limit(strip_tags($row->content), 100) !!}
                            </div>
                        </td>

                        <td class="p-4">
                            <div class="font-medium">{{ $row->author }}</div>
                        </td>

                        <td class="p-4">
                            <span class="px-2 py-1 rounded-full text-xs bg-slate-100">
                                {{ $row->status }}
                            </span>
                        </td>

                        <td class="p-4">{{ number_format($row->clicked) }}</td>

                        <td class="p-4">
                            {{ $row->updated_at?->format('d M Y, H:i') }}
                        </td>

                        <td class="p-4">
                            <div class="flex flex-wrap gap-2">
                                <a href="{{ route('admin.articles.edit', $row->id) }}"
                                   class="px-3 py-1.5 rounded-lg text-xs bg-blue-100 text-blue-700 hover:bg-blue-200">
                                    Edit
                                </a>

                                <button wire:click="toggleStatus('{{ $row->id }}')"
                                    class="px-3 py-1.5 rounded-lg text-xs bg-emerald-100 text-emerald-700 hover:bg-emerald-200">
                                    {{ $row->status === 'active' ? 'Deactivate' : 'Activate' }}
                                </button>

                                <button wire:click="delete('{{ $row->id }}')"
                                    class="px-3 py-1.5 rounded-lg text-xs bg-red-100 text-red-700 hover:bg-red-200">
                                    Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="p-6 text-center text-slate-500">
                            No articles found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>{{ $rows->links() }}</div>

</div>