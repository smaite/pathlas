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
            <div class="flex items-center gap-2">
                <!-- Template Selector -->
                <select id="reportTemplate" onchange="updateReportLinks()" class="h-10 pl-3 pr-8 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    <option value="default">Default Template</option>
                    <option value="modern1">Modern 1 (Blue)</option>
                    <option value="modern2">Modern 2 (Clean)</option>
                </select>

                <!-- Header Option -->
                <select id="headerOption" onchange="updateReportLinks()" class="h-10 pl-3 pr-8 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    <option value="yes">With Header</option>
                    <option value="no">No Header</option>
                </select>

                <!-- View Button -->
                <a href="{{ route('reports.download', ['report' => $report, 'stream' => 'true']) }}" id="viewReportBtn" target="_blank"
                    class="h-10 px-4 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 flex items-center gap-2 font-medium transition-colors" title="View in Browser">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    View
                </a>

                <!-- Download Dropdown -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="h-10 px-4 bg-primary-600 text-white rounded-xl hover:bg-primary-700 flex items-center gap-2 font-medium transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Download
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-lg border border-gray-100 z-10 overflow-hidden">
                        <a href="{{ route('reports.download', ['report' => $report, 'header' => 'yes']) }}" class="download-link block px-4 py-3 hover:bg-gray-50 border-b border-gray-100">
                            <span class="font-medium text-gray-800 text-sm">📄 With Header/Footer</span>
                            <p class="text-xs text-gray-500 mt-0.5">Full letterhead report</p>
                        </a>
                        <a href="{{ route('reports.download', ['report' => $report, 'header' => 'no']) }}" class="download-link block px-4 py-3 hover:bg-gray-50">
                            <span class="font-medium text-gray-800 text-sm">📋 Without Header/Footer</span>
                            <p class="text-xs text-gray-500 mt-0.5">For pre-printed paper</p>
                        </a>
                    </div>
                </div>

                <!-- WhatsApp Share -->
                @php
                $labName = auth()->user()->lab?->name ?? 'PathLAS';
                $patientName = $report->booking->patient->name;
                $reportLink = route('reports.public-download', $report->report_id);

                $waMsg1 = "Dear sir/ma'am, Your lab test results is now ready.\n\nYou can access your report through this link:\n";
                $waMsg2 = "\n\nPatient: {$patientName}\n\nThanks,\n{$labName}";

                $fullWaMsg = $waMsg1 . $reportLink . $waMsg2;
                $waUrl = "https://wa.me/?text=" . urlencode($fullWaMsg);
                @endphp
                <a href="{{ $waUrl }}" id="whatsappBtn" target="_blank"
                    data-msg-part1="{{ urlencode($waMsg1) }}"
                    data-base-link="{{ $reportLink }}"
                    data-msg-part2="{{ urlencode($waMsg2) }}"
                    class="h-10 px-4 bg-green-500 text-white rounded-xl hover:bg-green-600 flex items-center gap-2 font-medium transition-colors">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" />
                    </svg>
                    WhatsApp
                </a>

                <!-- Regenerate -->
                <form action="{{ route('reports.regenerate', $report) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" onclick="return confirm('Regenerate this report with latest data?')"
                        class="h-10 w-10 bg-yellow-100 text-yellow-700 rounded-xl hover:bg-yellow-200 flex items-center justify-center transition-colors" title="Regenerate Report">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                    </button>
                </form>
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

@push('scripts')
<script>
    function updateReportLinks() {
        const template = document.getElementById('reportTemplate').value;
        const headerOption = document.getElementById('headerOption').value;

        // Update View Button
        const viewBtn = document.getElementById('viewReportBtn');
        if (viewBtn) {
            let viewUrl = new URL(viewBtn.href);
            viewUrl.searchParams.set('template', template);
            viewUrl.searchParams.set('header', headerOption);
            viewBtn.href = viewUrl.toString();
        }

        // Update Download Links
        document.querySelectorAll('.download-link').forEach(link => {
            let url = new URL(link.href);
            url.searchParams.set('template', template);
            // Download links in dropdown are explicit, so we might not want to override header param
            // But if user selected "No Header" globally, maybe they expect it?
            // The dropdown has explicit "With Header" and "Without Header" options.
            // Let's NOT override header param for download links as they are explicit.
            link.href = url.toString();
        });

        // Update WhatsApp Link
        const waBtn = document.getElementById('whatsappBtn');
        if (waBtn) {
            const msgPart1 = decodeURIComponent(waBtn.dataset.msgPart1.replace(/\+/g, ' '));
            const baseLink = waBtn.dataset.baseLink;
            const msgPart2 = decodeURIComponent(waBtn.dataset.msgPart2.replace(/\+/g, ' '));

            // Add template param to link
            let linkUrl = new URL(baseLink);
            if (template !== 'default') {
                linkUrl.searchParams.set('template', template);
            }
            linkUrl.searchParams.set('header', headerOption);

            const finalLink = linkUrl.toString();
            const fullMessage = msgPart1 + finalLink + msgPart2;

            waBtn.href = `https://wa.me/?text=${encodeURIComponent(fullMessage)}`;
        }
    }
</script>
@endpush