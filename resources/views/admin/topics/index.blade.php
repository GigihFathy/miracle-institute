@extends('layouts.admin')

@section('content')
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold">Topics</h1>
                <p class="text-gray-500">Kelola topic per course, mentor, dan visibility.</p>
            </div>
            <x-ui.button variant="primary" size="md">+ New Topic</x-ui.button>
        </div>

        <div class="rounded-2xl bg-white border overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-left">
                    <tr>
                        <th class="p-4">Name</th>
                        <th class="p-4">Course</th>
                        <th class="p-4">Teacher</th>
                        <th class="p-4">Visibility</th>
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