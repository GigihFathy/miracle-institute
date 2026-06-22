@extends('layouts.admin')

@section('content')
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold">Topics</h1>
                <p class="text-gray-500">Kelola topic per topik pembelajaran, mentor, dan visibility.</p>
            </div>
            <button class="px-4 py-2 rounded-md bg-black text-white text-sm">
                + New Topic
            </button>
        </div>

        <div class="rounded-2xl bg-white border overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-left">
                    <tr>
                        <th class="p-4">Name</th>
                        <th class="p-4">Topik pembelajaran</th>
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