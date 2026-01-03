@extends('layouts.app')
@section('title', 'Reports')
@section('content')
<div class="flex justify-between items-center mb-6">
    <form action="{{ route('reports.index') }}" method="GET" class="flex gap-3">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search..." class="px-4 py-2 border border-gray-200 rounded-xl w-48">
        <input type="date" name="date" value="{{ request('date') }}" class="px-4 py-2 border border-gray-200 rounded-xl">
        <button type="submit" class="px-4 py-2 bg-gray-100 rounded-xl hover:bg-gray-200">Filter</button>
    </form>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Report ID</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Patient</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Booking</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Generated</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse($reports as $report)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 font-mono text-sm">{{ $report->report_id }}</td>
                <td class="px-6 py-4">{{ $report->booking->patient->name }}</td>
                <td class="px-6 py-4 text-sm">{{ $report->booking->booking_id }}</td>
                <td class="px-6 py-4 text-sm text-gray-500">{{ $report->generated_at?->format('M d, Y h:i A') ?? '-' }}</td>
                <td class="px-6 py-4">
                    <div class="flex items-center gap-2">
                        <select onchange="updateLinks(this, '{{ $report->report_id }}')" class="text-xs border-gray-200 rounded-lg py-1 pl-2 pr-6">
                            <option value="default">Default</option>
                            <option value="modern1">Modern 1</option>
                            <option value="modern2">Modern 2</option>
                        </select>

                        <a href="{{ route('reports.download', ['report' => $report, 'header' => 'yes', 'stream' => 'true']) }}" id="view-{{ $report->report_id }}" target="_blank" class="px-3 py-1.5 bg-gray-100 text-gray-700 text-sm rounded-lg hover:bg-gray-200" title="View in Browser">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                        </a>

                        <a href="{{ route('reports.download', $report) }}" id="dl-{{ $report->report_id }}" class="px-3 py-1.5 bg-primary-600 text-white text-sm rounded-lg hover:bg-primary-700" title="Download">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                        </a>

                        <form action="{{ route('reports.regenerate', $report) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" onclick="return confirm('Regenerate this report?')" class="px-3 py-1.5 bg-yellow-100 text-yellow-700 text-sm rounded-lg hover:bg-yellow-200" title="Regenerate">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="5" class="px-6 py-8 text-center text-gray-500">No reports found</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-6">{{ $reports->links() }}</div>

@push('scripts')
<script>
    function updateLinks(select, reportId) {
        const template = select.value;
        const viewBtn = document.getElementById('view-' + reportId);
        const dlBtn = document.getElementById('dl-' + reportId);

        // Update View Link (Stream)
        let viewUrl = new URL(viewBtn.href);
        viewUrl.searchParams.set('template', template);
        viewUrl.searchParams.set('stream', 'true'); // Add flag for streaming
        viewBtn.href = viewUrl.toString();

        // Update Download Link
        let dlUrl = new URL(dlBtn.href);
        dlUrl.searchParams.set('template', template);
        dlBtn.href = dlUrl.toString();
    }
</script>
@endpush
@endsection
