@extends('layouts.app')
@section('title', 'Enter Result')
@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="mb-6 pb-6 border-b">
            <h2 class="text-xl font-bold">{{ $result->bookingTest->test->name }}</h2>
            <p class="text-gray-500">{{ $result->bookingTest->test->category->name ?? '' }}</p>
        </div>

        <div class="grid grid-cols-2 gap-4 mb-6">
            <div><p class="text-sm text-gray-500">Patient</p><p class="font-medium">{{ $result->bookingTest->booking->patient->name }}</p></div>
            <div><p class="text-sm text-gray-500">Age/Gender</p><p class="font-medium">{{ $result->bookingTest->booking->patient->age }} / {{ ucfirst($result->bookingTest->booking->patient->gender ?? '') }}</p></div>
            <div><p class="text-sm text-gray-500">Booking ID</p><p class="font-mono">{{ $result->bookingTest->booking->booking_id }}</p></div>
            <div><p class="text-sm text-gray-500">Normal Range</p><p class="font-medium">{{ $result->bookingTest->test->normal_range }} {{ $result->bookingTest->test->unit }}</p></div>
        </div>

        <form action="{{ route('results.store', $result) }}" method="POST" class="space-y-6">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Result Value *</label>
                <div class="flex gap-3">
                    <input type="text" name="value" required placeholder="Enter result value" class="flex-1 px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500">
                    <span class="px-4 py-3 bg-gray-100 rounded-xl text-gray-600">{{ $result->bookingTest->test->unit }}</span>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Numeric Value (for range check)</label>
                <input type="number" name="numeric_value" step="0.01" placeholder="Enter numeric value if applicable" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500" id="numericValue">
                <p class="mt-2 text-sm" id="rangeWarning"></p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Remarks</label>
                <textarea name="remarks" rows="2" class="w-full px-4 py-3 border border-gray-200 rounded-xl"></textarea>
            </div>

            <div class="flex gap-4">
                <button type="submit" class="px-6 py-3 bg-primary-600 text-white rounded-xl font-medium hover:bg-primary-700">Submit Result</button>
                <a href="{{ route('results.pending') }}" class="px-6 py-3 bg-gray-100 text-gray-700 rounded-xl">Cancel</a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('numericValue').addEventListener('input', async function() {
    const val = parseFloat(this.value);
    if (isNaN(val)) return;
    
    const resp = await fetch('{{ route("results.check-flag") }}', {
        method: 'POST',
        headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'},
        body: JSON.stringify({
            test_id: {{ $result->bookingTest->test->id }},
            value: val,
            gender: '{{ $result->bookingTest->booking->patient->gender ?? '' }}'
        })
    });
    const data = await resp.json();
    const warning = document.getElementById('rangeWarning');
    
    if (data.is_abnormal) {
        warning.innerHTML = `<span class="text-red-600 font-medium">⚠️ Value is ${data.flag.replace('_', ' ').toUpperCase()} (Normal: ${data.normal_range})</span>`;
    } else {
        warning.innerHTML = '<span class="text-green-600">✓ Value is within normal range</span>';
    }
});
</script>
@endpush
@endsection
