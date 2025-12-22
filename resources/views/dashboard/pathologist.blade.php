@extends('layouts.app')
@section('title', 'Pathologist Dashboard')
@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
    <div class="stat-card bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
        <p class="text-sm font-medium text-gray-500">Pending Approval</p>
        <p class="text-3xl font-bold text-amber-600 mt-1">{{ $stats['pending_approval'] }}</p>
    </div>
    <div class="stat-card bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
        <p class="text-sm font-medium text-gray-500">Approved Today</p>
        <p class="text-3xl font-bold text-green-600 mt-1">{{ $stats['approved_today'] }}</p>
    </div>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100">
    <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
        <h2 class="text-lg font-semibold">Results Awaiting Approval</h2>
        <a href="{{ route('approvals.index') }}" class="px-4 py-2 bg-primary-600 text-white rounded-xl font-medium hover:bg-primary-700">View All</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Patient</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Test</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Result</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Entered By</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($pendingApprovals as $result)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">{{ $result->bookingTest->booking->patient->name }}</td>
                    <td class="px-6 py-4">{{ $result->bookingTest->test->name }}</td>
                    <td class="px-6 py-4">
                        <span class="font-mono">{{ $result->value }}</span>
                        @if($result->flag && $result->flag !== 'normal')
                        <span class="ml-2 px-2 py-0.5 rounded text-xs font-bold {{ $result->flag_badge }}">{{ $result->flag_label }}</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm">{{ $result->enteredBy?->name ?? 'N/A' }}</td>
                    <td class="px-6 py-4">
                        <a href="{{ route('approvals.show', $result->bookingTest->booking) }}" class="px-3 py-1.5 bg-green-50 text-green-600 text-sm font-medium rounded-lg hover:bg-green-100">Review</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-gray-500">No results pending approval</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
