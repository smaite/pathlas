@extends('layouts.app')
@section('title', 'Edit Lab')
@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
        <h2 class="text-xl font-semibold mb-6">Edit: {{ $lab->name }}</h2>
        <form action="{{ route('labs.update', $lab) }}" method="POST" class="space-y-6">
            @csrf @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Lab Name *</label>
                    <input type="text" name="name" required value="{{ old('name', $lab->name) }}" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Lab Code *</label>
                    <input type="text" name="code" required value="{{ old('code', $lab->code) }}" class="w-full px-4 py-3 border border-gray-200 rounded-xl">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tagline</label>
                    <input type="text" name="tagline" value="{{ old('tagline', $lab->tagline) }}" class="w-full px-4 py-3 border border-gray-200 rounded-xl">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
                    <input type="tel" name="phone" value="{{ old('phone', $lab->phone) }}" class="w-full px-4 py-3 border border-gray-200 rounded-xl">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Phone 2</label>
                    <input type="tel" name="phone2" value="{{ old('phone2', $lab->phone2) }}" class="w-full px-4 py-3 border border-gray-200 rounded-xl">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input type="email" name="email" value="{{ old('email', $lab->email) }}" class="w-full px-4 py-3 border border-gray-200 rounded-xl">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Website</label>
                    <input type="text" name="website" value="{{ old('website', $lab->website) }}" class="w-full px-4 py-3 border border-gray-200 rounded-xl">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                <input type="text" name="address" value="{{ old('address', $lab->address) }}" class="w-full px-4 py-3 border border-gray-200 rounded-xl">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">City</label>
                    <input type="text" name="city" value="{{ old('city', $lab->city) }}" class="w-full px-4 py-3 border border-gray-200 rounded-xl">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">State</label>
                    <input type="text" name="state" value="{{ old('state', $lab->state) }}" class="w-full px-4 py-3 border border-gray-200 rounded-xl">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Pincode</label>
                    <input type="text" name="pincode" value="{{ old('pincode', $lab->pincode) }}" class="w-full px-4 py-3 border border-gray-200 rounded-xl">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Header Color (Report)</label>
                <div class="flex gap-3 items-center">
                    <input type="color" name="header_color" value="{{ old('header_color', $lab->header_color) }}" class="w-16 h-10 rounded border">
                    <span class="text-sm text-gray-500">Used in PDF report header</span>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Report Notes</label>
                <textarea name="report_notes" rows="3" class="w-full px-4 py-3 border border-gray-200 rounded-xl">{{ old('report_notes', $lab->report_notes) }}</textarea>
            </div>

            <div class="flex gap-4 pt-4">
                <button type="submit" class="px-6 py-3 bg-primary-600 text-white rounded-xl font-medium hover:bg-primary-700">Update Lab</button>
                <a href="{{ route('labs.index') }}" class="px-6 py-3 bg-gray-100 text-gray-700 rounded-xl">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
