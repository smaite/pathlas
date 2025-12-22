@extends('layouts.app')
@section('title', 'User Management')
@section('content')
<div class="flex justify-between items-center mb-6">
    <form action="{{ route('users.index') }}" method="GET" class="flex gap-3">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search..." class="px-4 py-2 border border-gray-200 rounded-xl w-48">
        <select name="role" class="px-4 py-2 border border-gray-200 rounded-xl">
            <option value="">All Roles</option>
            @foreach($roles as $role)
            <option value="{{ $role->name }}" {{ request('role') == $role->name ? 'selected' : '' }}>{{ $role->display_name }}</option>
            @endforeach
        </select>
        <button type="submit" class="px-4 py-2 bg-gray-100 rounded-xl hover:bg-gray-200">Filter</button>
    </form>
    <a href="{{ route('users.create') }}" class="px-4 py-2 bg-primary-600 text-white rounded-xl font-medium hover:bg-primary-700">Add User</a>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Name</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Email</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Role</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse($users as $user)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 font-medium">{{ $user->name }}</td>
                <td class="px-6 py-4">{{ $user->email }}</td>
                <td class="px-6 py-4"><span class="px-2 py-1 bg-primary-100 text-primary-700 text-xs rounded-full">{{ $user->role?->display_name ?? 'N/A' }}</span></td>
                <td class="px-6 py-4"><span class="px-2 py-1 rounded text-xs {{ $user->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">{{ ucfirst($user->status) }}</span></td>
                <td class="px-6 py-4">
                    <div class="flex gap-2">
                        <a href="{{ route('users.edit', $user) }}" class="px-3 py-1.5 bg-gray-100 text-gray-700 text-sm rounded-lg hover:bg-gray-200">Edit</a>
                        <form action="{{ route('users.toggle-status', $user) }}" method="POST">
                            @csrf
                            <button type="submit" class="px-3 py-1.5 {{ $user->status === 'active' ? 'bg-red-50 text-red-600' : 'bg-green-50 text-green-600' }} text-sm rounded-lg hover:opacity-80">{{ $user->status === 'active' ? 'Deactivate' : 'Activate' }}</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="5" class="px-6 py-8 text-center text-gray-500">No users found</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-6">{{ $users->links() }}</div>
@endsection
