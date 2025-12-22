@extends('layouts.guest')
@section('title', 'Subscription Expired')
@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-red-50 to-pink-50">
    <div class="max-w-md w-full mx-4">
        <div class="bg-white rounded-3xl p-8 shadow-xl text-center">
            <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-gray-800 mb-2">Subscription Expired</h1>
            <p class="text-gray-600 mb-6">
                Your subscription has expired. Please renew to continue using PathLAS.
            </p>
            
            @if(auth()->user()->lab)
            <div class="bg-gray-50 rounded-xl p-4 text-left mb-6">
                <div class="flex justify-between mb-2">
                    <span class="text-gray-500">Lab:</span>
                    <span class="font-medium">{{ auth()->user()->lab->name }}</span>
                </div>
                <div class="flex justify-between mb-2">
                    <span class="text-gray-500">Plan:</span>
                    <span>{{ ucfirst(str_replace('_', ' ', auth()->user()->lab->subscription_plan)) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Expired:</span>
                    <span class="text-red-600">{{ auth()->user()->lab->subscription_expires_at?->format('M d, Y') }}</span>
                </div>
            </div>
            @endif

            <div class="space-y-3">
                <a href="mailto:support@pathlas.com" class="block w-full py-3 bg-primary-600 text-white rounded-xl hover:bg-primary-700 transition">
                    Contact Support to Renew
                </a>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button class="w-full py-3 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition">
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
