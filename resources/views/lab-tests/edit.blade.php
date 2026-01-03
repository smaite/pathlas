@extends('layouts.app')
@section('title', 'Edit Test - ' . $test->name)
@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('lab-tests.index') }}" class="text-blue-600 text-sm hover:underline">← Back to Lab Tests</a>
        <h1 class="text-2xl font-bold text-gray-800 mt-2">Customize: {{ $test->name }}</h1>
        <p class="text-gray-500">Changes only affect your lab. Leave fields empty to use master values.</p>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <form action="{{ route('lab-tests.update', $test) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Master Values Reference -->
            <div class="bg-gray-50 rounded-xl p-4 mb-6">
                <h3 class="font-medium mb-2 text-gray-700">Master Values (Read Only)</h3>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div><span class="text-gray-500">Name:</span> {{ $test->name }}</div>
                    <div><span class="text-gray-500">Price:</span> Rs. {{ number_format($test->price, 2) }}</div>
                    <div><span class="text-gray-500">Unit:</span> {{ $test->unit ?: 'N/A' }}</div>
                    <div><span class="text-gray-500">Sample:</span> {{ $test->sample_type ?: 'N/A' }}</div>
                </div>
            </div>

            <!-- Override Fields -->
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Custom Price</label>
                    <input type="number" step="0.01" min="0" name="price" 
                        value="{{ $override?->overrides['price'] ?? '' }}"
                        class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                        placeholder="{{ $test->price }} (master)">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Custom Name</label>
                    <input type="text" name="name" 
                        value="{{ $override?->overrides['name'] ?? '' }}"
                        class="w-full px-4 py-2 border border-gray-200 rounded-xl"
                        placeholder="{{ $test->name }}">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Custom Unit</label>
                    <input type="text" name="unit" 
                        value="{{ $override?->overrides['unit'] ?? '' }}"
                        class="w-full px-4 py-2 border border-gray-200 rounded-xl"
                        placeholder="{{ $test->unit ?: 'None' }}">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Custom Normal Range</label>
                    <input type="text" name="normal_range" 
                        value="{{ $override?->overrides['normal_range'] ?? '' }}"
                        class="w-full px-4 py-2 border border-gray-200 rounded-xl"
                        placeholder="{{ $test->normal_range }}">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Sample Type</label>
                <input type="text" name="sample_type" 
                    value="{{ $override?->overrides['sample_type'] ?? '' }}"
                    class="w-full px-4 py-2 border border-gray-200 rounded-xl"
                    placeholder="{{ $test->sample_type ?: 'None' }}">
            </div>

            <div>
                <label class="flex items-center">
                    <input type="checkbox" name="is_active" value="1" 
                        {{ ($override?->is_active ?? true) ? 'checked' : '' }}
                        class="w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
                    <span class="ml-2 text-sm text-gray-700">Enable this test for your lab</span>
                </label>
                <p class="text-xs text-gray-500 mt-1">Uncheck to hide this test from your lab's test list</p>
            </div>

            <div class="flex gap-3 pt-4">
                <button type="submit" class="px-6 py-2 bg-primary-600 text-white rounded-xl hover:bg-primary-700">
                    Save Changes
                </button>
                <a href="{{ route('lab-tests.index') }}" class="px-6 py-2 border border-gray-200 rounded-xl hover:bg-gray-50">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
