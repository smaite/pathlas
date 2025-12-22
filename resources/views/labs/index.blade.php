@extends('layouts.app')
@section('title', 'Lab Management')
@section('content')
<div class="flex justify-between items-center mb-6">
    <h2 class="text-xl font-semibold">Labs / Branches</h2>
    <a href="{{ route('labs.create') }}" class="px-5 py-2 bg-primary-600 text-white rounded-xl font-medium hover:bg-primary-700">
        + Add Lab
    </a>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse($labs as $lab)
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition">
        <div class="p-6" style="border-top: 4px solid {{ $lab->header_color }};">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <h3 class="font-bold text-lg">{{ $lab->name }}</h3>
                    <p class="text-sm text-gray-500">{{ $lab->code }}</p>
                </div>
                <span class="px-2 py-1 text-xs rounded-full {{ $lab->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                    {{ $lab->is_active ? 'Active' : 'Inactive' }}
                </span>
            </div>

            @if($lab->tagline)
            <p class="text-sm text-gray-600 mb-3">{{ $lab->tagline }}</p>
            @endif

            <div class="space-y-2 text-sm text-gray-600 border-t pt-3">
                @if($lab->phone)
                <p>📞 {{ $lab->phone }}{{ $lab->phone2 ? ', ' . $lab->phone2 : '' }}</p>
                @endif
                @if($lab->email)
                <p>✉️ {{ $lab->email }}</p>
                @endif
                @if($lab->city)
                <p>📍 {{ $lab->city }}, {{ $lab->state }}</p>
                @endif
            </div>

            <div class="grid grid-cols-3 gap-2 mt-4 pt-3 border-t text-center">
                <div>
                    <p class="text-lg font-bold text-primary-600">{{ $lab->users_count }}</p>
                    <p class="text-xs text-gray-500">Users</p>
                </div>
                <div>
                    <p class="text-lg font-bold text-primary-600">{{ $lab->patients_count }}</p>
                    <p class="text-xs text-gray-500">Patients</p>
                </div>
                <div>
                    <p class="text-lg font-bold text-primary-600">{{ $lab->bookings_count }}</p>
                    <p class="text-xs text-gray-500">Bookings</p>
                </div>
            </div>

            <div class="flex gap-2 mt-4 pt-3 border-t">
                <a href="{{ route('labs.edit', $lab) }}" class="flex-1 px-3 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm text-center hover:bg-gray-200">Edit</a>
                <form action="{{ route('labs.toggle-status', $lab) }}" method="POST" class="flex-1">
                    @csrf
                    <button type="submit" class="w-full px-3 py-2 {{ $lab->is_active ? 'bg-red-50 text-red-600' : 'bg-green-50 text-green-600' }} rounded-lg text-sm hover:opacity-80">
                        {{ $lab->is_active ? 'Disable' : 'Enable' }}
                    </button>
                </form>
            </div>
        </div>
    </div>
    @empty
    <div class="col-span-3 bg-white rounded-2xl p-12 text-center">
        <p class="text-gray-500 text-lg mb-4">No labs configured yet</p>
        <a href="{{ route('labs.create') }}" class="px-6 py-3 bg-primary-600 text-white rounded-xl">Create First Lab</a>
    </div>
    @endforelse
</div>
@endsection
