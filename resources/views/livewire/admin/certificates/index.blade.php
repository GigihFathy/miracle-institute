<div class="space-y-6">
    <x-ui.page-header
        title="{{ __('admin.certificates.page_title') }}"
        subtitle="{{ __('admin.certificates.page_subtitle') }}"
    >
        @if($selectedCourse)
            <a href="{{ localized_route('admin.topics.index', ['courseFilter' => $selectedCourse->id]) }}"
               class="rounded-xl border px-4 py-2 text-sm">
                Back
            </a>
        @endif
    </x-ui.page-header>

    @if($selectedCourse)
        <div class="rounded-2xl border bg-white px-4 py-3 text-sm text-slate-700">
            <div class="font-semibold text-slate-900">{{ $selectedCourse->title }}</div>
            <div class="mt-1 text-xs text-slate-500">Total certificates: {{ number_format($certificatesCount) }}</div>
        </div>
    @endif

    <div class="rounded-2xl border bg-white p-4">
        <input wire:model.live="search"
            class="w-full rounded-xl border px-4 py-2"
            placeholder="Search by name, email, or certificate number...">
    </div>

    <x-ui.table-shell class="table-auto">
        <thead class="bg-slate-50 text-left">
            <tr>
                <th class="whitespace-nowrap px-4 py-3 font-medium text-slate-600">{{ __('admin.certificates.table.certificate') }}</th>
                <th class="whitespace-nowrap px-4 py-3 font-medium text-slate-600">{{ __('admin.certificates.table.issued') }}</th>
                <th class="whitespace-nowrap px-4 py-3 font-medium text-slate-600">{{ __('admin.certificates.table.status') }}</th>
                <th class="whitespace-nowrap px-4 py-3 font-medium text-slate-600">{{ __('admin.certificates.table.action') }}</th>
            </tr>
        </thead>

        <tbody class="divide-y divide-slate-100 bg-white">
            @forelse($rows as $row)
                <tr class="align-top">
                    <td class="px-4 py-3">
                        <div class="font-medium text-slate-900">{{ $row->user?->full_name ?? '-' }}</div>
                        <div class="text-xs text-slate-500">
                            {{ $row->user?->email ?? '-' }}
                        </div>
                    </td>

                    <td class="whitespace-nowrap px-4 py-3">{{ $row->issued_at?->format('d M Y H:i') ?? '-' }}</td>

                    <td class="whitespace-nowrap px-4 py-3">
                        <span class="rounded-full bg-slate-100 px-2 py-1 text-xs">
                            {{ __('admin.certificates.status.' . $row->status, [], $row->status) }}
                        </span>
                    </td>

                    <td class="px-4 py-3">
                        <div class="flex flex-wrap gap-2">
                            <a href="{{ localized_route('certificates.download', $row->id) }}"
                                class="rounded-lg bg-slate-100 px-3 py-1.5 text-xs hover:bg-slate-200">
                                {{ __('admin.certificates.actions.download') }}
                            </a>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-4 py-6 text-center text-slate-500">
                        {{ __('admin.certificates.empty') }}
                    </td>
                </tr>
            @endforelse
        </tbody>
    </x-ui.table-shell>

    <div>{{ $rows->links() }}</div>
</div>