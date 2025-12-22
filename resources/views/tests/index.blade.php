@extends('layouts.app')
@section('title', 'Test Catalogue')
@section('content')
<div class="flex justify-between items-center mb-6">
    <form action="{{ route('tests.index') }}" method="GET" class="flex gap-3">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search tests..." class="px-4 py-2 border border-gray-200 rounded-xl w-48">
        <select name="category" class="px-4 py-2 border border-gray-200 rounded-xl">
            <option value="">All Categories</option>
            @foreach($categories as $cat)
            <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
            @endforeach
        </select>
        <button type="submit" class="px-4 py-2 bg-gray-100 rounded-xl hover:bg-gray-200">Filter</button>
    </form>
    <div class="flex gap-3">
        <a href="{{ route('tests.categories') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200">Manage Categories</a>
        <a href="{{ route('tests.create') }}" class="px-4 py-2 bg-primary-600 text-white rounded-xl font-medium hover:bg-primary-700">Add Test</a>
    </div>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Code</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Test Name</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Category</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Normal Range</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Price</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse($tests as $test)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 font-mono text-sm">{{ $test->code }}</td>
                <td class="px-6 py-4 font-medium">{{ $test->name }}</td>
                <td class="px-6 py-4 text-sm">{{ $test->category->name ?? '-' }}</td>
                <td class="px-6 py-4 text-sm text-gray-600">{{ $test->normal_range }} {{ $test->unit }}</td>
                <td class="px-6 py-4 font-medium text-primary-600">₹{{ number_format($test->price, 2) }}</td>
                <td class="px-6 py-4">
                    <a href="{{ route('tests.edit', $test) }}" class="px-3 py-1.5 bg-gray-100 text-gray-700 text-sm rounded-lg hover:bg-gray-200">Edit</a>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="px-6 py-8 text-center text-gray-500">No tests found</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-6">{{ $tests->links() }}</div>
@endsection
