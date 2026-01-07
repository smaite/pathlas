@extends('layouts.app')
@section('title', 'Bookings')
@section('content')
<div class="flex justify-between items-center mb-6">
    <form action="{{ route('bookings.index') }}" method="GET" class="flex gap-3">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search..." class="px-4 py-2 border border-gray-200 rounded-xl w-48">
        <select name="status" class="px-4 py-2 border border-gray-200 rounded-xl">
            <option value="">All Status</option>
            @foreach(['pending', 'sample_collected', 'in_progress', 'completed', 'cancelled'] as $s)
            <option value="{{ $s }}" {{ request('status') == $s ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $s)) }}</option>
            @endforeach
        </select>
        <button type="submit" class="px-4 py-2 bg-gray-100 rounded-xl hover:bg-gray-200">Filter</button>
    </form>
    <a href="{{ route('bookings.create') }}" class="px-4 py-2 bg-primary-600 text-white rounded-xl font-medium hover:bg-primary-700">New Booking</a>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Booking ID</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Patient</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Tests</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Amount</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Payment</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($bookings as $booking)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 font-mono text-sm">{{ $booking->booking_id }}</td>
                <td class="px-6 py-4">
                    <p class="font-medium">{{ $booking->patient->name }}</p>
                    <p class="text-sm text-gray-500">{{ $booking->patient->phone }}</p>
                </td>
                <td class="px-6 py-4 text-sm">{{ $booking->bookingTests->count() }} tests</td>
                <td class="px-6 py-4 font-medium">₹{{ number_format($booking->total_amount, 2) }}</td>
                <td class="px-6 py-4"><span class="px-2.5 py-1 rounded-full text-xs font-medium {{ $booking->status_badge }}">{{ ucfirst(str_replace('_', ' ', $booking->status ?? '')) }}</span></td>
                <td class="px-6 py-4"><span class="px-2.5 py-1 rounded-full text-xs font-medium {{ $booking->payment_status == 'paid' ? 'bg-green-100 text-green-700' : ($booking->payment_status == 'partial' ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-700') }}">{{ ucfirst($booking->payment_status ?? '') }}</span></td>
                <td class="px-6 py-4">
                    <a href="{{ route('bookings.show', $booking) }}" class="px-3 py-1.5 bg-gray-100 text-gray-700 text-sm rounded-lg hover:bg-gray-200">View</a>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="px-6 py-8 text-center text-gray-500">No bookings found</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-6">{{ $bookings->links() }}</div>
@endsection
