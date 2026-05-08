@extends('layouts.admin')

@section('content')
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold">Sessions</h1>
                <p class="text-gray-500">Kelola jadwal Zoom, recording, dan reminder.</p>
            </div>
            <x-ui.button variant="primary" size="md">+ New Session</x-ui.button>
        </div>

        <div class="rounded-2xl bg-white border overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-left">
                    <tr>
                        <th class="p-4">Title</th>
                        <th class="p-4">Topic</th>
                        <th class="p-4">Start</th>
                        <th class="p-4">End</th>
                        <th class="p-4">Status</th>
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