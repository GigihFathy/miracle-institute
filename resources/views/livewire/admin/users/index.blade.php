<div class="space-y-6">
    <x-ui.page-header title="{{ __('admin.users.page_title') }}" subtitle="{{ __('admin.users.page_subtitle') }}" />

    <div class="rounded-2xl border bg-white p-4">
        <div class="grid grid-cols-1 gap-3 md:grid-cols-3">
            <input wire:model.live="search" class="w-full rounded-xl border px-4 py-2" placeholder="{{ __('admin.users.search_placeholder') }}">

            <select wire:model.live="roleFilter" class="rounded-xl border px-4 py-2">
                <option value="">{{ __('admin.users.filters.all_roles') }}</option>
                @foreach($roles as $role)
                    <option value="{{ $role->name }}">{{ $role->name }}</option>
                @endforeach
            </select>

            <select wire:model.live="sort" class="rounded-xl border px-4 py-2">
                <option value="latest">{{ __('admin.users.sort.latest') }}</option>
                <option value="name_asc">{{ __('admin.users.sort.name_asc') }}</option>
                <option value="name_desc">{{ __('admin.users.sort.name_desc') }}</option>
                <option value="email_asc">{{ __('admin.users.sort.email_asc') }}</option>
                <option value="email_desc">{{ __('admin.users.sort.email_desc') }}</option>
            </select>
        </div>
    </div>

    <div class="overflow-hidden rounded-2xl border bg-white">
        <div class="overflow-x-auto">
            <table class="table-fixed w-full min-w-full text-sm">
                <thead class="bg-slate-50 text-left text-slate-600">
                    <tr>
                        <th class="w-1/4 px-4 py-3 font-medium">{{ __('admin.users.table.name') }}</th>
                        <th class="w-1/4 px-4 py-3 font-medium">{{ __('admin.users.table.email') }}</th>
                        <th class="w-1/3 px-4 py-3 font-medium">{{ __('admin.users.table.roles') }}</th>
                        <th class="w-1/6 px-4 py-3 text-right font-medium">{{ __('admin.users.table.action') }}</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($rows as $row)
                        <tr class="align-top transition-colors hover:bg-slate-50/60">
                            <td class="px-4 py-3 font-medium text-slate-900" style="overflow-wrap:anywhere;">
                                {{ $row->full_name }}
                            </td>

                            <td class="px-4 py-3 text-slate-700" style="overflow-wrap:anywhere;">
                                {{ $row->email }}
                            </td>

                            <td class="px-4 py-3">
                                <div class="flex flex-wrap gap-2">
                                    @forelse($row->roles as $role)
                                        <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-700 ring-1 ring-slate-200">
                                            {{ $role->name }}
                                        </span>
                                    @empty
                                        <span class="text-xs text-slate-400">{{ __('admin.users.no_roles') }}</span>
                                    @endforelse
                                </div>
                            </td>

                            <td class="px-4 py-3 text-right">
                                <a href="{{ localized_route('admin.users.roles', $row->id) }}" class="inline-flex items-center rounded-lg border border-brand-dark/20 bg-transparent px-3 py-2 text-xs font-medium text-brand-dark transition hover:bg-brand/10">
                                    {{ __('admin.users.actions.manage_roles') }}
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-slate-500">
                                {{ __('admin.users.empty') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div>{{ $rows->links() }}</div>
</div>