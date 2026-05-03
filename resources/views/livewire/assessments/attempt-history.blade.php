<div class="rounded-2xl bg-white border p-5 space-y-4">
    <h2 class="text-lg font-semibold">Attempt History</h2>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="text-left text-slate-500 border-b">
                <tr>
                    <th class="py-2">Attempt</th>
                    <th>Score</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach($attempts as $attempt)
                    <tr class="border-b">
                        <td class="py-2">#{{ $attempt->attempt_no }}</td>
                        <td>{{ $attempt->score }}</td>
                        <td>
                            <span class="{{ $attempt->passed ? 'text-green-600' : 'text-red-600' }}">
                                {{ $attempt->passed ? 'Passed' : 'Failed' }}
                            </span>
                        </td>
                        <td>{{ $attempt->created_at->format('d M Y') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>