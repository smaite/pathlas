@extends('layouts.app')
@section('title', 'Reception Dashboard')
@section('content')
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="stat-card bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
        <p class="text-sm font-medium text-gray-500">Today's Patients</p>
        <p class="text-3xl font-bold text-gray-800 mt-1">{{ $stats['patients_today'] }}</p>
    </div>
    <div class="stat-card bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
        <p class="text-sm font-medium text-gray-500">Today's Bookings</p>
        <p class="text-3xl font-bold text-gray-800 mt-1">{{ $stats['bookings_today'] }}</p>
    </div>
    <div class="stat-card bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
        <p class="text-sm font-medium text-gray-500">Pending Bookings</p>
        <p class="text-3xl font-bold text-gray-800 mt-1">{{ $stats['pending_bookings'] }}</p>
    </div>
    <div class="stat-card bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
        <p class="text-sm font-medium text-gray-500">Unpaid Bookings</p>
        <p class="text-3xl font-bold text-gray-800 mt-1">{{ $stats['unpaid_bookings'] }}</p>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
    <a href="{{ route('patients.create') }}" class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-2xl p-6 text-white hover:shadow-xl transition">
        <h3 class="font-bold text-xl">Register New Patient</h3>
        <p class="text-indigo-100">Add patient details</p>
    </a>
    <a href="{{ route('bookings.create') }}" class="bg-gradient-to-r from-emerald-500 to-teal-600 rounded-2xl p-6 text-white hover:shadow-xl transition">
        <h3 class="font-bold text-xl">Create New Booking</h3>
        <p class="text-emerald-100">Select tests and generate invoice</p>
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-lg font-semibold">Recent Patients</h2>
        </div>
        <div class="divide-y">
            @forelse($recentPatients as $patient)
            <a href="{{ route('patients.show', $patient) }}" class="block px-6 py-4 hover:bg-gray-50">
                <p class="font-medium">{{ $patient->name }}</p>
                <p class="text-sm text-gray-500">{{ $patient->patient_id }}</p>
            </a>
            @empty
            <p class="px-6 py-8 text-center text-gray-500">No patients yet</p>
            @endforelse
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-lg font-semibold">Pending Payments</h2>
        </div>
        <div class="divide-y">
            @forelse($pendingPayments as $booking)
            <a href="{{ route('bookings.show', $booking) }}" class="block px-6 py-4 hover:bg-gray-50">
                <div class="flex justify-between">
                    <div>
                        <p class="font-medium">{{ $booking->patient->name }}</p>
                        <p class="text-sm text-gray-500">{{ $booking->booking_id }}</p>
                    </div>
                    <p class="font-semibold text-red-600">₹{{ number_format($booking->due_amount, 2) }}</p>
                </div>
            </a>
            @empty
            <p class="px-6 py-8 text-center text-gray-500">All payments up to date!</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
