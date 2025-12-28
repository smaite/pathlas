@extends('layouts.app')
@section('title', 'Lab Settings')
@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="h-2" style="background: {{ $lab->header_color }}"></div>
        <div class="p-8">
            <div class="flex justify-between items-start mb-6">
                <div>
                    <h2 class="text-xl font-semibold">Lab Settings</h2>
                    <p class="text-gray-500">Customize your lab's profile and report appearance</p>
                </div>
                <div class="text-right text-sm text-gray-500">
                    <p>Code: <span class="font-medium">{{ $lab->code }}</span></p>
                    <p class="text-xs">
                        Subscription: 
                        <span class="px-2 py-0.5 rounded-full {{ $lab->subscription_badge }}">
                            {{ ucfirst($lab->subscription_status) }}
                        </span>
                        @if($lab->days_remaining !== null)
                        ({{ $lab->days_remaining }} days left)
                        @endif
                    </p>
                </div>
            </div>

            <form action="{{ route('lab.settings.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf @method('PUT')

                <!-- Basic Info -->
                <div class="bg-gray-50 rounded-xl p-6">
                    <h3 class="font-medium text-gray-800 mb-4">Lab Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Lab Name *</label>
                            <input type="text" name="name" required value="{{ old('name', $lab->name) }}" class="w-full px-4 py-2 border border-gray-200 rounded-lg">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tagline</label>
                            <input type="text" name="tagline" value="{{ old('tagline', $lab->tagline) }}" placeholder="e.g. Accurate | Caring | Instant" class="w-full px-4 py-2 border border-gray-200 rounded-lg">
                        </div>
                    </div>
                </div>

                <!-- Contact Info -->
                <div class="bg-gray-50 rounded-xl p-6">
                    <h3 class="font-medium text-gray-800 mb-4">Contact Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                            <input type="tel" name="phone" value="{{ old('phone', $lab->phone) }}" class="w-full px-4 py-2 border border-gray-200 rounded-lg">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Phone 2</label>
                            <input type="tel" name="phone2" value="{{ old('phone2', $lab->phone2) }}" class="w-full px-4 py-2 border border-gray-200 rounded-lg">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" name="email" value="{{ old('email', $lab->email) }}" class="w-full px-4 py-2 border border-gray-200 rounded-lg">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">PAN Number</label>
                            <input type="text" name="pan_number" value="{{ old('pan_number', $lab->pan_number) }}" placeholder="e.g. 123456789" class="w-full px-4 py-2 border border-gray-200 rounded-lg">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Website</label>
                            <input type="text" name="website" value="{{ old('website', $lab->website) }}" placeholder="www.yourlab.com" class="w-full px-4 py-2 border border-gray-200 rounded-lg">
                        </div>
                    </div>
                </div>

                <!-- Address -->
                <div class="bg-gray-50 rounded-xl p-6">
                    <h3 class="font-medium text-gray-800 mb-4">Address</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="md:col-span-3">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Street Address</label>
                            <input type="text" name="address" value="{{ old('address', $lab->address) }}" class="w-full px-4 py-2 border border-gray-200 rounded-lg">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">City</label>
                            <input type="text" name="city" value="{{ old('city', $lab->city) }}" class="w-full px-4 py-2 border border-gray-200 rounded-lg">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">State</label>
                            <input type="text" name="state" value="{{ old('state', $lab->state) }}" class="w-full px-4 py-2 border border-gray-200 rounded-lg">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Pincode</label>
                            <input type="text" name="pincode" value="{{ old('pincode', $lab->pincode) }}" class="w-full px-4 py-2 border border-gray-200 rounded-lg">
                        </div>
                    </div>
                </div>

                <!-- Report Settings -->
                <div class="bg-gray-50 rounded-xl p-6">
                    <h3 class="font-medium text-gray-800 mb-4">Report Customization</h3>
                    <div class="space-y-4">
                        <div class="flex items-center gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Header Color</label>
                                <input type="color" name="header_color" value="{{ old('header_color', $lab->header_color) }}" class="w-16 h-10 rounded border cursor-pointer">
                            </div>
                            <div class="flex-1">
                                <p class="text-sm text-gray-500">This color will be used in your PDF report headers</p>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Logo</label>
                            <input type="file" name="logo" accept="image/*" class="w-full px-4 py-2 border border-gray-200 rounded-lg">
                            @if($lab->logo)
                            <p class="text-sm text-gray-500 mt-1">Current: {{ basename($lab->logo) }}</p>
                            @endif
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Report Notes</label>
                            <textarea name="report_notes" rows="3" placeholder="Notes that appear on all reports" class="w-full px-4 py-2 border border-gray-200 rounded-lg">{{ old('report_notes', $lab->report_notes) }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Signature Settings -->
                <div class="bg-gray-50 rounded-xl p-6">
                    <h3 class="font-medium text-gray-800 mb-4">Signature (for Reports)</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Signature Image</label>
                            <input type="file" name="signature_image" accept="image/*" class="w-full px-4 py-2 border border-gray-200 rounded-lg">
                            @if($lab->signature_image)
                            <div class="mt-2 flex items-center gap-2">
                                <img src="{{ asset('storage/'.$lab->signature_image) }}" class="h-12 border rounded" alt="Signature">
                                <span class="text-sm text-gray-500">Current signature</span>
                            </div>
                            @endif
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Signatory Name</label>
                            <input type="text" name="signature_name" value="{{ old('signature_name', $lab->signature_name) }}" placeholder="e.g. Dr. John Doe" class="w-full px-4 py-2 border border-gray-200 rounded-lg">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Designation</label>
                            <input type="text" name="signature_designation" value="{{ old('signature_designation', $lab->signature_designation) }}" placeholder="e.g. Pathologist, MD" class="w-full px-4 py-2 border border-gray-200 rounded-lg">
                        </div>
                    </div>
                </div>

                <div class="flex gap-4">
                    <button type="submit" class="px-6 py-3 bg-primary-600 text-white rounded-xl font-medium hover:bg-primary-700">Save Settings</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
