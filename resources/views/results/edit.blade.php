@extends('layouts.app')
@section('title', 'Edit Results - ' . $bookingTest->booking->booking_id)
@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
        <div class="flex justify-between items-center mb-6 pb-4 border-b">
            <div>
                <h2 class="text-xl font-bold">Edit: {{ $bookingTest->test->name }}</h2>
                <p class="text-gray-500">{{ $bookingTest->test->category->name ?? '' }}</p>
            </div>
            <div class="text-right">
                <p class="font-mono text-sm text-gray-500">{{ $bookingTest->booking->booking_id }}</p>
                <p class="font-medium">{{ $bookingTest->booking->patient->name }}</p>
            </div>
        </div>

        @if($bookingTest->result && $bookingTest->result->edited_at)
        <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-6">
            <p class="text-amber-800 text-sm">
                <strong>Last Edited:</strong> {{ $bookingTest->result->edited_at->format('d M Y h:i A') }} 
                by {{ $bookingTest->result->editedBy->name ?? 'Unknown' }}
            </p>
            @if($bookingTest->result->edit_reason)
            <p class="text-amber-700 text-sm mt-1"><strong>Reason:</strong> {{ $bookingTest->result->edit_reason }}</p>
            @endif
            @if($bookingTest->result->previous_value)
            <p class="text-amber-600 text-sm mt-1"><strong>Previous Value:</strong> {{ $bookingTest->result->previous_value }}</p>
            @endif
        </div>
        @endif

        <form action="{{ route('results.update-edit', $bookingTest) }}" method="POST">
            @csrf

            @if($bookingTest->test->hasParameters())
            <!-- Parameter-based test -->
            <div class="space-y-4 mb-6">
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
                        $isCalculated = $param->is_calculated && $param->formula;
                    @endphp
                    
                    <div class="grid grid-cols-12 gap-4 items-center py-3 border-b border-gray-100">
                        <div class="col-span-4">
                            <p class="font-medium">
                                {{ $param->name }}
                                @if($isCalculated)
                                <span class="text-xs text-purple-600 bg-purple-50 px-1.5 py-0.5 rounded ml-1">Calc</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-span-3">
                            <input type="text" 
                                name="parameters[{{ $param->id }}][value]" 
                                value="{{ old('parameters.' . $param->id . '.value', $existingResult?->value) }}"
                                class="w-full px-3 py-2 border border-gray-200 rounded-lg param-input {{ $isCalculated ? 'bg-purple-50' : '' }}"
                                data-param-id="{{ $param->id }}"
                                data-code="{{ $param->code }}"
                                data-formula="{{ $param->formula ?? '' }}"
                                data-min="{{ $param->normal_min }}"
                                data-max="{{ $param->normal_max }}"
                                {{ $isCalculated ? 'readonly' : '' }}>
                        </div>
                        <div class="col-span-2 text-center text-sm text-gray-500">{{ $param->unit }}</div>
                        <div class="col-span-3 text-sm text-gray-600">
                            {{ $param->getNormalRange($bookingTest->booking->patient->gender) }}
                        </div>
                    </div>
                @endforeach
            </div>
            @else
            <!-- Simple test -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Result Value</label>
                <div class="flex gap-3">
                    <input type="text" name="value" value="{{ old('value', $bookingTest->result?->value) }}"
                        class="flex-1 px-4 py-3 border border-gray-200 rounded-xl">
                    <span class="px-4 py-3 bg-gray-100 rounded-xl text-gray-600">{{ $bookingTest->test->unit }}</span>
                </div>
            </div>
            @endif

            <!-- Edit Reason (required) -->
            <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6">
                <label class="block text-sm font-medium text-red-800 mb-2">
                    ⚠️ Reason for Edit <span class="text-red-600">*</span>
                </label>
                <input type="text" name="edit_reason" required placeholder="e.g. Typo correction, Recalculated value, etc."
                    class="w-full px-4 py-3 border border-red-200 rounded-xl" value="{{ old('edit_reason') }}">
                <p class="text-xs text-red-600 mt-2">This will be logged for audit purposes.</p>
            </div>

            <div class="flex gap-4">
                <button type="submit" class="px-6 py-3 bg-amber-600 text-white rounded-xl font-medium hover:bg-amber-700">
                    Update Result
                </button>
                <a href="{{ route('bookings.show', $bookingTest->booking) }}" class="px-6 py-3 bg-gray-100 text-gray-700 rounded-xl">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const paramInputs = document.querySelectorAll('.param-input');
    
    // Formula evaluation function
    function evaluateFormula(formula, values) {
        if (!formula) return null;
        
        let expression = formula;
        const pattern = /\{([^}]+)\}/g;
        let match;
        
        while ((match = pattern.exec(formula)) !== null) {
            const code = match[1];
            if (values[code] !== undefined && values[code] !== null && !isNaN(values[code])) {
                expression = expression.replace(match[0], values[code]);
            } else {
                return null; // Missing dependency
            }
        }
        
        try {
            const result = Function('"use strict"; return (' + expression + ')')();
            return isFinite(result) ? result : null;
        } catch (e) {
            console.error('Formula error:', e);
            return null;
        }
    }

    // Update calculated fields
    function updateCalculatedFields() {
        const values = {};
        paramInputs.forEach(input => {
            const code = input.dataset.code;
            const val = parseFloat(input.value);
            if (code && !isNaN(val)) {
                values[code] = val;
            }
        });

        paramInputs.forEach(input => {
            const formula = input.dataset.formula;
            if (formula && formula.trim() !== '') {
                const result = evaluateFormula(formula, values);
                if (result !== null) {
                    input.value = result.toFixed(2);
                }
            }
        });
    }

    // Listen for changes
    paramInputs.forEach(input => {
        input.addEventListener('input', function() {
            const formula = this.dataset.formula;
            if (!formula || formula.trim() === '') {
                setTimeout(updateCalculatedFields, 10);
            }
        });
    });

    // Initial calculation
    setTimeout(updateCalculatedFields, 100);
});
</script>
@endpush
@endsection

