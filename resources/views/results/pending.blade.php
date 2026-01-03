@extends('layouts.app')
@section('title', 'Pending Tests')
@section('content')
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100">
        <h2 class="text-lg font-semibold">Pending Test Results</h2>
        <p class="text-sm text-gray-500">Tests awaiting result entry</p>
    </div>
    <table class="w-full">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Patient</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Test</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Category</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Booking</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Action</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse($bookingTests as $bt)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4">
                    <p class="font-medium">{{ $bt->booking->patient->name }}</p>
                    <p class="text-sm text-gray-500">{{ $bt->booking->patient->age }} {{ $bt->booking->patient->gender ? ucfirst(substr($bt->booking->patient->gender, 0, 1)) : '' }}</p>
                </td>
                <td class="px-6 py-4 font-medium">{{ $bt->test->name }}</td>
                <td class="px-6 py-4 text-sm text-gray-600">{{ $bt->test->category->name ?? '-' }}</td>
                <td class="px-6 py-4 font-mono text-sm">{{ $bt->booking->booking_id }}</td>
                <td class="px-6 py-4">
                    @if($bt->result)
                    <span class="px-2 py-1 bg-yellow-100 text-yellow-700 text-xs rounded-full">{{ ucfirst($bt->result->status) }}</span>
                    @else
                    <span class="px-2 py-1 bg-gray-100 text-gray-600 text-xs rounded-full">No entry</span>
                    @endif
                </td>
                <td class="px-6 py-4">
                    <a href="{{ route('results.parameters', $bt) }}" class="px-4 py-2 bg-primary-600 text-white text-sm rounded-xl hover:bg-primary-700">Enter Result</a>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="px-6 py-8 text-center text-gray-500">No pending tests! 🎉</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-6">{{ $bookingTests->links() }}</div>
@endsection
