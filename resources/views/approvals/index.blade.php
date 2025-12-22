@extends('layouts.app')
@section('title', 'Pending Approvals')
@section('content')
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100">
        <h2 class="text-lg font-semibold">Results Awaiting Approval</h2>
    </div>
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
        <tbody class="divide-y">
            @forelse($pendingResults as $result)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4">
                    <p class="font-medium">{{ $result->bookingTest->booking->patient->name }}</p>
                    <p class="text-sm text-gray-500">{{ $result->bookingTest->booking->booking_id }}</p>
                </td>
                <td class="px-6 py-4">{{ $result->bookingTest->test->name }}</td>
                <td class="px-6 py-4">
                    <span class="font-mono">{{ $result->value }}</span>
                    @if($result->flag && $result->flag !== 'normal')
                    <span class="ml-2 px-2 py-0.5 rounded text-xs font-bold {{ $result->flag_badge }}">{{ $result->flag_label }}</span>
                    @endif
                </td>
                <td class="px-6 py-4 text-sm">{{ $result->enteredBy?->name ?? 'N/A' }}</td>
                <td class="px-6 py-4">
                    <div class="flex gap-2">
                        <form action="{{ route('approvals.approve', $result) }}" method="POST">
                            @csrf
                            <button type="submit" class="px-3 py-1.5 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700">Approve</button>
                        </form>
                        <a href="{{ route('approvals.show', $result->bookingTest->booking) }}" class="px-3 py-1.5 bg-gray-100 text-gray-700 text-sm rounded-lg hover:bg-gray-200">Review</a>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="5" class="px-6 py-8 text-center text-gray-500">No results pending approval! 🎉</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-6">{{ $pendingResults->links() }}</div>
@endsection
