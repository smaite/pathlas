@extends('layouts.app')
@section('title', 'Activity Logs')
@section('content')
<div class="flex justify-between items-center mb-6">
    <form action="{{ route('activity-logs') }}" method="GET" class="flex gap-3">
        <select name="user_id" class="px-4 py-2 border border-gray-200 rounded-xl">
            <option value="">All Users</option>
            @foreach($users as $user)
            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
            @endforeach
        </select>
        <input type="date" name="date" value="{{ request('date') }}" class="px-4 py-2 border border-gray-200 rounded-xl">
        <button type="submit" class="px-4 py-2 bg-gray-100 rounded-xl hover:bg-gray-200">Filter</button>
    </form>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Time</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">User</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Action</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Details</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse($logs as $log)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 text-sm text-gray-500">{{ $log->created_at->format('M d, Y h:i A') }}</td>
                <td class="px-6 py-4">{{ $log->user?->name ?? 'System' }}</td>
                <td class="px-6 py-4"><span class="px-2 py-1 bg-gray-100 text-gray-700 text-xs rounded">{{ str_replace('_', ' ', $log->action) }}</span></td>
                <td class="px-6 py-4 text-sm text-gray-600">{{ $log->model_type ? class_basename($log->model_type) . ' #' . $log->model_id : '-' }}</td>
            </tr>
            @empty
            <tr><td colspan="4" class="px-6 py-8 text-center text-gray-500">No activity logs found</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-6">{{ $logs->links() }}</div>
@endsection
