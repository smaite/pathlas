@extends('layouts.app')
@section('title', 'Test Categories')
@section('content')
<div class="max-w-4xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold">Test Categories</h2>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-semibold mb-4">Add New Category</h3>
            <form action="{{ route('tests.categories.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Name *</label>
                    <input type="text" name="name" required class="w-full px-4 py-3 border border-gray-200 rounded-xl">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Code *</label>
                    <input type="text" name="code" required class="w-full px-4 py-3 border border-gray-200 rounded-xl" placeholder="e.g., HAEM, BIOCHEM">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea name="description" rows="2" class="w-full px-4 py-3 border border-gray-200 rounded-xl"></textarea>
                </div>
                <button type="submit" class="w-full px-4 py-3 bg-primary-600 text-white rounded-xl font-medium hover:bg-primary-700">Add Category</button>
            </form>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b"><h3 class="font-semibold">Existing Categories</h3></div>
            <div class="divide-y">
                @forelse($categories as $cat)
                <div class="px-6 py-4 flex justify-between items-center">
                    <div>
                        <p class="font-medium">{{ $cat->name }}</p>
                        <p class="text-sm text-gray-500">{{ $cat->code }} • {{ $cat->tests_count }} tests</p>
                    </div>
                    <span class="px-2 py-1 rounded text-xs {{ $cat->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">{{ $cat->is_active ? 'Active' : 'Inactive' }}</span>
                </div>
                @empty
                <p class="px-6 py-8 text-center text-gray-500">No categories yet</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
