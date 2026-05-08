@extends('layouts.admin')

@section('content')
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold">Study Programs</h1>
                <p class="text-gray-500">Kelola kategori besar seperti Discipleship dan Sermon.</p>
            </div>
            <x-ui.button variant="primary" size="md">+ New Study Program</x-ui.button>
        </div>

        <div class="rounded-2xl bg-white border overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-left">
                    <tr>
                        <th class="p-4">Title</th>
                        <th class="p-4">Slug</th>
                        <th class="p-4">Status</th>
                        <th class="p-4">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-t">
                        <td class="p-4 text-gray-500" colspan="4">Data belum dihubungkan.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
@endsection