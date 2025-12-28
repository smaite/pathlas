@extends('layouts.app')
@section('title', 'Edit Package - ' . $package->name)
@section('content')
<div class="max-w-4xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold">Edit Package</h1>
        <a href="{{ route('packages.index') }}" class="text-gray-600 hover:text-gray-800">← Back</a>
    </div>

    <form action="{{ route('packages.update', $package) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
            <h2 class="text-lg font-semibold mb-4">Package Details</h2>
            
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Package Name *</label>
                    <input type="text" name="name" value="{{ old('name', $package->name) }}" required
                        class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500">
                    @error('name')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Code *</label>
                    <input type="text" name="code" value="{{ old('code', $package->code) }}" required
                        class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 uppercase">
                    @error('code')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <textarea name="description" rows="2"
                    class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500">{{ old('description', $package->description) }}</textarea>
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Package Price *</label>
                    <input type="number" name="price" value="{{ old('price', $package->price) }}" required step="0.01" min="0"
                        class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500">
                    @error('price')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">MRP</label>
                    <input type="number" name="mrp" value="{{ old('mrp', $package->mrp) }}" step="0.01" min="0"
                        class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="is_active" class="w-full px-4 py-2 border border-gray-200 rounded-lg">
                        <option value="1" {{ $package->is_active ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ !$package->is_active ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
            <h2 class="text-lg font-semibold mb-4">Select Tests *</h2>
            
            @php $selectedTests = old('tests', $package->tests->pluck('id')->toArray()); @endphp
            
            <div class="grid grid-cols-2 md:grid-cols-3 gap-3 max-h-80 overflow-y-auto">
                @foreach($tests as $test)
                <label class="flex items-center gap-2 p-3 border rounded-lg hover:bg-gray-50 cursor-pointer {{ in_array($test->id, $selectedTests) ? 'bg-primary-50 border-primary-200' : '' }}">
                    <input type="checkbox" name="tests[]" value="{{ $test->id }}" 
                        class="rounded text-primary-600"
                        {{ in_array($test->id, $selectedTests) ? 'checked' : '' }}>
                    <div>
                        <span class="font-medium text-sm">{{ $test->name }}</span>
                        <span class="text-xs text-gray-400 block">Rs. {{ number_format($test->price, 0) }}</span>
                    </div>
                </label>
                @endforeach
            </div>
            @error('tests')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
        </div>

        <button type="submit" class="w-full bg-primary-600 text-white py-3 rounded-xl font-semibold hover:bg-primary-700">
            Update Package
        </button>
    </form>
</div>
@endsection
