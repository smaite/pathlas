@extends('layouts.app')
@section('title', 'Edit Patient')
@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
        <h2 class="text-xl font-semibold mb-6">Edit Patient: {{ $patient->name }}</h2>
        <form action="{{ route('patients.update', $patient) }}" method="POST" class="space-y-6">
            @csrf @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Full Name </label>
                    <input type="text" name="name" value="{{ old('name', $patient->name) }}" class="w-full px-4 py-3 border border-gray-200 rounded-xl">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Age </label>
                    <input type="number" name="age" value="{{ old('age', $patient->age) }}" class="w-full px-4 py-3 border border-gray-200 rounded-xl">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Gender </label>
                    <select name="gender" class="w-full px-4 py-3 border border-gray-200 rounded-xl">
                        <option value="male" {{ $patient->gender == 'male' ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ $patient->gender == 'female' ? 'selected' : '' }}>Female</option>
                        <option value="other" {{ $patient->gender == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Phone </label>
                    <input type="tel" name="phone" value="{{ old('phone', $patient->phone) }}" class="w-full px-4 py-3 border border-gray-200 rounded-xl">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input type="email" name="email" value="{{ old('email', $patient->email) }}" class="w-full px-4 py-3 border border-gray-200 rounded-xl">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                    <textarea name="address" rows="2" class="w-full px-4 py-3 border border-gray-200 rounded-xl">{{ old('address', $patient->address) }}</textarea>
                </div>
            </div>
            <div class="flex gap-4">
                <button type="submit" class="px-6 py-3 bg-primary-600 text-white rounded-xl font-medium hover:bg-primary-700">Update Patient</button>
                <a href="{{ route('patients.show', $patient) }}" class="px-6 py-3 bg-gray-100 text-gray-700 rounded-xl">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection