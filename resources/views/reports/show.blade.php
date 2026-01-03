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

                <!-- View Button -->
                <a href="{{ route('reports.download', ['report' => $report, 'stream' => 'true']) }}" id="viewReportBtn" target="_blank"
                   class="h-10 px-4 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 flex items-center gap-2 font-medium transition-colors" title="View in Browser">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                    View
                </a>

                <!-- Download Dropdown -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="h-10 px-4 bg-primary-600 text-white rounded-xl hover:bg-primary-700 flex items-center gap-2 font-medium transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Download
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
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

                <!-- WhatsApp Share Removed -->

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

        // Update View Button
        const viewBtn = document.getElementById('viewReportBtn');
        if (viewBtn) {
            let viewUrl = new URL(viewBtn.href);
            viewUrl.searchParams.set('template', template);
            viewBtn.href = viewUrl.toString();
        }

        // Update Download Links
        document.querySelectorAll('.download-link').forEach(link => {
            let url = new URL(link.href);
            url.searchParams.set('template', template);
            link.href = url.toString();
        });

        // Update WhatsApp Link
        // const waBtn = document.getElementById('whatsappBtn');
        // if (waBtn) {
        //     const msgPart1 = waBtn.dataset.msgPart1;
        //     const baseLink = waBtn.dataset.baseLink;
        //     const msgPart2 = waBtn.dataset.msgPart2;

        //     // Add template param to link
        //     let linkUrl = new URL(baseLink);
        //     if (template !== 'default') {
        //         linkUrl.searchParams.set('template', template);
        //     }

        //     const finalLink = linkUrl.toString();
        //     const fullMessage = msgPart1 + finalLink + msgPart2;

        //     waBtn.href = `https://wa.me/?text=${encodeURIComponent(fullMessage)}`;
        // }
    }
</script>
@endpush
