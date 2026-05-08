<div class="space-y-6">
    <x-ui.page-header title="Users" subtitle="Daftar pengguna dan role yang mereka miliki." />

    <div class="rounded-2xl bg-white border p-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <input wire:model.live="search" class="w-full border rounded-xl px-4 py-2" placeholder="Search user...">

            <select wire:model.live="roleFilter" class="border rounded-xl px-4 py-2">
                <option value="">All roles</option>
                @foreach($roles as $role)
                    <option value="{{ $role->name }}">{{ $role->name }}</option>
                @endforeach
            </select>

            <select wire:model.live="sort" class="border rounded-xl px-4 py-2">
                <option value="latest">Latest</option>
                <option value="name_asc">Name A-Z</option>
                <option value="name_desc">Name Z-A</option>
                <option value="email_asc">Email A-Z</option>
                <option value="email_desc">Email Z-A</option>
            </select>
        </div>
    </div>

    <div class="rounded-2xl bg-white border overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full min-w-full text-sm table-fixed">
                <thead class="bg-slate-50 text-left text-slate-600">
                    <tr>
                        <th class="px-4 py-3 w-1/4 font-medium">Name</th>
                        <th class="px-4 py-3 w-1/4 font-medium">Email</th>
                        <th class="px-4 py-3 w-1/3 font-medium">Roles</th>
                        <th class="px-4 py-3 w-1/6 font-medium text-right">Action</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($rows as $row)
                        <tr class="align-top hover:bg-slate-50/60 transition-colors">
                            <td class="px-4 py-3 font-medium text-slate-900" style="overflow-wrap:anywhere;">{{ $row->full_name }}</td>

                            <td class="px-4 py-3 text-slate-700" style="overflow-wrap:anywhere;">{{ $row->email }}</td>

                            <td class="px-4 py-3">
                                <div class="flex flex-wrap gap-2">
                                    @forelse($row->roles as $role)
                                        <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-700 ring-1 ring-slate-200">
                                            {{ $role->name }}
                                        </span>
                                    @empty
                                        <span class="text-xs text-slate-400">No roles</span>
                                    @endforelse
                                </div>
                            </td>

                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('admin.users.roles', $row->id) }}" class="inline-flex items-center rounded-lg bg-primary px-3 py-2 text-xs font-medium text-white hover:bg-primary-dark transition">
                                    Manage roles
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-slate-500">No users.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div>{{ $rows->links() }}</div>
</div>