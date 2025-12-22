@extends('layouts.app')
@section('title', 'Add Lab')
@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
        <h2 class="text-xl font-semibold mb-6">Add New Lab / Branch</h2>
        <form action="{{ route('labs.store') }}" method="POST" class="space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Lab Name *</label>
                    <input type="text" name="name" required value="{{ old('name') }}" placeholder="e.g. Smart Pathology Laboratory" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Lab Code *</label>
                    <input type="text" name="code" required value="{{ old('code') }}" placeholder="e.g. SPL-MAIN" class="w-full px-4 py-3 border border-gray-200 rounded-xl">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tagline</label>
                    <input type="text" name="tagline" value="{{ old('tagline', 'Accurate | Caring | Instant') }}" class="w-full px-4 py-3 border border-gray-200 rounded-xl">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
                    <input type="tel" name="phone" value="{{ old('phone') }}" placeholder="Primary phone" class="w-full px-4 py-3 border border-gray-200 rounded-xl">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Phone 2</label>
                    <input type="tel" name="phone2" value="{{ old('phone2') }}" placeholder="Secondary phone" class="w-full px-4 py-3 border border-gray-200 rounded-xl">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="lab@example.com" class="w-full px-4 py-3 border border-gray-200 rounded-xl">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Website</label>
                    <input type="text" name="website" value="{{ old('website') }}" placeholder="www.example.com" class="w-full px-4 py-3 border border-gray-200 rounded-xl">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                <input type="text" name="address" value="{{ old('address') }}" placeholder="Street address" class="w-full px-4 py-3 border border-gray-200 rounded-xl">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">City</label>
                    <input type="text" name="city" value="{{ old('city') }}" class="w-full px-4 py-3 border border-gray-200 rounded-xl">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">State</label>
                    <input type="text" name="state" value="{{ old('state') }}" class="w-full px-4 py-3 border border-gray-200 rounded-xl">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Pincode</label>
                    <input type="text" name="pincode" value="{{ old('pincode') }}" class="w-full px-4 py-3 border border-gray-200 rounded-xl">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Header Color (Report)</label>
                <div class="flex gap-3 items-center">
                    <input type="color" name="header_color" value="{{ old('header_color', '#1e3a8a') }}" class="w-16 h-10 rounded border">
                    <span class="text-sm text-gray-500">Used in PDF report header</span>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Report Notes (shown on all reports)</label>
                <textarea name="report_notes" rows="3" placeholder="Enter notes that will appear on all reports from this lab" class="w-full px-4 py-3 border border-gray-200 rounded-xl">{{ old('report_notes') }}</textarea>
            </div>

            <div class="flex gap-4 pt-4">
                <button type="submit" class="px-6 py-3 bg-primary-600 text-white rounded-xl font-medium hover:bg-primary-700">Create Lab</button>
                <a href="{{ route('labs.index') }}" class="px-6 py-3 bg-gray-100 text-gray-700 rounded-xl">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
