@extends('layouts.app')
@section('title', 'Create Test Package')
@section('content')
<div class="max-w-4xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold">Create Test Package</h1>
        <a href="{{ route('packages.index') }}" class="text-gray-600 hover:text-gray-800">← Back</a>
    </div>

    <form action="{{ route('packages.store') }}" method="POST">
        @csrf
        
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
            <h2 class="text-lg font-semibold mb-4">Package Details</h2>
            
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Package Name *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                        class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500"
                        placeholder="Health Checkup Package">
                    @error('name')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Code *</label>
                    <input type="text" name="code" value="{{ old('code') }}" required
                        class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 uppercase"
                        placeholder="HCP001">
                    @error('code')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <textarea name="description" rows="2"
                    class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500"
                    placeholder="Package description...">{{ old('description') }}</textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Package Price *</label>
                    <input type="number" name="price" value="{{ old('price') }}" required step="0.01" min="0"
                        class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500"
                        placeholder="999">
                    @error('price')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">MRP (for discount display)</label>
                    <input type="number" name="mrp" value="{{ old('mrp') }}" step="0.01" min="0"
                        class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500"
                        placeholder="1500">
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
            <h2 class="text-lg font-semibold mb-4">Select Tests *</h2>
            
            <!-- Search box -->
            <div class="mb-4">
                <input type="text" id="test-search" placeholder="Search tests..." 
                    class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500">
            </div>
            
            <div id="test-list" class="grid grid-cols-2 md:grid-cols-3 gap-3 max-h-80 overflow-y-auto">
                @foreach($tests as $test)
                <label class="test-item flex items-center gap-2 p-3 border rounded-lg hover:bg-gray-50 cursor-pointer" data-name="{{ strtolower($test->name) }}">
                    <input type="checkbox" name="tests[]" value="{{ $test->id }}" 
                        class="rounded text-primary-600"
                        {{ in_array($test->id, old('tests', [])) ? 'checked' : '' }}>
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
            Create Package
        </button>
    </form>
</div>

@push('scripts')
<script>
document.getElementById('test-search').addEventListener('input', function() {
    const query = this.value.toLowerCase();
    document.querySelectorAll('.test-item').forEach(item => {
        const name = item.dataset.name;
        item.style.display = name.includes(query) ? '' : 'none';
    });
});
</script>
@endpush
@endsection
