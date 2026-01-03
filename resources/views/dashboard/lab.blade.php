@extends('layouts.app')
@section('title', 'Lab Dashboard')
@section('content')
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="stat-card bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
        <p class="text-sm font-medium text-gray-500">Pending Tests</p>
        <p class="text-3xl font-bold text-amber-600 mt-1">{{ $stats['pending_tests'] }}</p>
    </div>
    <div class="stat-card bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
        <p class="text-sm font-medium text-gray-500">In Progress</p>
        <p class="text-3xl font-bold text-blue-600 mt-1">{{ $stats['in_progress'] }}</p>
    </div>
    <div class="stat-card bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
        <p class="text-sm font-medium text-gray-500">Completed Today</p>
        <p class="text-3xl font-bold text-green-600 mt-1">{{ $stats['completed_today'] }}</p>
    </div>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100">
    <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
        <h2 class="text-lg font-semibold">Pending Test Results</h2>
        <a href="{{ route('results.pending') }}" class="btn-primary px-4 py-2 bg-primary-600 text-white rounded-xl font-medium hover:bg-primary-700">View All</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Patient</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Test</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Booking ID</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($pendingTests as $result)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <p class="font-medium">{{ $result->bookingTest->booking->patient->name }}</p>
                        <p class="text-sm text-gray-500">{{ $result->bookingTest->booking->patient->age }}{{ $result->bookingTest->booking->patient->gender ? substr($result->bookingTest->booking->patient->gender, 0, 1) : '' }}</p>
                    </td>
                    <td class="px-6 py-4">
                        <p class="font-medium">{{ $result->bookingTest->test->name }}</p>
                        <p class="text-sm text-gray-500">{{ $result->bookingTest->test->category->name ?? '' }}</p>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $result->bookingTest->booking->booking_id }}</td>
                    <td class="px-6 py-4">
                        <a href="{{ route('results.entry', $result) }}" class="px-3 py-1.5 bg-primary-50 text-primary-600 text-sm font-medium rounded-lg hover:bg-primary-100">Enter Result</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-8 text-center text-gray-500">No pending tests</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection