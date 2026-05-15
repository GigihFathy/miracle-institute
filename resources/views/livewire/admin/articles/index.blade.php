<div class="space-y-6">
    <x-ui.page-header
        title="{{ __('admin.articles.page_title') }}"
        subtitle="{{ __('admin.articles.page_subtitle') }}"
    >
        <div class="flex gap-2">
            <a href="{{ localized_route('admin.articles.create') }}"
               class="rounded-xl bg-slate-900 px-4 py-2 text-sm text-white transition hover:bg-slate-800">
                {{ __('admin.articles.actions.create') }}
            </a>
        </div>
    </x-ui.page-header>

    <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
        <div class="rounded-2xl border bg-white p-5">
            <div class="text-xs text-slate-500">{{ __('admin.articles.stats.total') }}</div>
            <div class="mt-1 text-3xl font-bold">{{ number_format($stats['total']) }}</div>
        </div>

        <div class="rounded-2xl border bg-emerald-50/30 p-5">
            <div class="text-xs text-slate-500">{{ __('admin.articles.stats.active') }}</div>
            <div class="mt-1 text-3xl font-bold text-emerald-600">{{ number_format($stats['active']) }}</div>
        </div>

        <div class="rounded-2xl border bg-slate-50 p-5">
            <div class="text-xs text-slate-500">{{ __('admin.articles.stats.draft') }}</div>
            <div class="mt-1 text-3xl font-bold">{{ number_format($stats['draft']) }}</div>
        </div>

        <div class="rounded-2xl border bg-rose-50/30 p-5">
            <div class="text-xs text-slate-500">{{ __('admin.articles.stats.clicks') }}</div>
            <div class="mt-1 text-3xl font-bold text-rose-600">{{ number_format($stats['clicks']) }}</div>
        </div>
    </div>

    <div class="space-y-3 rounded-2xl border bg-white p-4">
        <div class="grid grid-cols-1 gap-3 md:grid-cols-3">
            <input
                wire:model.live="search"
                class="w-full rounded-xl border px-4 py-2"
                placeholder="{{ __('admin.articles.search_placeholder') }}"
            >

            <select wire:model.live="statusFilter" class="rounded-xl border px-4 py-2">
                <option value="">{{ __('admin.articles.filters.all_status') }}</option>
                <option value="active">{{ __('admin.articles.filters.active') }}</option>
                <option value="inactive">{{ __('admin.articles.filters.inactive') }}</option>
                <option value="draft">{{ __('admin.articles.filters.draft') }}</option>
            </select>

            <select wire:model.live="perPage" class="rounded-xl border px-4 py-2">
                <option value="10">{{ trans_choice('admin.articles.per_page', 10) }}</option>
                <option value="25">{{ trans_choice('admin.articles.per_page', 25) }}</option>
                <option value="50">{{ trans_choice('admin.articles.per_page', 50) }}</option>
            </select>
        </div>
    </div>

    <div class="overflow-hidden rounded-2xl border bg-white">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-left">
                <tr>
                    <th class="p-4">{{ __('admin.articles.table.thumbnail') }}</th>
                    <th class="p-4">{{ __('admin.articles.table.title') }}</th>
                    <th class="p-4">{{ __('admin.articles.table.author') }}</th>
                    <th class="p-4">{{ __('admin.articles.table.status') }}</th>
                    <th class="p-4">{{ __('admin.articles.table.clicks') }}</th>
                    <th class="p-4">{{ __('admin.articles.table.updated') }}</th>
                    <th class="p-4">{{ __('admin.articles.table.action') }}</th>
                </tr>
            </thead>

            <tbody>
                @forelse($rows as $row)
                    <tr class="align-top border-t">
                        <td class="p-4">
                            @if($row->image_url)
                                <img src="{{ $row->image_url }}"
                                     alt="{{ $row->title }}"
                                     class="h-16 w-24 rounded-xl border object-cover">
                            @else
                                <div class="flex h-16 w-24 items-center justify-center rounded-xl border bg-slate-100 text-xs text-slate-400">
                                    {{ __('admin.articles.no_image') }}
                                </div>
                            @endif
                        </td>

                        <td class="p-4">
                            <div class="font-medium text-slate-900">{{ $row->title }}</div>
                            <div class="line-clamp-2 text-xs text-slate-500">
                                {!! \Illuminate\Support\Str::limit(strip_tags($row->content), 100) !!}
                            </div>
                        </td>

                        <td class="p-4">
                            <div class="font-medium">{{ $row->author }}</div>
                        </td>

                        <td class="p-4">
                            <span class="rounded-full bg-slate-100 px-2 py-1 text-xs">
                                {{ __('admin.articles.status.' . $row->status, [], $row->status) }}
                            </span>
                        </td>

                        <td class="p-4">{{ number_format($row->clicked) }}</td>

                        <td class="p-4">
                            {{ $row->updated_at?->format('d M Y, H:i') }}
                        </td>

                        <td class="p-4">
                            <div class="flex flex-wrap gap-2">
                                <a href="{{ localized_route('admin.articles.edit', $row->id) }}"
                                   class="rounded-lg bg-blue-100 px-3 py-1.5 text-xs text-blue-700 transition hover:bg-blue-200">
                                    {{ __('admin.articles.actions.edit') }}
                                </a>

                                <button
                                    wire:click="toggleStatus('{{ $row->id }}')"
                                    class="rounded-lg bg-emerald-100 px-3 py-1.5 text-xs text-emerald-700 transition hover:bg-emerald-200"
                                >
                                    {{ $row->status === 'active'
                                        ? __('admin.articles.actions.deactivate')
                                        : __('admin.articles.actions.activate') }}
                                </button>

                                <button
                                    wire:click="delete('{{ $row->id }}')"
                                    class="rounded-lg bg-red-100 px-3 py-1.5 text-xs text-red-700 transition hover:bg-red-200"
                                >
                                    {{ __('admin.articles.actions.delete') }}
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="p-6 text-center text-slate-500">
                            {{ __('admin.articles.empty') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>{{ $rows->links() }}</div>
</div>