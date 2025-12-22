@extends('layouts.guest')
@section('title', 'Pending Verification')
@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-yellow-50 to-orange-50">
    <div class="max-w-md w-full mx-4">
        <div class="bg-white rounded-3xl p-8 shadow-xl text-center">
            <div class="w-20 h-20 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-gray-800 mb-2">Pending Verification</h1>
            <p class="text-gray-600 mb-6">
                Your lab registration is currently under review. We'll notify you once your account is verified.
            </p>
            <div class="bg-yellow-50 rounded-xl p-4 text-left text-sm text-yellow-800 mb-6">
                <p class="font-medium mb-2">What happens next?</p>
                <ul class="list-disc list-inside space-y-1">
                    <li>Our team will review your registration</li>
                    <li>You'll receive an email once approved</li>
                    <li>This usually takes 1-2 business days</li>
                </ul>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button class="w-full py-3 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition">
                    Logout
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
