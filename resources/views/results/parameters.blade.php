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

            {{-- Show saved interpretation from test catalog if exists --}}
            @if($bookingTest->test->interpretation)
            <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-6">
                <div class="flex items-start gap-3">
                    <span class="text-amber-600">📋</span>
                    <div>
                        <p class="font-medium text-amber-800 mb-2">Clinical Notes (Saved)</p>
                        <div class="text-sm text-amber-700 prose prose-sm max-w-none">{!! $bookingTest->test->interpretation !!}</div>
                    </div>
                </div>
            </div>
            @endif
            
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
                            @if($param->is_calculated)
                            <p class="text-xs text-green-600">⚡ Auto-calculated</p>
                            @endif
                        </div>
                        <div class="col-span-3">
                            <input type="text" 
                                name="parameters[{{ $param->id }}][value]" 
                                value="{{ old('parameters.' . $param->id . '.value', $existingResult?->value) }}"
                                placeholder="{{ $param->is_calculated ? 'Calculated' : 'Enter value' }}"
                                class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500 param-input {{ $param->is_calculated ? 'bg-green-50 border-green-200' : 'border-gray-200' }}"
                                data-param-id="{{ $param->id }}"
                                data-code="{{ $param->code }}"
                                data-formula="{{ $param->formula }}"
                                data-min="{{ $param->normal_min }}"
                                data-max="{{ $param->normal_max }}"
                                data-gender="{{ $bookingTest->booking->patient->gender }}"
                                {{ $param->is_calculated ? 'readonly' : '' }}>
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
            <!-- Simple test OR test with no parameters added yet -->
            @if($bookingTest->test->parameters()->count() == 0)
            <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 mb-6">
                <p class="text-yellow-800 font-medium">⚠️ No parameters defined for this test</p>
                <p class="text-yellow-700 text-sm mt-1">Add parameters in <a href="{{ route('tests.show', $bookingTest->test) }}" class="underline">Test Catalogue</a> or enter a single result below.</p>
            </div>
            @endif
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
document.addEventListener('DOMContentLoaded', function() {
    const paramInputs = document.querySelectorAll('.param-input');
    
    // Collect all parameter codes and their input elements
    const paramMap = {};
    paramInputs.forEach(input => {
        const code = input.dataset.code;
        if (code) {
            paramMap[code] = input;
        }
    });

    // Function to evaluate formula
    function evaluateFormula(formula, values) {
        if (!formula) return null;
        
        let expression = formula;
        // Replace {CODE} with actual values
        const matches = formula.match(/\{([^}]+)\}/g);
        if (matches) {
            for (const match of matches) {
                const code = match.slice(1, -1); // Remove { and }
                const val = values[code];
                if (val === null || val === undefined || isNaN(val)) {
                    return null; // Can't calculate if any dependency is missing
                }
                expression = expression.replace(match, val);
            }
        }
        
        try {
            // Safe eval for simple math expressions
            const result = Function('"use strict"; return (' + expression + ')')();
            return isFinite(result) ? result : null;
        } catch (e) {
            console.error('Formula eval error:', e);
            return null;
        }
    }

    // Function to update calculated fields
    function updateCalculatedFields() {
        // Collect current values by code
        const values = {};
        paramInputs.forEach(input => {
            const code = input.dataset.code;
            const val = parseFloat(input.value);
            if (code && !isNaN(val)) {
                values[code] = val;
            }
        });

        console.log('Current values:', values);

        // Find and update all fields with formulas
        paramInputs.forEach(input => {
            const formula = input.dataset.formula;
            // Only process if formula exists and is not empty
            if (formula && formula.trim() !== '') {
                console.log('Processing formula:', formula);
                const result = evaluateFormula(formula, values);
                console.log('Result:', result);
                if (result !== null) {
                    input.value = result.toFixed(2);
                    // Update the hidden numeric value
                    const numericInput = input.parentElement.querySelector('.numeric-value');
                    if (numericInput) {
                        numericInput.value = result;
                    }
                    // Update flag
                    updateFlag(input);
                }
            }
        });
    }

    // Listen for changes on all param inputs
    paramInputs.forEach(input => {
        // On input change, update calculated fields
        input.addEventListener('input', function() {
            // Update numeric value hidden field
            const numericInput = this.parentElement.querySelector('.numeric-value');
            const val = parseFloat(this.value);
            if (!isNaN(val) && numericInput) {
                numericInput.value = val;
            }
            
            // Update flag indicator
            updateFlag(this);
            
            // Recalculate dependent formulas only for non-calculated fields
            const formula = this.dataset.formula;
            if (!formula || formula.trim() === '') {
                setTimeout(updateCalculatedFields, 10);
            }
        });
        
        // On blur for calculated fields
        input.addEventListener('blur', function() {
            updateFlag(this);
        });
    });

    function updateFlag(input) {
        const val = parseFloat(input.value);
        const flagIndicator = document.querySelector(`.flag-indicator[data-param-id="${input.dataset.paramId}"]`);
        
        if (!isNaN(val) && flagIndicator) {
            const min = parseFloat(input.dataset.min);
            const max = parseFloat(input.dataset.max);
            
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
    }

    // Initial calculation on page load (for pre-filled values)
    setTimeout(updateCalculatedFields, 100);
});
</script>
@endpush
@endsection
