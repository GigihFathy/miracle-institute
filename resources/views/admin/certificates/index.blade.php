@extends('layouts.admin')

@section('content')
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold">Certificates</h1>
                <p class="text-gray-500">Pantau penerbitan sertifikat topic dan topik pembelajaran.</p>
            </div>
            <button class="px-4 py-2 rounded-md bg-black text-white text-sm">
                Refresh
            </button>
        </div>

        <div class="rounded-2xl bg-white border overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-left">
                    <tr>
                        <th class="p-4">Certificate #</th>
                        <th class="p-4">User</th>
                        <th class="p-4">Type</th>
                        <th class="p-4">Issued At</th>
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