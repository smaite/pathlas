@extends('layouts.app')
@section('title', 'Create Booking')
@section('content')
<form action="{{ route('bookings.store') }}" method="POST" id="bookingForm">
    @csrf
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <!-- Dates Section -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="font-semibold mb-4">Collection Details</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Date of Collection</label>
                        <input type="datetime-local" name="collection_date" value="{{ now()->format('Y-m-d\TH:i') }}" class="w-full px-4 py-2 border border-gray-200 rounded-xl">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Received Date</label>
                        <input type="datetime-local" name="received_date" value="{{ now()->format('Y-m-d\TH:i') }}" class="w-full px-4 py-2 border border-gray-200 rounded-xl">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Reporting Date</label>
                        <input type="datetime-local" name="reporting_date" value="{{ now()->addDay()->format('Y-m-d\TH:i') }}" class="w-full px-4 py-2 border border-gray-200 rounded-xl">
                    </div>
                </div>
            </div>

            <!-- Patient Selection -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="font-semibold mb-4">Patient Details</h3>
                @if($patient)
                <input type="hidden" name="patient_id" value="{{ $patient->id }}">
                <div class="p-4 bg-primary-50 border border-primary-200 rounded-xl">
                    <p class="font-semibold text-primary-800">{{ $patient->name }}</p>
                    <p class="text-sm text-primary-600">{{ $patient->patient_id }} • {{ $patient->age }}{{ $patient->gender[0] }} • {{ $patient->phone }}</p>
                </div>
                @else
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Search Patient</label>
                        <div class="relative">
                            <input type="text" id="patientSearch" placeholder="Search patient by name, ID or phone..." class="w-full px-4 py-3 border border-gray-200 rounded-xl">
                            <div id="patientResults" class="absolute z-10 w-full mt-1 bg-white border border-gray-200 rounded-xl shadow-lg hidden max-h-48 overflow-y-auto"></div>
                        </div>
                        <input type="hidden" name="patient_id" id="selectedPatientId" required>
                        <div id="selectedPatient" class="mt-3 p-4 bg-primary-50 border border-primary-200 rounded-xl hidden"></div>
                    </div>
                </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Ref. By Dr.</label>
                        <input type="text" name="referring_doctor" placeholder="Doctor name" class="w-full px-4 py-2 border border-gray-200 rounded-xl">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Received By</label>
                        <input type="text" name="received_by" value="{{ auth()->user()->name }}" placeholder="Staff name" class="w-full px-4 py-2 border border-gray-200 rounded-xl">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Patient Type</label>
                        <select name="patient_type" class="w-full px-4 py-2 border border-gray-200 rounded-xl">
                            <option value="other">Other</option>
                            <option value="opd">OPD</option>
                            <option value="ipd">IPD</option>
                            <option value="emergency">Emergency</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Collection Centre</label>
                        <select name="collection_centre" class="w-full px-4 py-2 border border-gray-200 rounded-xl">
                            <option value="Main Branch">Main Branch</option>
                            <option value="Branch 2">Branch 2</option>
                            <option value="Home Collection">Home Collection</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Sample Collected By</label>
                        <input type="text" name="sample_collected_by" placeholder="Name" class="w-full px-4 py-2 border border-gray-200 rounded-xl">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Sample Collected At</label>
                        <input type="text" name="sample_collected_at_address" placeholder="Address" class="w-full px-4 py-2 border border-gray-200 rounded-xl">
                    </div>
                </div>
            </div>

            <!-- Package Selection -->
            @if(isset($packages) && $packages->count() > 0)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="font-semibold mb-4">📦 Quick Packages</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    @foreach($packages as $package)
                    <label class="package-item flex items-start gap-3 p-4 border-2 border-gray-200 rounded-xl hover:border-primary-300 cursor-pointer transition" 
                           data-tests="{{ $package->tests->pluck('id')->join(',') }}"
                           data-price="{{ $package->price }}">
                        <input type="checkbox" class="package-checkbox mt-1 rounded text-primary-600" data-package-id="{{ $package->id }}">
                        <div class="flex-1">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="font-semibold text-gray-800">{{ $package->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $package->tests->count() }} tests included</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-primary-600 font-bold">₹{{ number_format($package->price, 0) }}</p>
                                    @if($package->mrp && $package->mrp > $package->price)
                                    <p class="text-xs text-gray-400 line-through">₹{{ number_format($package->mrp, 0) }}</p>
                                    @endif
                                </div>
                            </div>
                            <div class="flex flex-wrap gap-1 mt-2">
                                @foreach($package->tests->take(4) as $test)
                                <span class="text-xs bg-gray-100 px-1.5 py-0.5 rounded">{{ $test->name }}</span>
                                @endforeach
                                @if($package->tests->count() > 4)
                                <span class="text-xs text-gray-400">+{{ $package->tests->count() - 4 }} more</span>
                                @endif
                            </div>
                        </div>
                    </label>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Test Selection -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-semibold">Select Tests</h3>
                    <label class="flex items-center space-x-2 text-sm">
                        <input type="checkbox" id="showAllTests" class="rounded">
                        <span>Show all test groups</span>
                    </label>
                </div>
                <div class="mb-4">
                    <input type="text" id="testSearch" placeholder="Search test template..." class="w-full px-4 py-3 border border-gray-200 rounded-xl">
                </div>
                <div class="space-y-4 max-h-96 overflow-y-auto" id="testList">
                    @foreach($tests as $categoryName => $categoryTests)
                    <div class="border border-gray-200 rounded-xl category-group">
                        <div class="px-4 py-3 bg-gray-50 rounded-t-xl font-medium text-gray-700">{{ $categoryName }}</div>
                        <div class="p-4 grid grid-cols-1 md:grid-cols-2 gap-3">
                            @foreach($categoryTests as $test)
                            <label class="flex items-center space-x-3 p-3 border border-gray-100 rounded-lg hover:bg-gray-50 cursor-pointer test-item" data-price="{{ $test->price }}" data-name="{{ $test->name }}" data-search="{{ strtolower($test->name . ' ' . $test->code) }}">
                                <input type="checkbox" name="tests[]" value="{{ $test->id }}" class="w-4 h-4 text-primary-600 rounded test-checkbox">
                                <div class="flex-1">
                                    <p class="font-medium text-gray-800">{{ $test->name }}</p>
                                    <p class="text-sm text-gray-500">{{ $test->code }}</p>
                                </div>
                                <span class="text-primary-600 font-semibold">₹{{ number_format($test->price, 2) }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Remarks</label>
                <textarea name="notes" rows="2" class="w-full px-4 py-3 border border-gray-200 rounded-xl"></textarea>
            </div>
        </div>

        <!-- Order Summary Sidebar -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sticky top-6">
                <h3 class="text-lg font-semibold mb-4">Selected Tests</h3>
                <div id="selectedTests" class="space-y-2 mb-4 max-h-48 overflow-y-auto border-b pb-4"></div>
                
                <div class="space-y-3 mb-4">
                    <div class="flex justify-between"><span class="text-gray-600">Subtotal</span><span id="subtotal" class="font-medium">₹0.00</span></div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600">Discount (%)</span>
                        <input type="number" name="discount_percent" id="discountPercent" value="0" min="0" max="100" class="w-20 px-2 py-1 border border-gray-200 rounded text-right">
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600">Discount</span>
                        <span id="discountAmount" class="text-red-600">-₹0.00</span>
                    </div>
                    <div class="flex justify-between text-lg font-bold pt-2 border-t">
                        <span>Payable</span>
                        <span id="totalAmount">₹0.00</span>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3 mb-4">
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Paid</label>
                        <input type="number" name="paid_amount" id="paidAmount" value="0" min="0" step="0.01" class="w-full px-3 py-2 border border-gray-200 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Due</label>
                        <input type="text" id="dueAmount" value="₹0.00" readonly class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg">
                    </div>
                </div>

                <div class="mb-4">
                    <select name="payment_method" class="w-full px-3 py-2 border border-gray-200 rounded-lg">
                        <option value="cash">Cash</option>
                        <option value="card">Card</option>
                        <option value="upi">UPI</option>
                    </select>
                </div>

                <input type="hidden" name="discount" id="discountValue" value="0">

                <div class="flex items-center mb-4">
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="checkbox" name="is_urgent" value="1" class="w-4 h-4 text-red-600 rounded">
                        <span class="text-sm font-medium text-red-600">Urgent</span>
                    </label>
                </div>

                <button type="submit" class="w-full px-6 py-3 bg-primary-600 text-white rounded-xl font-semibold hover:bg-primary-700 disabled:opacity-50" id="submitBtn" disabled>Save</button>
            </div>
        </div>
    </div>
</form>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const testItems = document.querySelectorAll('.test-checkbox');
    const selectedTestsDiv = document.getElementById('selectedTests');
    const subtotalEl = document.getElementById('subtotal');
    const discountPercentEl = document.getElementById('discountPercent');
    const discountAmountEl = document.getElementById('discountAmount');
    const discountValueEl = document.getElementById('discountValue');
    const totalEl = document.getElementById('totalAmount');
    const paidEl = document.getElementById('paidAmount');
    const dueEl = document.getElementById('dueAmount');
    const submitBtn = document.getElementById('submitBtn');
    const testSearch = document.getElementById('testSearch');

    let subtotal = 0;

    function updateSummary() {
        subtotal = 0;
        let selectedHtml = '';
        let count = 0;

        testItems.forEach(item => {
            if (item.checked) {
                const parent = item.closest('.test-item');
                const price = parseFloat(parent.dataset.price);
                const name = parent.dataset.name;
                subtotal += price;
                count++;
                selectedHtml += `<div class="flex justify-between text-sm p-2 bg-blue-50 rounded"><span class="text-blue-800">${name}</span><span class="font-medium">₹${price.toFixed(2)}</span></div>`;
            }
        });

        const discountPct = parseFloat(discountPercentEl.value) || 0;
        const discountAmt = (subtotal * discountPct / 100);
        const total = Math.max(0, subtotal - discountAmt);
        const paid = parseFloat(paidEl.value) || 0;
        const due = Math.max(0, total - paid);

        selectedTestsDiv.innerHTML = selectedHtml || '<p class="text-gray-400 text-sm text-center py-4">No tests selected</p>';
        subtotalEl.textContent = '₹' + subtotal.toFixed(2);
        discountAmountEl.textContent = '-₹' + discountAmt.toFixed(2);
        discountValueEl.value = discountAmt.toFixed(2);
        totalEl.textContent = '₹' + total.toFixed(2);
        dueEl.value = '₹' + due.toFixed(2);

        const hasPatient = document.getElementById('selectedPatientId')?.value || {{ $patient ? 'true' : 'false' }};
        submitBtn.disabled = count === 0 || !hasPatient;
    }

    testItems.forEach(item => item.addEventListener('change', updateSummary));
    discountPercentEl.addEventListener('input', updateSummary);
    paidEl.addEventListener('input', updateSummary);

    // Test search
    testSearch.addEventListener('input', function() {
        const query = this.value.toLowerCase();
        document.querySelectorAll('.test-item').forEach(item => {
            const show = item.dataset.search.includes(query);
            item.style.display = show ? '' : 'none';
        });
    });

    // Package selection - auto-select tests
    document.querySelectorAll('.package-checkbox').forEach(pkgBox => {
        pkgBox.addEventListener('change', function() {
            const pkgItem = this.closest('.package-item');
            const testIds = pkgItem.dataset.tests.split(',').filter(id => id);
            
            testIds.forEach(testId => {
                const testCheckbox = document.querySelector(`input[name="tests[]"][value="${testId}"]`);
                if (testCheckbox) {
                    testCheckbox.checked = this.checked;
                }
            });
            
            // Visual feedback
            if (this.checked) {
                pkgItem.classList.add('border-primary-500', 'bg-primary-50');
            } else {
                pkgItem.classList.remove('border-primary-500', 'bg-primary-50');
            }
            
            updateSummary();
        });
    });

    // Patient search
    const searchInput = document.getElementById('patientSearch');
    const resultsDiv = document.getElementById('patientResults');
    const selectedDiv = document.getElementById('selectedPatient');
    const patientIdInput = document.getElementById('selectedPatientId');

    if (searchInput) {
        searchInput.addEventListener('input', async function() {
            if (this.value.length < 2) { resultsDiv.classList.add('hidden'); return; }
            const resp = await fetch(`{{ route('patients.search') }}?q=${encodeURIComponent(this.value)}`);
            const patients = await resp.json();
            resultsDiv.innerHTML = patients.map(p => 
                `<div class="px-4 py-3 hover:bg-gray-50 cursor-pointer border-b" onclick="selectPatient(${p.id}, '${p.name}', '${p.patient_id}', ${p.age}, '${p.gender}', '${p.phone}')">${p.name} - ${p.patient_id} - ${p.phone}</div>`
            ).join('') || '<p class="px-4 py-3 text-gray-500">No patients found</p>';
            resultsDiv.classList.remove('hidden');
        });
    }

    window.selectPatient = function(id, name, patientId, age, gender, phone) {
        patientIdInput.value = id;
        selectedDiv.innerHTML = `<p class="font-semibold text-primary-800">${name}</p><p class="text-sm text-primary-600">${patientId} • ${age}${gender[0].toUpperCase()} • ${phone}</p>`;
        selectedDiv.classList.remove('hidden');
        resultsDiv.classList.add('hidden');
        searchInput.value = '';
        updateSummary();
    };
});
</script>
@endpush
@endsection
