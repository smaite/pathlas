@extends('layouts.app')
@section('title', 'Patient: ' . $patient->name)
@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-1">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="text-center mb-6">
                <div class="w-20 h-20 bg-gradient-to-br from-primary-500 to-indigo-600 rounded-2xl mx-auto flex items-center justify-center text-white text-2xl font-bold">
                    {{ substr($patient->name ?? '', 0, 1) }}
                </div>
                <h2 class="text-xl font-bold mt-4">{{ $patient->name }}</h2>
                <p class="text-gray-500">{{ $patient->patient_id }}</p>
            </div>
            <div class="space-y-4">
                <div class="flex justify-between"><span class="text-gray-500">Age</span><span class="font-medium">{{ $patient->age }} years</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Gender</span><span class="font-medium capitalize">{{ $patient->gender }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Phone</span><span class="font-medium">{{ $patient->phone }}</span></div>
                @if($patient->email)<div class="flex justify-between"><span class="text-gray-500">Email</span><span class="font-medium">{{ $patient->email }}</span></div>@endif
                @if($patient->blood_group)<div class="flex justify-between"><span class="text-gray-500">Blood Group</span><span class="font-medium">{{ $patient->blood_group }}</span></div>@endif
            </div>
            <div class="mt-6 flex gap-3">
                <a href="{{ route('patients.edit', $patient) }}" class="flex-1 px-4 py-2 bg-gray-100 text-gray-700 rounded-xl text-center hover:bg-gray-200">Edit</a>
                <a href="{{ route('bookings.create', ['patient_id' => $patient->id]) }}" class="flex-1 px-4 py-2 bg-primary-600 text-white rounded-xl text-center hover:bg-primary-700">Book Test</a>
            </div>
        </div>
    </div>

    <div class="lg:col-span-2">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="text-lg font-semibold">Booking History</h2>
            </div>
            <div class="divide-y">
                @forelse($patient->bookings as $booking)
                <a href="{{ route('bookings.show', $booking) }}" class="block px-6 py-4 hover:bg-gray-50">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="font-medium">{{ $booking->booking_id }}</p>
                            <p class="text-sm text-gray-500">{{ $booking->created_at->format('M d, Y h:i A') }}</p>
                            <div class="mt-2 flex flex-wrap gap-1">
                                @foreach($booking->bookingTests as $bt)
                                <span class="px-2 py-0.5 bg-gray-100 text-gray-600 text-xs rounded">{{ $bt->test->name }}</span>
                                @endforeach
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="px-2.5 py-1 rounded-full text-xs font-medium {{ $booking->status_badge }}">{{ ucfirst(str_replace('_', ' ', $booking->status ?? '')) }}</span>
                            <p class="mt-2 font-semibold">₹{{ number_format($booking->total_amount, 2) }}</p>
                        </div>
                    </div>
                </a>
                @empty
                <p class="px-6 py-8 text-center text-gray-500">No bookings yet</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
