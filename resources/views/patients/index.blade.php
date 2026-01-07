@extends('layouts.app')
@section('title', 'Patients')
@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <form action="{{ route('patients.index') }}" method="GET" class="flex gap-3">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search patients..." 
                class="px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent w-64">
            <button type="submit" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200">Search</button>
        </form>
    </div>
    <a href="{{ route('patients.create') }}" class="px-4 py-2 bg-primary-600 text-white rounded-xl font-medium hover:bg-primary-700 flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        Add Patient
    </a>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Patient ID</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Name</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Age/Gender</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Phone</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Registered</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($patients as $patient)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 font-mono text-sm">{{ $patient->patient_id }}</td>
                <td class="px-6 py-4 font-medium">{{ $patient->name }}</td>
                <td class="px-6 py-4">{{ $patient->age }} / {{ ucfirst($patient->gender ?? '') }}</td>
                <td class="px-6 py-4">{{ $patient->phone }}</td>
                <td class="px-6 py-4 text-sm text-gray-500">{{ $patient->created_at->format('M d, Y') }}</td>
                <td class="px-6 py-4">
                    <div class="flex gap-2">
                        <a href="{{ route('patients.show', $patient) }}" class="px-3 py-1.5 bg-gray-100 text-gray-700 text-sm rounded-lg hover:bg-gray-200">View</a>
                        <a href="{{ route('bookings.create', ['patient_id' => $patient->id]) }}" class="px-3 py-1.5 bg-primary-50 text-primary-600 text-sm rounded-lg hover:bg-primary-100">Book</a>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="px-6 py-8 text-center text-gray-500">No patients found</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-6">{{ $patients->links() }}</div>
@endsection
