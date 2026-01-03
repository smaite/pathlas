@extends('layouts.app')
@section('title', 'Review Booking')
@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
        <div class="flex justify-between items-start mb-6">
            <div>
                <h2 class="text-xl font-bold">{{ $booking->patient->name }}</h2>
                <p class="text-gray-500">{{ $booking->booking_id }} • {{ $booking->patient->age }}{{ $booking->patient->gender ? substr($booking->patient->gender, 0, 1) : '' }}</p>
            </div>
            @if($hasEnteredResults)
            <form action="{{ route('approvals.approve-booking', $booking) }}" method="POST">
                @csrf
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-xl hover:bg-green-700">Approve All & Generate Report</button>
            </form>
            @endif
        </div>

        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Test</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Result</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Normal Range</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Entered By</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @foreach($booking->bookingTests as $bt)
                <tr>
                    <td class="px-4 py-4">
                        <p class="font-medium">{{ $bt->test->name }}</p>
                        <p class="text-sm text-gray-500">{{ $bt->test->category->name ?? '' }}</p>
                    </td>
                    <td class="px-4 py-4">
                        @if($bt->result && $bt->result->value)
                        <span class="font-mono">{{ $bt->result->value }} {{ $bt->test->unit }}</span>
                        @if($bt->result->flag && $bt->result->flag !== 'normal')
                        <span class="ml-2 px-2 py-0.5 rounded text-xs font-bold {{ $bt->result->flag_badge }}">{{ $bt->result->flag_label }}</span>
                        @endif
                        @else
                        <span class="text-gray-400">Pending</span>
                        @endif
                    </td>
                    <td class="px-4 py-4 text-sm">{{ $bt->test->normal_range }}</td>
                    <td class="px-4 py-4 text-sm">{{ $bt->result?->enteredBy?->name ?? '-' }}</td>
                    <td class="px-4 py-4"><span class="px-2 py-1 rounded text-xs {{ $bt->result?->status === 'approved' ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' }}">{{ ucfirst($bt->result?->status ?? 'pending') }}</span></td>
                    <td class="px-4 py-4">
                        @if($bt->result && in_array($bt->result->status, ['entered', 'verified']))
                        <form action="{{ route('approvals.approve', $bt->result) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="px-3 py-1 bg-green-600 text-white text-xs rounded hover:bg-green-700">Approve</button>
                        </form>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <a href="{{ route('approvals.index') }}" class="text-primary-600 hover:underline">← Back to Approvals</a>
</div>
@endsection