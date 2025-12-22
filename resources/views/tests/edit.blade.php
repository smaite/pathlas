@extends('layouts.app')
@section('title', 'Edit Test')
@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
        <h2 class="text-xl font-semibold mb-6">Edit: {{ $test->name }}</h2>
        <form action="{{ route('tests.update', $test) }}" method="POST" class="space-y-6">
            @csrf @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Test Name *</label>
                    <input type="text" name="name" value="{{ old('name', $test->name) }}" required class="w-full px-4 py-3 border border-gray-200 rounded-xl">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Test Code *</label>
                    <input type="text" name="code" value="{{ old('code', $test->code) }}" required class="w-full px-4 py-3 border border-gray-200 rounded-xl">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Category *</label>
                    <select name="category_id" required class="w-full px-4 py-3 border border-gray-200 rounded-xl">
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ $test->category_id == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Price (₹) *</label>
                    <input type="number" name="price" value="{{ old('price', $test->price) }}" step="0.01" required class="w-full px-4 py-3 border border-gray-200 rounded-xl">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Unit</label>
                    <input type="text" name="unit" value="{{ old('unit', $test->unit) }}" class="w-full px-4 py-3 border border-gray-200 rounded-xl">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Normal Min</label>
                    <input type="number" name="normal_min" value="{{ old('normal_min', $test->normal_min) }}" step="0.01" class="w-full px-4 py-3 border border-gray-200 rounded-xl">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Normal Max</label>
                    <input type="number" name="normal_max" value="{{ old('normal_max', $test->normal_max) }}" step="0.01" class="w-full px-4 py-3 border border-gray-200 rounded-xl">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Sample Type</label>
                    <select name="sample_type" class="w-full px-4 py-3 border border-gray-200 rounded-xl">
                        @foreach(['blood', 'urine', 'stool', 'swab', 'other'] as $type)
                        <option value="{{ $type }}" {{ $test->sample_type == $type ? 'selected' : '' }}>{{ ucfirst($type) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-center">
                    <label class="flex items-center space-x-3">
                        <input type="checkbox" name="is_active" value="1" {{ $test->is_active ? 'checked' : '' }} class="w-5 h-5 text-primary-600 rounded">
                        <span class="font-medium">Active</span>
                    </label>
                </div>
            </div>
            <div class="flex gap-4">
                <button type="submit" class="px-6 py-3 bg-primary-600 text-white rounded-xl font-medium hover:bg-primary-700">Update Test</button>
                <a href="{{ route('tests.index') }}" class="px-6 py-3 bg-gray-100 text-gray-700 rounded-xl">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
