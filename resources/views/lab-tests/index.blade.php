@extends('layouts.app')
@section('title', 'Lab Test Pricing')
@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Lab Test Pricing</h1>
        <p class="text-gray-500">Customize test prices and settings for your lab. Changes only affect your lab.</p>
    </div>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-50 border-b border-gray-100">
            <tr>
                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Test Name</th>
                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Category</th>
                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Master Price</th>
                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Your Price</th>
                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($tests as $test)
            <tr class="hover:bg-gray-50 {{ $test['has_override'] ? 'bg-blue-50/30' : '' }}">
                <td class="px-6 py-4">
                    <span class="font-medium">{{ $test['name'] }}</span>
                    @if($test['has_override'])
                    <span class="ml-2 text-xs px-1.5 py-0.5 bg-blue-100 text-blue-700 rounded">Customized</span>
                    @endif
                </td>
                <td class="px-6 py-4 text-sm text-gray-600">{{ $test['category']['name'] ?? 'N/A' }}</td>
                <td class="px-6 py-4 text-sm text-gray-500">Rs. {{ number_format($test['price'], 2) }}</td>
                <td class="px-6 py-4">
                    <span class="font-semibold {{ $test['lab_price'] != $test['price'] ? 'text-primary-600' : '' }}">
                        Rs. {{ number_format($test['lab_price'], 2) }}
                    </span>
                    @if($test['lab_price'] != $test['price'])
                    <span class="text-xs {{ $test['lab_price'] > $test['price'] ? 'text-green-600' : 'text-red-600' }}">
                        ({{ $test['lab_price'] > $test['price'] ? '+' : '' }}{{ number_format($test['lab_price'] - $test['price']) }})
                    </span>
                    @endif
                </td>
                <td class="px-6 py-4">
                    <span class="px-2 py-1 rounded text-xs {{ $test['_is_active'] ?? true ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                        {{ $test['_is_active'] ?? true ? 'Active' : 'Disabled' }}
                    </span>
                </td>
                <td class="px-6 py-4">
                    <a href="{{ route('lab-tests.edit', $test['id']) }}" class="px-3 py-1.5 bg-gray-100 text-gray-700 text-sm rounded-lg hover:bg-gray-200">
                        Edit
                    </a>
                    @if($test['has_override'])
                    <form action="{{ route('lab-tests.reset', $test['id']) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-3 py-1.5 bg-amber-100 text-amber-700 text-sm rounded-lg hover:bg-amber-200" onclick="return confirm('Reset to default?')">
                            Reset
                        </button>
                    </form>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-6 py-8 text-center text-gray-500">No tests available</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
