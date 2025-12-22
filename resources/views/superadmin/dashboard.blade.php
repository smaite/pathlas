@extends('layouts.app')
@section('title', 'Super Admin Dashboard')
@section('content')
<div class="space-y-6">
    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Total Labs</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $stats['total_labs'] }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Pending Verification</p>
                    <p class="text-3xl font-bold text-yellow-600">{{ $stats['pending_labs'] }}</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Active Subscriptions</p>
                    <p class="text-3xl font-bold text-green-600">{{ $stats['active_subscriptions'] }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Expired</p>
                    <p class="text-3xl font-bold text-red-600">{{ $stats['expired_subscriptions'] }}</p>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Pending Verifications -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
            <div class="p-6 border-b border-gray-100">
                <h3 class="font-semibold text-lg">Pending Verifications</h3>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($pendingLabs as $lab)
                <div class="p-4 flex items-center justify-between">
                    <div>
                        <p class="font-medium">{{ $lab->name }}</p>
                        <p class="text-sm text-gray-500">{{ $lab->city }}, {{ $lab->state }} • {{ $lab->created_at->diffForHumans() }}</p>
                    </div>
                    <div class="flex gap-2">
                        <form action="{{ route('superadmin.verify-lab', $lab) }}" method="POST">
                            @csrf
                            <button class="px-3 py-1 bg-green-100 text-green-700 rounded-lg text-sm hover:bg-green-200">Verify</button>
                        </form>
                        <a href="{{ route('superadmin.lab-details', $lab) }}" class="px-3 py-1 bg-gray-100 text-gray-700 rounded-lg text-sm">View</a>
                    </div>
                </div>
                @empty
                <div class="p-6 text-center text-gray-500">No pending verifications</div>
                @endforelse
            </div>
            @if($stats['pending_labs'] > 5)
            <div class="p-4 border-t">
                <a href="{{ route('superadmin.labs') }}?status=pending" class="text-primary-600 text-sm">View all pending →</a>
            </div>
            @endif
        </div>

        <!-- Expiring Soon -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
            <div class="p-6 border-b border-gray-100">
                <h3 class="font-semibold text-lg">Expiring Soon (7 days)</h3>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($expiringLabs as $lab)
                <div class="p-4 flex items-center justify-between">
                    <div>
                        <p class="font-medium">{{ $lab->name }}</p>
                        <p class="text-sm text-red-500">Expires {{ $lab->subscription_expires_at->diffForHumans() }}</p>
                    </div>
                    <a href="{{ route('superadmin.lab-details', $lab) }}" class="px-3 py-1 bg-primary-100 text-primary-700 rounded-lg text-sm">Extend</a>
                </div>
                @empty
                <div class="p-6 text-center text-gray-500">No labs expiring soon</div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Recent Labs -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
        <div class="p-6 border-b border-gray-100 flex justify-between items-center">
            <h3 class="font-semibold text-lg">Recent Labs</h3>
            <a href="{{ route('superadmin.labs') }}" class="text-primary-600 text-sm">View All →</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Lab</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Location</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Subscription</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Registered</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($recentLabs as $lab)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <a href="{{ route('superadmin.lab-details', $lab) }}" class="font-medium text-primary-600 hover:underline">{{ $lab->name }}</a>
                            <p class="text-xs text-gray-500">{{ $lab->code }}</p>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $lab->city }}, {{ $lab->state }}</td>
                        <td class="px-6 py-4">
                            @if($lab->is_verified)
                            <span class="px-2 py-1 text-xs bg-green-100 text-green-700 rounded-full">Verified</span>
                            @else
                            <span class="px-2 py-1 text-xs bg-yellow-100 text-yellow-700 rounded-full">Pending</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs {{ $lab->subscription_badge }} rounded-full">
                                {{ ucfirst(str_replace('_', ' ', $lab->subscription_status)) }}
                            </span>
                            @if($lab->days_remaining !== null)
                            <span class="text-xs text-gray-500 ml-1">{{ $lab->days_remaining }}d</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $lab->created_at->format('M d, Y') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
