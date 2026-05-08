@extends('layouts.admin')

@section('content')
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold">Articles</h1>
                <p class="text-gray-500">Kelola artikel CMS sederhana.</p>
            </div>
            <x-ui.button variant="primary" size="md">+ New Article</x-ui.button>
        </div>

        <div class="rounded-2xl bg-white border overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-left">
                    <tr>
                        <th class="p-4">Title</th>
                        <th class="p-4">Author</th>
                        <th class="p-4">Clicks</th>
                        <th class="p-4">Status</th>
                        <th class="p-4">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-t">
                        <td class="p-4 text-gray-500" colspan="5">Data belum dihubungkan.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
@endsection