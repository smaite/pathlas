@extends('layouts.app')
@section('title', 'Login Activity')
@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
            <div>
                <h2 class="text-lg font-semibold">Login Activity</h2>
                <p class="text-sm text-gray-500">Review recent login sessions from your account</p>
            </div>
            <form action="{{ route('profile.login-activity.clear') }}" method="POST" onsubmit="return confirm('Clear all login history?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 bg-red-50 text-red-600 rounded-xl text-sm hover:bg-red-100">
                    Clear History
                </button>
            </form>
        </div>
        
        <div class="divide-y">
            @forelse($activities as $activity)
            <div class="px-6 py-4 flex items-start gap-4 hover:bg-gray-50">
                <div class="text-3xl">{{ $activity->device_icon }}</div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-1">
                        <p class="font-medium text-gray-900">{{ $activity->browser }} on {{ $activity->platform }}</p>
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $activity->activity_badge }}">
                            {{ ucfirst(str_replace('_', ' ', $activity->activity_type)) }}
                        </span>
                    </div>
                    <div class="flex items-center gap-4 text-sm text-gray-500">
                        <span>{{ $activity->device_type }}</span>
                        <span>•</span>
                        <span>IP: {{ $activity->ip_address }}</span>
                        <span>•</span>
                        <span>{{ $activity->time_ago }}</span>
                    </div>
                    <p class="text-xs text-gray-400 mt-1 truncate">{{ $activity->user_agent }}</p>
                </div>
                <div class="text-right text-sm text-gray-500 whitespace-nowrap">
                    {{ $activity->created_at->format('M d, Y') }}<br>
                    {{ $activity->created_at->format('h:i A') }}
                </div>
            </div>
            @empty
            <div class="px-6 py-12 text-center text-gray-500">
                <div class="text-4xl mb-2">🔐</div>
                <p>No login activity recorded yet</p>
            </div>
            @endforelse
        </div>

        @if($activities->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $activities->links() }}
        </div>
        @endif
    </div>

    <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-xl">
        <p class="text-blue-800 text-sm">
            <strong>🔒 Security Tip:</strong> If you see any unfamiliar login activity, change your password immediately and contact your administrator.
        </p>
    </div>
</div>
@endsection
