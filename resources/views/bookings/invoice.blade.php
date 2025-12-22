@extends('layouts.app')
@section('title', 'Invoice: ' . $booking->booking_id)
@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
        <div class="flex justify-between items-start mb-8">
            <div>
                <h1 class="text-2xl font-bold text-primary-600">PathLAS</h1>
                <p class="text-gray-500">Pathology Lab Automation System</p>
            </div>
            <div class="text-right">
                <h2 class="text-xl font-bold">INVOICE</h2>
                <p class="text-gray-600">{{ $booking->booking_id }}</p>
                <p class="text-sm text-gray-500">{{ $booking->created_at->format('M d, Y') }}</p>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-8 mb-8 pb-8 border-b">
            <div>
                <h3 class="font-semibold text-gray-500 mb-2">Bill To:</h3>
                <p class="font-medium text-lg">{{ $booking->patient->name }}</p>
                <p class="text-gray-600">{{ $booking->patient->phone }}</p>
                @if($booking->patient->email)<p class="text-gray-600">{{ $booking->patient->email }}</p>@endif
            </div>
            <div class="text-right">
                <p><span class="text-gray-500">Patient ID:</span> {{ $booking->patient->patient_id }}</p>
                <p><span class="text-gray-500">Age/Gender:</span> {{ $booking->patient->age }} / {{ ucfirst($booking->patient->gender) }}</p>
            </div>
        </div>

        <table class="w-full mb-8">
            <thead>
                <tr class="border-b">
                    <th class="text-left py-3">Test Name</th>
                    <th class="text-right py-3">Price</th>
                </tr>
            </thead>
            <tbody>
                @foreach($booking->bookingTests as $bt)
                <tr class="border-b">
                    <td class="py-3">{{ $bt->test->name }}</td>
                    <td class="py-3 text-right">₹{{ number_format($bt->price, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr><td class="py-2 text-right text-gray-600">Subtotal</td><td class="py-2 text-right">₹{{ number_format($booking->subtotal, 2) }}</td></tr>
                <tr><td class="py-2 text-right text-gray-600">Discount</td><td class="py-2 text-right text-red-600">-₹{{ number_format($booking->discount, 2) }}</td></tr>
                <tr class="font-bold text-lg"><td class="py-3 text-right">Total</td><td class="py-3 text-right">₹{{ number_format($booking->total_amount, 2) }}</td></tr>
            </tfoot>
        </table>

        <div class="flex justify-between items-center pt-4 border-t">
            <div>
                <span class="px-3 py-1 rounded-full text-sm font-medium {{ $booking->payment_status == 'paid' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">{{ ucfirst($booking->payment_status) }}</span>
            </div>
            <div class="space-x-3">
                <a href="{{ route('bookings.invoice.pdf', $booking) }}" class="px-4 py-2 bg-primary-600 text-white rounded-xl hover:bg-primary-700">Download PDF</a>
                <button onclick="window.print()" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200">Print</button>
            </div>
        </div>
    </div>
</div>
@endsection
