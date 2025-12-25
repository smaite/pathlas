@extends('layouts.app')
@section('title', 'Booking: ' . $booking->booking_id)
@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">
        <!-- Booking Info -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex justify-between items-start mb-6">
                <div>
                    <h2 class="text-xl font-bold">{{ $booking->booking_id }}</h2>
                    <p class="text-gray-500">{{ $booking->created_at->format('M d, Y h:i A') }}</p>
                </div>
                <span class="px-3 py-1.5 rounded-full text-sm font-medium {{ $booking->status_badge }}">{{ ucfirst(str_replace('_', ' ', $booking->status)) }}</span>
            </div>
            
            <div class="grid grid-cols-2 gap-4 mb-6">
                <div>
                    <p class="text-sm text-gray-500">Patient</p>
                    <p class="font-medium">{{ $booking->patient->name }}</p>
                    <p class="text-sm text-gray-600">{{ $booking->patient->phone }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Created By</p>
                    <p class="font-medium">{{ $booking->createdBy?->name ?? 'N/A' }}</p>
                </div>
            </div>

            @if($booking->status === 'pending')
            <form action="{{ route('bookings.status', $booking) }}" method="POST" class="inline">
                @csrf
                <input type="hidden" name="status" value="sample_collected">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700">Mark Sample Collected</button>
            </form>
            @endif
        </div>

        <!-- Tests -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100"><h3 class="font-semibold">Tests & Results</h3></div>
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Test</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Result</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Normal Range</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($booking->bookingTests as $bt)
                    <tr>
                        <td class="px-6 py-4">
                            <p class="font-medium">{{ $bt->test->name }}</p>
                            <p class="text-sm text-gray-500">{{ $bt->test->category->name ?? '' }}</p>
                        </td>
                        <td class="px-6 py-4">
                            @if($bt->result && $bt->result->value)
                            <span class="font-mono">{{ $bt->result->value }} {{ $bt->test->unit }}</span>
                            @if($bt->result->flag && $bt->result->flag !== 'normal')
                            <span class="ml-2 px-2 py-0.5 rounded text-xs font-bold {{ $bt->result->flag_badge }}">{{ $bt->result->flag_label }}</span>
                            @endif
                            @else
                            <span class="text-gray-400">Pending</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $bt->test->normal_range }}</td>
                        <td class="px-6 py-4"><span class="px-2 py-1 rounded text-xs {{ $bt->status === 'completed' ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' }}">{{ ucfirst($bt->status) }}</span></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @php
            $allResultsApproved = $booking->bookingTests->count() > 0 && 
                $booking->bookingTests->every(fn($bt) => $bt->result && $bt->result->status === 'approved');
        @endphp

        @if($booking->report)
        <div class="bg-green-50 border border-green-200 rounded-2xl p-6">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="font-semibold text-green-800">Report Generated</h3>
                    <p class="text-sm text-green-600">{{ $booking->report->report_id }}</p>
                </div>
                <a href="{{ route('reports.download', $booking->report) }}" class="px-4 py-2 bg-green-600 text-white rounded-xl hover:bg-green-700">Download PDF</a>
            </div>
        </div>
        @elseif($booking->status === 'completed' || $allResultsApproved)
        <a href="{{ route('reports.generate', $booking) }}" class="block bg-primary-50 border border-primary-200 rounded-2xl p-6 text-center hover:bg-primary-100">
            <p class="font-semibold text-primary-700">✅ All Results Complete - Generate Report</p>
        </a>
        @endif
    </div>

    <!-- Sidebar -->
    <div class="space-y-6">
        <!-- Payment Summary -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-semibold mb-4">Payment Summary</h3>
            <div class="space-y-2 mb-4">
                <div class="flex justify-between"><span class="text-gray-600">Subtotal</span><span>₹{{ number_format($booking->subtotal, 2) }}</span></div>
                <div class="flex justify-between"><span class="text-gray-600">Discount</span><span class="text-red-600">-₹{{ number_format($booking->discount, 2) }}</span></div>
                <div class="flex justify-between font-bold text-lg pt-2 border-t"><span>Total</span><span>₹{{ number_format($booking->total_amount, 2) }}</span></div>
                <div class="flex justify-between"><span class="text-gray-600">Paid</span><span class="text-green-600">₹{{ number_format($booking->paid_amount, 2) }}</span></div>
                <div class="flex justify-between font-medium"><span>Due</span><span class="text-red-600">₹{{ number_format($booking->due_amount, 2) }}</span></div>
            </div>

            @if($booking->due_amount > 0)
            <form action="{{ route('bookings.payment', $booking) }}" method="POST" class="space-y-3">
                @csrf
                <input type="number" name="amount" step="0.01" max="{{ $booking->due_amount }}" required placeholder="Amount" class="w-full px-4 py-2 border border-gray-200 rounded-xl">
                <select name="method" required class="w-full px-4 py-2 border border-gray-200 rounded-xl">
                    <option value="cash">Cash</option>
                    <option value="card">Card</option>
                    <option value="upi">UPI</option>
                </select>
                <button type="submit" class="w-full px-4 py-2 bg-green-600 text-white rounded-xl hover:bg-green-700">Record Payment</button>
            </form>
            @endif
        </div>

        <!-- Actions -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-semibold mb-4">Actions</h3>
            <div class="space-y-2">
                <a href="{{ route('bookings.invoice', $booking) }}" class="block w-full px-4 py-2 bg-gray-100 text-gray-700 rounded-xl text-center hover:bg-gray-200">View Invoice</a>
                <a href="{{ route('bookings.invoice.pdf', $booking) }}" class="block w-full px-4 py-2 bg-gray-100 text-gray-700 rounded-xl text-center hover:bg-gray-200">Download Invoice PDF</a>
            </div>
        </div>
    </div>
</div>
@endsection
