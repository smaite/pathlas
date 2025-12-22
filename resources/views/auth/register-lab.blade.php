@extends('layouts.guest')
@section('title', 'Register Your Lab')
@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-slate-900 via-purple-900 to-slate-900 py-12 px-4">
    <div class="max-w-xl w-full">
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-white/10 rounded-2xl mb-4">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-white">Register Your Lab</h1>
            <p class="text-gray-400 mt-2">Start your 14-day free trial today</p>
        </div>

        <div class="bg-white/10 backdrop-blur-xl rounded-3xl p-8 border border-white/20">
            @if($errors->any())
            <div class="mb-6 bg-red-500/20 border border-red-500/50 text-red-200 px-4 py-3 rounded-xl">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form action="{{ route('register-lab.store') }}" method="POST" class="space-y-5">
                @csrf
                
                <div class="bg-white/5 rounded-xl p-4 border border-white/10">
                    <h3 class="text-white font-medium mb-4">Lab Information</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm text-gray-300 mb-2">Lab Name *</label>
                            <input type="text" name="lab_name" required value="{{ old('lab_name') }}" 
                                   placeholder="e.g. Smart Pathology Laboratory"
                                   class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-400 focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm text-gray-300 mb-2">City</label>
                                <input type="text" name="city" value="{{ old('city') }}" 
                                       class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-400">
                            </div>
                            <div>
                                <label class="block text-sm text-gray-300 mb-2">State</label>
                                <input type="text" name="state" value="{{ old('state') }}" 
                                       class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-400">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm text-gray-300 mb-2">Address</label>
                            <input type="text" name="address" value="{{ old('address') }}" 
                                   placeholder="Full address"
                                   class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-400">
                        </div>
                    </div>
                </div>

                <div class="bg-white/5 rounded-xl p-4 border border-white/10">
                    <h3 class="text-white font-medium mb-4">Admin Account</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm text-gray-300 mb-2">Your Name *</label>
                            <input type="text" name="owner_name" required value="{{ old('owner_name') }}" 
                                   placeholder="Lab owner/admin name"
                                   class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-400">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-300 mb-2">Email *</label>
                            <input type="email" name="email" required value="{{ old('email') }}" 
                                   placeholder="admin@yourlab.com"
                                   class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-400">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-300 mb-2">Phone *</label>
                            <input type="tel" name="phone" required value="{{ old('phone') }}" 
                                   placeholder="Contact number"
                                   class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-400">
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm text-gray-300 mb-2">Password *</label>
                                <input type="password" name="password" required minlength="8"
                                       class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-400">
                            </div>
                            <div>
                                <label class="block text-sm text-gray-300 mb-2">Confirm Password *</label>
                                <input type="password" name="password_confirmation" required
                                       class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-400">
                            </div>
                        </div>
                    </div>
                </div>

                <button type="submit" class="w-full py-4 bg-gradient-to-r from-purple-600 to-pink-600 text-white font-semibold rounded-xl hover:opacity-90 transition shadow-lg">
                    Start Free Trial →
                </button>
            </form>

            <p class="text-center text-gray-400 mt-6">
                Already have an account? <a href="{{ route('login') }}" class="text-purple-400 hover:text-purple-300">Login here</a>
            </p>
        </div>

        <p class="text-center text-gray-500 text-sm mt-6">
            ✓ 14 days free trial &nbsp;•&nbsp; ✓ No credit card required &nbsp;•&nbsp; ✓ Cancel anytime
        </p>
    </div>
</div>
@endsection
