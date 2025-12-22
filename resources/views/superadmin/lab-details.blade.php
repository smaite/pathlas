@extends('layouts.app')
@section('title', $lab->name)
@section('content')
<div class="space-y-6">
    <!-- Lab Header -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="h-3" style="background: {{ $lab->header_color }}"></div>
        <div class="p-6">
            <div class="flex justify-between items-start">
                <div>
                    <h2 class="text-2xl font-bold">{{ $lab->name }}</h2>
                    <p class="text-gray-500">{{ $lab->code }} • {{ $lab->city }}, {{ $lab->state }}</p>
                </div>
                <div class="flex gap-2">
                    @if(!$lab->is_verified && !$lab->rejection_reason)
                    <form action="{{ route('superadmin.verify-lab', $lab) }}" method="POST">
                        @csrf
                        <button class="px-4 py-2 bg-green-600 text-white rounded-xl hover:bg-green-700">Verify Lab</button>
                    </form>
                    @endif
                    <form action="{{ route('superadmin.toggle-lab-status', $lab) }}" method="POST">
                        @csrf
                        <button class="px-4 py-2 {{ $lab->is_active ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }} rounded-xl">
                            {{ $lab->is_active ? 'Disable' : 'Enable' }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Lab Info -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Stats -->
            <div class="grid grid-cols-4 gap-4">
                <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 text-center">
                    <p class="text-2xl font-bold text-primary-600">{{ $stats['users'] }}</p>
                    <p class="text-sm text-gray-500">Users</p>
                </div>
                <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 text-center">
                    <p class="text-2xl font-bold text-primary-600">{{ $stats['patients'] }}</p>
                    <p class="text-sm text-gray-500">Patients</p>
                </div>
                <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 text-center">
                    <p class="text-2xl font-bold text-primary-600">{{ $stats['bookings'] }}</p>
                    <p class="text-sm text-gray-500">Bookings</p>
                </div>
                <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 text-center">
                    <p class="text-2xl font-bold text-primary-600">{{ $stats['reports'] }}</p>
                    <p class="text-sm text-gray-500">Reports</p>
                </div>
            </div>

            <!-- Contact Info -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                <h3 class="font-semibold mb-4">Contact Information</h3>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div><span class="text-gray-500">Email:</span> {{ $lab->email }}</div>
                    <div><span class="text-gray-500">Phone:</span> {{ $lab->phone }} {{ $lab->phone2 ? '/ '.$lab->phone2 : '' }}</div>
                    <div><span class="text-gray-500">Website:</span> {{ $lab->website ?? '-' }}</div>
                    <div><span class="text-gray-500">Address:</span> {{ $lab->full_address }}</div>
                </div>
            </div>

            <!-- Recent Bookings -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
                <div class="p-6 border-b"><h3 class="font-semibold">Recent Bookings</h3></div>
                <div class="divide-y">
                    @forelse($recentBookings as $booking)
                    <div class="p-4 flex justify-between">
                        <div>
                            <p class="font-medium">{{ $booking->patient->name }}</p>
                            <p class="text-sm text-gray-500">{{ $booking->booking_id }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm">₹{{ number_format($booking->total_amount) }}</p>
                            <p class="text-xs text-gray-500">{{ $booking->created_at->format('M d, Y') }}</p>
                        </div>
                    </div>
                    @empty
                    <div class="p-6 text-center text-gray-500">No bookings yet</div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Subscription & Status -->
        <div class="space-y-6">
            <!-- Status Card -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                <h3 class="font-semibold mb-4">Status</h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Verification:</span>
                        @if($lab->is_verified)
                        <span class="px-2 py-1 text-xs bg-green-100 text-green-700 rounded-full">Verified</span>
                        @elseif($lab->rejection_reason)
                        <span class="px-2 py-1 text-xs bg-red-100 text-red-700 rounded-full">Rejected</span>
                        @else
                        <span class="px-2 py-1 text-xs bg-yellow-100 text-yellow-700 rounded-full">Pending</span>
                        @endif
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Active:</span>
                        <span class="px-2 py-1 text-xs {{ $lab->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }} rounded-full">
                            {{ $lab->is_active ? 'Yes' : 'No' }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Registered:</span>
                        <span>{{ $lab->created_at->format('M d, Y') }}</span>
                    </div>
                </div>
            </div>

            <!-- Subscription Card -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                <h3 class="font-semibold mb-4">Subscription</h3>
                <div class="space-y-3 mb-6">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Plan:</span>
                        <span class="font-medium">{{ ucfirst(str_replace('_', ' ', $lab->subscription_plan)) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Status:</span>
                        <span class="px-2 py-1 text-xs {{ $lab->subscription_badge }} rounded-full">
                            {{ ucfirst(str_replace('_', ' ', $lab->subscription_status)) }}
                        </span>
                    </div>
                    @if($lab->subscription_expires_at)
                    <div class="flex justify-between">
                        <span class="text-gray-500">Expires:</span>
                        <span>{{ $lab->subscription_expires_at->format('M d, Y') }}</span>
                    </div>
                    @endif
                    @if($lab->subscription_amount > 0)
                    <div class="flex justify-between">
                        <span class="text-gray-500">Amount:</span>
                        <span>₹{{ number_format($lab->subscription_amount) }}</span>
                    </div>
                    @endif
                </div>

                <!-- Extend Form -->
                <form action="{{ route('superadmin.extend-subscription', $lab) }}" method="POST" class="space-y-4 border-t pt-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Plan</label>
                        <select name="subscription_plan" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                            <option value="free_trial">Free Trial</option>
                            <option value="monthly">Monthly</option>
                            <option value="yearly">Yearly</option>
                            <option value="lifetime">Lifetime</option>
                            <option value="custom">Custom</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Expires At</label>
                        <input type="date" name="expires_at" value="{{ now()->addMonth()->format('Y-m-d') }}" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Amount (₹)</label>
                        <input type="number" name="amount" step="0.01" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                    </div>
                    <button type="submit" class="w-full py-2 bg-primary-600 text-white rounded-lg text-sm">Update Subscription</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
