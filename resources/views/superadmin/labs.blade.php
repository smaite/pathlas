@extends('layouts.app')
@section('title', 'All Labs')
@section('content')
<div class="space-y-6">
    <!-- Filters -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
        <form action="" method="GET" class="flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Lab name, code, email..." 
                       class="w-full px-4 py-2 border border-gray-200 rounded-xl">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select name="status" class="px-4 py-2 border border-gray-200 rounded-xl">
                    <option value="">All</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="verified" {{ request('status') === 'verified' ? 'selected' : '' }}>Verified</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active Subscription</option>
                    <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Expired</option>
                </select>
            </div>
            <button type="submit" class="px-6 py-2 bg-primary-600 text-white rounded-xl">Filter</button>
            <a href="{{ route('superadmin.labs') }}" class="px-4 py-2 text-gray-600">Reset</a>
        </form>
    </div>

    <!-- Labs Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase">Lab</th>
                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase">Contact</th>
                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase">Verification</th>
                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase">Subscription</th>
                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase">Stats</th>
                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($labs as $lab)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white font-bold" style="background: {{ $lab->header_color }}">
                                {{ substr($lab->name, 0, 1) }}
                            </div>
                            <div>
                                <a href="{{ route('superadmin.lab-details', $lab) }}" class="font-medium text-gray-900 hover:text-primary-600">{{ $lab->name }}</a>
                                <p class="text-xs text-gray-500">{{ $lab->code }} • {{ $lab->city }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <p class="text-sm">{{ $lab->email }}</p>
                        <p class="text-xs text-gray-500">{{ $lab->phone }}</p>
                    </td>
                    <td class="px-6 py-4">
                        @if($lab->is_verified)
                        <span class="px-2 py-1 text-xs bg-green-100 text-green-700 rounded-full">Verified</span>
                        @elseif($lab->rejection_reason)
                        <span class="px-2 py-1 text-xs bg-red-100 text-red-700 rounded-full">Rejected</span>
                        @else
                        <span class="px-2 py-1 text-xs bg-yellow-100 text-yellow-700 rounded-full">Pending</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs {{ $lab->subscription_badge }} rounded-full">
                            {{ ucfirst(str_replace('_', ' ', $lab->subscription_plan)) }}
                        </span>
                        @if($lab->subscription_expires_at)
                        <p class="text-xs text-gray-500 mt-1">
                            {{ $lab->subscription_expires_at->isPast() ? 'Expired' : 'Expires' }} 
                            {{ $lab->subscription_expires_at->format('M d, Y') }}
                        </p>
                        @elseif($lab->subscription_plan === 'lifetime')
                        <p class="text-xs text-green-600 mt-1">Never expires</p>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                        <p>{{ $lab->users_count }} users</p>
                        <p class="text-xs text-gray-400">{{ $lab->bookings_count }} bookings</p>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex gap-2">
                            <a href="{{ route('superadmin.lab-details', $lab) }}" class="px-3 py-1 bg-gray-100 text-gray-700 rounded-lg text-sm hover:bg-gray-200">View</a>
                            @if(!$lab->is_verified && !$lab->rejection_reason)
                            <form action="{{ route('superadmin.verify-lab', $lab) }}" method="POST">
                                @csrf
                                <button class="px-3 py-1 bg-green-100 text-green-700 rounded-lg text-sm hover:bg-green-200">Verify</button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-gray-500">No labs found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        
        @if($labs->hasPages())
        <div class="px-6 py-4 border-t">
            {{ $labs->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
