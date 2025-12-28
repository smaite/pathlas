@extends('layouts.app')
@section('title', 'Test Packages')
@section('content')
<div class="max-w-6xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold">Test Packages</h1>
            <p class="text-gray-500">Bundled tests at discounted prices</p>
        </div>
        <a href="{{ route('packages.create') }}" class="bg-primary-600 text-white px-5 py-2.5 rounded-xl font-medium hover:bg-primary-700 flex items-center gap-2">
            <span>+</span> New Package
        </a>
    </div>

    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-6">
        {{ session('success') }}
    </div>
    @endif

    @if($packages->count() > 0)
    <div class="grid gap-4">
        @foreach($packages as $package)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition {{ !$package->is_active ? 'opacity-60' : '' }}">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <div class="flex items-center gap-3">
                        <h3 class="text-lg font-semibold">{{ $package->name }}</h3>
                        <span class="text-xs font-mono bg-gray-100 px-2 py-1 rounded">{{ $package->code }}</span>
                        @if(!$package->is_active)
                        <span class="text-xs bg-red-100 text-red-600 px-2 py-1 rounded">Inactive</span>
                        @endif
                        @if($package->discount_percent > 0)
                        <span class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded font-medium">{{ $package->discount_percent }}% OFF</span>
                        @endif
                    </div>
                    <p class="text-sm text-gray-500 mt-1">{{ $package->tests->count() }} tests included</p>
                    <div class="flex flex-wrap gap-2 mt-2">
                        @foreach($package->tests->take(5) as $test)
                        <span class="text-xs bg-blue-50 text-blue-700 px-2 py-1 rounded">{{ $test->name }}</span>
                        @endforeach
                        @if($package->tests->count() > 5)
                        <span class="text-xs text-gray-500">+{{ $package->tests->count() - 5 }} more</span>
                        @endif
                    </div>
                </div>
                <div class="text-right ml-4">
                    <div class="text-2xl font-bold text-primary-600">Rs. {{ number_format($package->price, 0) }}</div>
                    @if($package->mrp && $package->mrp > $package->price)
                    <div class="text-sm text-gray-400 line-through">Rs. {{ number_format($package->mrp, 0) }}</div>
                    @endif
                    @if($package->savings > 0)
                    <div class="text-xs text-green-600 font-medium">Save Rs. {{ number_format($package->savings, 0) }}</div>
                    @endif
                </div>
                <div class="flex items-center gap-2 ml-6">
                    <a href="{{ route('packages.edit', $package) }}" class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                    </a>
                    <form action="{{ route('packages.toggle-status', $package) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="p-2 text-gray-400 hover:text-yellow-600 hover:bg-yellow-50 rounded-lg" title="{{ $package->is_active ? 'Deactivate' : 'Activate' }}">
                            @if($package->is_active)
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            @else
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                            </svg>
                            @endif
                        </button>
                    </form>
                    <form action="{{ route('packages.destroy', $package) }}" method="POST" class="inline" onsubmit="return confirm('Delete this package?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
        <div class="text-6xl mb-4">📦</div>
        <h3 class="text-xl font-semibold mb-2">No Packages Yet</h3>
        <p class="text-gray-500 mb-6">Create your first test package to offer bundled tests at discounted prices.</p>
        <a href="{{ route('packages.create') }}" class="inline-flex items-center gap-2 bg-primary-600 text-white px-6 py-3 rounded-xl font-medium hover:bg-primary-700">
            <span>+</span> Create Package
        </a>
    </div>
    @endif
</div>
@endsection
