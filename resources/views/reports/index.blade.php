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
                    <div class="flex gap-2">
                        <a href="{{ route('reports.show', $report) }}" class="px-3 py-1.5 bg-gray-100 text-gray-700 text-sm rounded-lg hover:bg-gray-200">View</a>
                        <a href="{{ route('reports.download', $report) }}" class="px-3 py-1.5 bg-primary-600 text-white text-sm rounded-lg hover:bg-primary-700">Download</a>
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
@endsection
