@extends('layouts.app')
@section('title', 'Add Test')
@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
        <h2 class="text-xl font-semibold mb-6">Add New Test</h2>
        <form action="{{ route('tests.store') }}" method="POST" class="space-y-6">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Test Name *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required class="w-full px-4 py-3 border border-gray-200 rounded-xl">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Test Code *</label>
                    <input type="text" name="code" value="{{ old('code') }}" required class="w-full px-4 py-3 border border-gray-200 rounded-xl">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Category *</label>
                    <select name="category_id" required class="w-full px-4 py-3 border border-gray-200 rounded-xl">
                        <option value="">Select Category</option>
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Price (₹) *</label>
                    <input type="number" name="price" value="{{ old('price') }}" step="0.01" required class="w-full px-4 py-3 border border-gray-200 rounded-xl">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Unit</label>
                    <input type="text" name="unit" value="{{ old('unit') }}" placeholder="mg/dL, g/dL, etc." class="w-full px-4 py-3 border border-gray-200 rounded-xl">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Normal Min</label>
                    <input type="number" name="normal_min" value="{{ old('normal_min') }}" step="0.01" class="w-full px-4 py-3 border border-gray-200 rounded-xl">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Normal Max</label>
                    <input type="number" name="normal_max" value="{{ old('normal_max') }}" step="0.01" class="w-full px-4 py-3 border border-gray-200 rounded-xl">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Sample Type</label>
                    <select name="sample_type" class="w-full px-4 py-3 border border-gray-200 rounded-xl">
                        @foreach(['blood', 'urine', 'stool', 'swab', 'other'] as $type)
                        <option value="{{ $type }}" {{ old('sample_type', 'blood') == $type ? 'selected' : '' }}>{{ ucfirst($type) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Turnaround Time (hrs)</label>
                    <input type="number" name="turnaround_time" value="{{ old('turnaround_time', 24) }}" class="w-full px-4 py-3 border border-gray-200 rounded-xl">
                </div>
            </div>
            <div class="flex gap-4">
                <button type="submit" class="px-6 py-3 bg-primary-600 text-white rounded-xl font-medium hover:bg-primary-700">Create Test</button>
                <a href="{{ route('tests.index') }}" class="px-6 py-3 bg-gray-100 text-gray-700 rounded-xl">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
