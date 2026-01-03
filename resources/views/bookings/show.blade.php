@extends('layouts.app')
@section('title', 'Booking: ' . $booking->booking_id)
@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">
        <!-- Booking Info -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex justify-between items-start mb-6">
                <div>
                    <h2 class="text-xl font-bold">{{ $booking->booking_id }}</h2>
                    <p class="text-gray-500">{{ $booking->created_at->format('M d, Y h:i A') }}</p>
                </div>
                <span class="px-3 py-1.5 rounded-full text-sm font-medium {{ $booking->status_badge }}">{{ ucfirst(str_replace('_', ' ', $booking->status)) }}</span>
            </div>
            
            <div class="grid grid-cols-2 gap-4 mb-6">
                <div>
                    <p class="text-sm text-gray-500">Patient</p>
                    <p class="font-medium">{{ $booking->patient->name }}</p>
                    <p class="text-sm text-gray-600">{{ $booking->patient->phone }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Created By</p>
                    <p class="font-medium">{{ $booking->createdBy?->name ?? 'N/A' }}</p>
                </div>
            </div>

            @if($booking->status === 'pending')
            <form action="{{ route('bookings.status', $booking) }}" method="POST" class="inline">
                @csrf
                <input type="hidden" name="status" value="sample_collected">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700">Mark Sample Collected</button>
            </form>
            @endif
        </div>

        <!-- Tests -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100"><h3 class="font-semibold">Tests & Results</h3></div>
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Test</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Result</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Normal Range</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($booking->bookingTests as $bt)
                    <tr>
                        <td class="px-6 py-4">
                            <p class="font-medium">{{ $bt->test->name }}</p>
                            <p class="text-sm text-gray-500">{{ $bt->test->category->name ?? '' }}</p>
                        </td>
                        <td class="px-6 py-4">
                            @if($bt->result && $bt->result->value)
                            <span class="font-mono">{{ $bt->result->value }} {{ $bt->test->unit }}</span>
                            @if($bt->result->flag && $bt->result->flag !== 'normal')
                            <span class="ml-2 px-2 py-0.5 rounded text-xs font-bold {{ $bt->result->flag_badge }}">{{ $bt->result->flag_label }}</span>
                            @endif
                            @if($bt->result->edited_at)
                            <span class="ml-1 text-xs text-amber-600" title="Edited: {{ $bt->result->edited_at->format('d M Y') }}">✎</span>
                            @endif
                            @else
                            <span class="text-gray-400">Pending</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $bt->test->normal_range }}</td>
                        <td class="px-6 py-4"><span class="px-2 py-1 rounded text-xs {{ $bt->status === 'completed' ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' }}">{{ ucfirst($bt->status) }}</span></td>
                        <td class="px-6 py-4">
                            @if($bt->result && $bt->result->status === 'approved')
                            <a href="{{ route('results.edit', $bt) }}" class="text-xs px-2 py-1 bg-amber-100 text-amber-700 rounded hover:bg-amber-200">✎ Edit</a>
                            @elseif(!$bt->result || $bt->result->status !== 'approved')
                            <a href="{{ route('results.parameters', $bt) }}" class="text-xs px-2 py-1 bg-blue-100 text-blue-700 rounded hover:bg-blue-200">Enter</a>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @php
            $allResultsApproved = $booking->bookingTests->count() > 0 && 
                $booking->bookingTests->every(fn($bt) => $bt->result && $bt->result->status === 'approved');
        @endphp

        @if($booking->report)
        <div class="bg-green-50 border border-green-200 rounded-2xl p-6">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="font-semibold text-green-800">Report Generated</h3>
                    <p class="text-sm text-green-600">{{ $booking->report->report_id }}</p>
                </div>
                <div class="flex gap-2">
                    @php
                        $labName = auth()->user()->lab?->name ?? 'PathLAS';
                        $patientName = $booking->patient->name;
                        $patientAge = $booking->patient->age;
                        $patientGender = ucfirst(substr($booking->patient->gender ?? '', 0, 1));
                        $regNo = $booking->booking_id;
                        $reportLink = route('reports.public-download', $booking->report->report_id);

                        $message = "Dear sir/ma'am, Your lab test results is now ready.\n\n";
                        $message .= "You can access your report through this link:\n";
                        $message .= "{$reportLink}\n\n";
                        $message .= "Patient details\n";
                        $message .= "* Name: {$patientName}\n";
                        $message .= "* Age/Sex: {$patientAge} / {$patientGender}\n";
                        $message .= "* Reg No: {$regNo}\n\n";
                        $message .= "If you have any questions regarding your results, kindly call us.\n\n";
                        $message .= "Thanks,\n";
                        $message .= "{$labName}";

                        $waUrl = "https://wa.me/?text=" . urlencode($message);
                    @endphp
                    <a href="{{ $waUrl }}" target="_blank" class="px-4 py-2 bg-green-500 text-white rounded-xl hover:bg-green-600 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                        WhatsApp
                    </a>
                    <a href="{{ route('reports.show', $booking->report) }}" class="px-4 py-2 bg-green-600 text-white rounded-xl hover:bg-green-700">View Report</a>
                </div>
            </div>
        </div>
        @elseif($booking->status === 'completed' || $allResultsApproved)
        <a href="{{ route('reports.generate', $booking) }}" class="block bg-primary-50 border border-primary-200 rounded-2xl p-6 text-center hover:bg-primary-100">
            <p class="font-semibold text-primary-700">All Results Complete - Generate Report</p>
        </a>
        @endif
    </div>

    <!-- Sidebar -->
    <div class="space-y-6">
        <!-- Payment Summary -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-semibold mb-4">Payment Summary</h3>
            <div class="space-y-2 mb-4">
                <div class="flex justify-between"><span class="text-gray-600">Subtotal</span><span>₹{{ number_format($booking->subtotal, 2) }}</span></div>
                <div class="flex justify-between"><span class="text-gray-600">Discount</span><span class="text-red-600">-₹{{ number_format($booking->discount, 2) }}</span></div>
                <div class="flex justify-between font-bold text-lg pt-2 border-t"><span>Total</span><span>₹{{ number_format($booking->total_amount, 2) }}</span></div>
                <div class="flex justify-between"><span class="text-gray-600">Paid</span><span class="text-green-600">₹{{ number_format($booking->paid_amount, 2) }}</span></div>
                <div class="flex justify-between font-medium"><span>Due</span><span class="text-red-600">₹{{ number_format($booking->due_amount, 2) }}</span></div>
            </div>

            @if($booking->due_amount > 0)
            <form action="{{ route('bookings.payment', $booking) }}" method="POST" class="space-y-3">
                @csrf
                <input type="number" name="amount" step="0.01" max="{{ $booking->due_amount }}" required placeholder="Amount" class="w-full px-4 py-2 border border-gray-200 rounded-xl">
                <select name="method" required class="w-full px-4 py-2 border border-gray-200 rounded-xl">
                    <option value="cash">Cash</option>
                    <option value="card">Card</option>
                    <option value="upi">UPI</option>
                </select>
                <button type="submit" class="w-full px-4 py-2 bg-green-600 text-white rounded-xl hover:bg-green-700">Record Payment</button>
            </form>
            @endif
        </div>

        <!-- Actions -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-semibold mb-4">Actions</h3>
            <div class="space-y-3">
                <!-- Receipt Template Selection -->
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Receipt Template</label>
                    <select id="receiptTemplate" onchange="updateReceiptLink()" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm bg-gray-50">
                        <option value="default">Default</option>
                        <option value="modern1">Modern 1 (Invoice)</option>
                        <option value="modern2">Modern 2 (Corporate)</option>
                    </select>
                </div>

                <a href="{{ route('bookings.receipt', $booking) }}" id="printReceiptBtn" target="_blank" class="block w-full px-4 py-2 bg-primary-600 text-white rounded-xl text-center hover:bg-primary-700 flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                    Print Receipt
                </a>

                <!-- WhatsApp Receipt Share -->
                @php
                    $labName = auth()->user()->lab?->name ?? 'PathLAS';

                    // Build test list
                    $testList = $booking->bookingTests->map(fn($bt) => $bt->test->name)->implode(', ');

                    $receiptMsg = "*{$labName}*\n\n";
                    $receiptMsg .= "Dear Sir/Ma'am,\n\n";
                    $receiptMsg .= "Thank you for visiting us!\n\n";
                    $receiptMsg .= "*Booking Confirmation*\n";
                    $receiptMsg .= "- Booking ID: {$booking->booking_id}\n";
                    $receiptMsg .= "- Patient: {$booking->patient->name}\n";
                    $receiptMsg .= "- Date: " . $booking->created_at->format('d M Y') . "\n\n";
                    $receiptMsg .= "*Tests Booked:*\n{$testList}\n\n";
                    $receiptMsg .= "*Payment:*\n";
                    $receiptMsg .= "- Total: Rs. " . number_format($booking->total_amount, 2) . "\n";
                    $receiptMsg .= "- Paid: Rs. " . number_format($booking->paid_amount, 2) . "\n";
                    if($booking->due_amount > 0) {
                        $receiptMsg .= "- Due: Rs. " . number_format($booking->due_amount, 2) . "\n";
                    }
                    $receiptMsg .= "\nReport will be ready within 24-48 hours.\n\n";
                    $receiptMsg .= "Thank you for choosing {$labName}!";

                    $waReceiptUrl = "https://wa.me/?text=" . urlencode($receiptMsg);
                @endphp
                <a href="{{ $waReceiptUrl }}" target="_blank"
                   class="flex items-center justify-center gap-2 w-full px-4 py-2 bg-green-500 text-white rounded-xl hover:bg-green-600">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                    </svg>
                    Send Receipt via WhatsApp
                </a>
                
                <a href="{{ route('bookings.invoice', $booking) }}" class="block w-full px-4 py-2 bg-gray-100 text-gray-700 rounded-xl text-center hover:bg-gray-200">View Invoice</a>
                <a href="{{ route('bookings.invoice.pdf', $booking) }}" class="block w-full px-4 py-2 bg-gray-100 text-gray-700 rounded-xl text-center hover:bg-gray-200">Download Invoice PDF</a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function updateReceiptLink() {
        const template = document.getElementById('receiptTemplate').value;
        const btn = document.getElementById('printReceiptBtn');

        // Create URL object from current href
        let url = new URL(btn.href);

        // Update template parameter
        url.searchParams.set('template', template);

        // Update button href
        btn.href = url.toString();
    }
</script>
@endpush
