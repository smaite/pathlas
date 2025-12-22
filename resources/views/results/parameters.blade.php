@extends('layouts.app')
@section('title', 'Enter Results - ' . $bookingTest->booking->booking_id)
@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
        <div class="flex justify-between items-center mb-6 pb-4 border-b">
            <div>
                <h2 class="text-xl font-bold">{{ $bookingTest->test->name }}</h2>
                <p class="text-gray-500">{{ $bookingTest->test->category->name ?? '' }}</p>
            </div>
            <div class="text-right">
                <p class="font-mono text-sm text-gray-500">{{ $bookingTest->booking->booking_id }}</p>
                <p class="font-medium">{{ $bookingTest->booking->patient->name }}</p>
                <p class="text-sm text-gray-600">{{ $bookingTest->booking->patient->age }} {{ ucfirst($bookingTest->booking->patient->gender) }}</p>
            </div>
        </div>

        <form action="{{ route('results.store-parameters', $bookingTest) }}" method="POST">
            @csrf
            
            @if($bookingTest->test->hasParameters())
            <!-- Test has sub-parameters -->
            <div class="space-y-6">
                @php $currentGroup = null; @endphp
                @foreach($bookingTest->test->parameters()->active()->ordered()->get() as $param)
                    @if($param->group_name && $param->group_name !== $currentGroup)
                    @php $currentGroup = $param->group_name; @endphp
                    <div class="bg-blue-50 px-4 py-2 rounded-lg font-semibold text-blue-800 mt-4">
                        {{ $param->group_name }}
                    </div>
                    @endif
                    
                    @php
                        $existingResult = $bookingTest->parameterResults->where('test_parameter_id', $param->id)->first();
                    @endphp
                    
                    <div class="grid grid-cols-12 gap-4 items-center py-3 border-b border-gray-100">
                        <div class="col-span-4">
                            <p class="font-medium">{{ $param->name }}</p>
                            @if($param->method)
                            <p class="text-xs text-gray-400">{{ $param->method }}</p>
                            @endif
                        </div>
                        <div class="col-span-3">
                            <input type="text" 
                                name="parameters[{{ $param->id }}][value]" 
                                value="{{ old('parameters.' . $param->id . '.value', $existingResult?->value) }}"
                                placeholder="Enter value"
                                class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 param-input"
                                data-param-id="{{ $param->id }}"
                                data-min="{{ $param->normal_min }}"
                                data-max="{{ $param->normal_max }}"
                                data-gender="{{ $bookingTest->booking->patient->gender }}">
                            <input type="hidden" name="parameters[{{ $param->id }}][numeric_value]" class="numeric-value">
                        </div>
                        <div class="col-span-2 text-center">
                            <span class="text-sm text-gray-500">{{ $param->unit }}</span>
                        </div>
                        <div class="col-span-2 text-sm text-gray-600">
                            {{ $param->getNormalRange($bookingTest->booking->patient->gender) }}
                        </div>
                        <div class="col-span-1 text-center flag-indicator" data-param-id="{{ $param->id }}">
                            @if($existingResult?->flag)
                            <span class="{{ $existingResult->flag_badge }}">{{ $existingResult->flag_label }}</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
            @else
            <!-- Simple test without parameters -->
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Result Value *</label>
                    <div class="flex gap-3">
                        <input type="text" name="value" required placeholder="Enter result value" 
                            class="flex-1 px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500"
                            value="{{ old('value', $bookingTest->result?->value) }}">
                        <span class="px-4 py-3 bg-gray-100 rounded-xl text-gray-600">{{ $bookingTest->test->unit }}</span>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Numeric Value (for range check)</label>
                    <input type="number" name="numeric_value" step="0.01" placeholder="Enter numeric value" 
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl"
                        value="{{ old('numeric_value', $bookingTest->result?->numeric_value) }}">
                </div>
            </div>
            @endif

            <div class="mt-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Interpretation / Remarks</label>
                <textarea name="remarks" rows="2" class="w-full px-4 py-3 border border-gray-200 rounded-xl">{{ old('remarks', $bookingTest->result?->remarks ?? '') }}</textarea>
            </div>

            <div class="mt-6 flex gap-4">
                <button type="submit" class="px-6 py-3 bg-primary-600 text-white rounded-xl font-medium hover:bg-primary-700">Save Results</button>
                <a href="{{ route('results.pending') }}" class="px-6 py-3 bg-gray-100 text-gray-700 rounded-xl">Cancel</a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.querySelectorAll('.param-input').forEach(input => {
    input.addEventListener('blur', function() {
        const val = parseFloat(this.value);
        const numericInput = this.parentElement.querySelector('.numeric-value');
        const flagIndicator = document.querySelector(`.flag-indicator[data-param-id="${this.dataset.paramId}"]`);
        
        if (!isNaN(val)) {
            numericInput.value = val;
            
            const min = parseFloat(this.dataset.min);
            const max = parseFloat(this.dataset.max);
            
            let flag = 'Normal';
            let flagClass = 'text-green-600';
            
            if (!isNaN(min) && val < min) {
                flag = 'Low';
                flagClass = 'text-blue-600';
            } else if (!isNaN(max) && val > max) {
                flag = 'High';
                flagClass = 'text-red-600';
            }
            
            flagIndicator.innerHTML = `<span class="${flagClass} font-medium">${flag}</span>`;
        }
    });
});
</script>
@endpush
@endsection
