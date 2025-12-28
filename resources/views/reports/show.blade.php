@extends('layouts.app')
@section('title', 'Report: ' . $report->report_id)
@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
        <div class="flex justify-between items-start mb-6">
            <div>
                <h2 class="text-xl font-bold">{{ $report->report_id }}</h2>
                <p class="text-gray-500">Generated: {{ $report->generated_at?->format('M d, Y h:i A') }}</p>
            </div>
            <div class="flex gap-2">
                <form action="{{ route('reports.regenerate', $report) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" onclick="return confirm('Regenerate this report with latest data?')" 
                            class="px-4 py-2 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Regenerate
                    </button>
                </form>
                
                <!-- WhatsApp Share Button -->
                @if($report->booking->patient->phone)
                @php
                    $phone = preg_replace('/[^0-9]/', '', $report->booking->patient->phone);
                    if(strlen($phone) == 10) $phone = '977' . $phone; // Nepal code
                    $labName = auth()->user()->lab?->name ?? 'PathLAS';
                    $patientName = $report->booking->patient->name;
                    $patientAge = $report->booking->patient->age;
                    $patientGender = ucfirst(substr($report->booking->patient->gender ?? '', 0, 1));
                    $regNo = $report->booking->booking_id;
                    $reportLink = route('reports.download', ['report' => $report, 'header' => 'yes']);
                    
                    $message = "🏥 *{$labName}*\n\n";
                    $message .= "Dear Sir/Ma'am,\n\n";
                    $message .= "✅ Your lab test report is now ready!\n\n";
                    $message .= "📋 *Patient Details*\n";
                    $message .= "• Name: {$patientName}\n";
                    $message .= "• Age/Gender: {$patientAge} / {$patientGender}\n";
                    $message .= "• Reg No: {$regNo}\n\n";
                    $message .= "📄 *Download Report*\n{$reportLink}\n\n";
                    $message .= "_Save this number to view the link if not visible_\n\n";
                    $message .= "For any queries, feel free to contact us.\n\n";
                    $message .= "Thank you for choosing {$labName}! 🙏";
                    
                    $waUrl = "https://wa.me/{$phone}?text=" . urlencode($message);
                @endphp
                <a href="{{ $waUrl }}" target="_blank" 
                   class="px-4 py-2 bg-green-500 text-white rounded-xl hover:bg-green-600 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                    </svg>
                    Share on WhatsApp
                </a>
                @endif
                
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="px-4 py-2 bg-primary-600 text-white rounded-xl hover:bg-primary-700 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Download PDF
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                    </button>
                    <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-lg border z-10">
                        <a href="{{ route('reports.download', ['report' => $report, 'header' => 'yes']) }}" class="block px-4 py-3 hover:bg-gray-50 rounded-t-xl">
                            <span class="font-medium text-gray-800">📄 With Header/Footer</span>
                            <p class="text-xs text-gray-500 mt-0.5">Full letterhead report</p>
                        </a>
                        <a href="{{ route('reports.download', ['report' => $report, 'header' => 'no']) }}" class="block px-4 py-3 hover:bg-gray-50 rounded-b-xl border-t">
                            <span class="font-medium text-gray-800">📋 Without Header/Footer</span>
                            <p class="text-xs text-gray-500 mt-0.5">For pre-printed paper</p>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4 mb-6 p-4 bg-gray-50 rounded-xl">
            <div><span class="text-gray-500">Patient:</span> <strong>{{ $report->booking->patient->name }}</strong></div>
            <div><span class="text-gray-500">Patient ID:</span> {{ $report->booking->patient->patient_id }}</div>
            <div><span class="text-gray-500">Booking:</span> {{ $report->booking->booking_id }}</div>
            <div><span class="text-gray-500">Generated By:</span> {{ $report->generatedBy?->name ?? 'System' }}</div>
        </div>

        <h3 class="font-semibold mb-4">Test Results</h3>
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Test</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Result</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Normal Range</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Flag</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @foreach($report->booking->bookingTests as $bt)
                <tr>
                    <td class="px-4 py-3 font-medium">{{ $bt->test->name }}</td>
                    <td class="px-4 py-3 font-mono">{{ $bt->result?->value ?? '-' }} {{ $bt->test->unit }}</td>
                    <td class="px-4 py-3 text-sm text-gray-600">{{ $bt->test->normal_range }}</td>
                    <td class="px-4 py-3">
                        @if($bt->result?->flag && $bt->result->flag !== 'normal')
                        <span class="px-2 py-1 rounded text-xs font-bold {{ $bt->result->flag_badge }}">{{ strtoupper(str_replace('_', ' ', $bt->result->flag)) }}</span>
                        @else
                        <span class="text-green-600">Normal</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <a href="{{ route('reports.index') }}" class="text-primary-600 hover:underline">← Back to Reports</a>
</div>
@endsection
