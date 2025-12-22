<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Lab Report - {{ $booking->booking_id }}</title>
    <style>
        @page { margin: 0; size: A4; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 9px; color: #333; line-height: 1.2; }
        
        .page { page-break-after: always; height: 297mm; position: relative; overflow: hidden; }
        .page:last-child { page-break-after: auto; }
        
        .header { background: linear-gradient(135deg, {{ $lab->header_color ?? '#1e3a8a' }} 0%, #3b82f6 100%); color: white; padding: 10px 20px; }
        .header-content { display: table; width: 100%; }
        .header-left { display: table-cell; width: 70%; vertical-align: middle; }
        .header-right { display: table-cell; width: 30%; text-align: right; vertical-align: middle; }
        .lab-name { font-size: 20px; font-weight: bold; }
        .lab-tagline { font-size: 9px; opacity: 0.9; margin-top: 2px; }
        .lab-address { font-size: 8px; margin-top: 3px; opacity: 0.85; }
        .contact-info { font-size: 9px; }
        .contact-info div { margin-bottom: 1px; }
        
        .sub-header { background: {{ $lab->header_color ?? '#1e3a8a' }}; color: white; font-size: 8px; padding: 3px 20px; text-align: right; }
        
        .patient-section { background: #f0f9ff; padding: 10px 20px; border-bottom: 2px solid {{ $lab->header_color ?? '#1e3a8a' }}; }
        .patient-grid { display: table; width: 100%; }
        .patient-col { display: table-cell; vertical-align: top; }
        .patient-col-1 { width: 22%; }
        .patient-col-2 { width: 38%; }
        .patient-col-3 { width: 40%; text-align: right; }
        .patient-name { font-size: 12px; font-weight: bold; color: {{ $lab->header_color ?? '#1e3a8a' }}; margin-bottom: 3px; }
        .patient-info { font-size: 9px; margin-bottom: 2px; }
        .qr-code { width: 50px; height: 50px; }
        .timestamps { font-size: 8px; color: #555; margin-top: 3px; text-align: right; }
        .timestamps div { margin-bottom: 1px; }
        .barcode-container { display: inline-block; text-align: center; }
        
        .test-title { background: {{ $lab->header_color ?? '#1e3a8a' }}; color: white; padding: 6px 20px; font-size: 11px; font-weight: bold; text-align: center; margin-top: 5px; }
        
        .sample-info { padding: 4px 20px; background: #f9fafb; font-size: 8px; border-bottom: 1px solid #e5e7eb; }
        .sample-info span { margin-right: 20px; }
        
        .results-section { padding: 0 20px; }
        .results-table { width: 100%; border-collapse: collapse; margin-top: 5px; }
        .results-table th { background: #e5e7eb; padding: 4px 8px; text-align: left; font-size: 8px; font-weight: bold; color: #374151; border-bottom: 1px solid #d1d5db; }
        .results-table td { padding: 3px 8px; border-bottom: 1px solid #eee; font-size: 9px; }
        .results-table tr:nth-child(even) { background: #fafafa; }
        
        .group-header { background: #dbeafe !important; }
        .group-header td { color: #1e40af; font-weight: bold; font-size: 9px; padding: 4px 8px; border-bottom: 1px solid #93c5fd; }
        
        .value-cell { font-weight: bold; }
        .flag-normal { color: #16a34a; }
        .flag-low { color: #2563eb; }
        .flag-high { color: #dc2626; }
        .flag-critical { color: #dc2626; font-weight: bold; }
        
        .interpretation { padding: 8px 20px; margin: 8px 20px; background: #fffbeb; border-left: 3px solid #f59e0b; font-size: 9px; }
        .interpretation strong { color: #b45309; }
        
        .notes-section { padding: 6px 20px; font-size: 8px; color: #555; }
        .notes-section strong { color: #333; }
        .notes-section ol { margin-left: 15px; margin-top: 3px; }
        .notes-section li { margin-bottom: 2px; }
        
        .footer { position: absolute; bottom: 0; left: 0; right: 0; padding: 0; }
        .footer-main { padding: 10px 20px; border-top: 2px solid {{ $lab->header_color ?? '#1e3a8a' }}; }
        .end-report { text-align: center; font-size: 8px; color: #666; padding: 5px; border-top: 1px dashed #ccc; border-bottom: 1px dashed #ccc; margin-bottom: 8px; }
        
        .signatures { display: table; width: 100%; }
        .signature-box { display: table-cell; width: 33%; text-align: center; padding-top: 15px; }
        .signature-line { border-top: 1px solid #333; width: 70%; margin: 0 auto 3px; }
        .signature-name { font-weight: bold; font-size: 9px; }
        .signature-title { font-size: 8px; color: #666; }
        
        .footer-bottom { background: {{ $lab->header_color ?? '#1e3a8a' }}; color: white; padding: 5px 20px; display: table; width: 100%; font-size: 8px; }
        .footer-col { display: table-cell; vertical-align: middle; }
        .footer-col-left { width: 40%; }
        .footer-col-center { width: 30%; text-align: center; }
        .footer-col-right { width: 30%; text-align: right; }
    </style>
</head>
<body>
@php $pageNum = 0; $totalPages = $booking->bookingTests->count(); @endphp

@foreach($booking->bookingTests as $bookingTest)
@php $pageNum++; @endphp
@if(($bookingTest->test->hasParameters() && $bookingTest->parameterResults->where('value', '!=', null)->count() > 0) || ($bookingTest->result && $bookingTest->result->value && $bookingTest->result->status === 'approved'))
<div class="page">
    <!-- Header -->
    <div class="header">
        <div class="header-content">
            <div class="header-left">
                <div class="lab-name">{{ $lab->name ?? config('app.name', 'PathLAS') }}</div>
                <div class="lab-tagline">{{ $lab->tagline ?? 'Accurate | Caring | Instant' }}</div>
                <div class="lab-address">{{ $lab->full_address ?? 'Your Lab Address Here' }}</div>
            </div>
            <div class="header-right">
                <div class="contact-info">
                    <div>📞 {{ $lab->phone ?? '0123456789' }}{{ $lab->phone2 ? ' | ' . $lab->phone2 : '' }}</div>
                    <div>✉ {{ $lab->email ?? 'info@pathlas.com' }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="sub-header">{{ $lab->website ?? 'www.pathlas.com' }}</div>

    <!-- Patient Info -->
    <div class="patient-section">
        <div class="patient-grid">
            <div class="patient-col patient-col-1">
                <div class="patient-name">{{ $booking->patient->name }}</div>
                <div class="patient-info">Age: {{ $booking->patient->age }} Years</div>
                <div class="patient-info">Sex: {{ ucfirst($booking->patient->gender) }}</div>
                <div class="patient-info">PID: {{ $booking->patient->patient_id }}</div>
            </div>
            <div class="patient-col patient-col-2">
                @if($booking->sample_collected_at_address)
                <div class="patient-info"><strong>Sample Collected At:</strong> {{ $booking->sample_collected_at_address }}</div>
                @endif
                @if($booking->sample_collected_by)
                <div class="patient-info"><strong>Collected By:</strong> {{ $booking->sample_collected_by }}</div>
                @endif
                @if($booking->referring_doctor_name)
                <div class="patient-info"><strong>Ref. By:</strong> Dr. {{ $booking->referring_doctor_name }}</div>
                @endif
            </div>
            <div class="patient-col patient-col-3">
                <div class="barcode-container">
                    <img src="data:image/svg+xml;base64,{{ $qrCode }}" alt="QR Code" class="qr-code">
                </div>
                <div class="timestamps">
                    @if($booking->collection_date)
                    <div><strong>Registered:</strong> {{ $booking->collection_date->format('h:i A d M, Y') }}</div>
                    @endif
                    @if($booking->received_date)
                    <div><strong>Collected:</strong> {{ $booking->received_date->format('h:i A d M, Y') }}</div>
                    @endif
                    <div><strong>Reported:</strong> {{ now()->format('h:i A d M, Y') }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Test Title -->
    <div class="test-title">{{ strtoupper($bookingTest->test->name) }}</div>

    <div class="sample-info">
        <span><strong>Sample:</strong> {{ ucfirst($bookingTest->test->sample_type ?? 'Blood') }}</span>
        <span><strong>TAT:</strong> {{ $bookingTest->test->turnaround_time ?? 1 }} day</span>
    </div>

    <!-- Results Table -->
    <div class="results-section">
        <table class="results-table">
            <thead>
                <tr>
                    <th style="width: 35%;">Investigation</th>
                    <th style="width: 12%; text-align: center;">Result</th>
                    <th style="width: 10%; text-align: center;">Flag</th>
                    <th style="width: 25%;">Reference Value</th>
                    <th style="width: 18%;">Unit</th>
                </tr>
            </thead>
            <tbody>
                @if($bookingTest->test->hasParameters())
                    @php $currentGroup = null; @endphp
                    @foreach($bookingTest->test->parameters()->active()->ordered()->get() as $param)
                        @php $result = $bookingTest->parameterResults->where('test_parameter_id', $param->id)->first(); @endphp
                        @if($result && $result->value)
                            @if($param->group_name && $param->group_name !== $currentGroup)
                            @php $currentGroup = $param->group_name; @endphp
                            <tr class="group-header">
                                <td colspan="5">{{ $param->group_name }}</td>
                            </tr>
                            @endif
                            <tr>
                                <td>{{ $param->name }}</td>
                                <td class="value-cell {{ $result->flag !== 'normal' ? ($result->flag === 'low' ? 'flag-low' : 'flag-high') : '' }}" style="text-align: center;">
                                    {{ $result->value }}
                                </td>
                                <td style="text-align: center;">
                                    @if($result->flag === 'low')
                                    <span class="flag-low">Low</span>
                                    @elseif($result->flag === 'high')
                                    <span class="flag-high">High</span>
                                    @elseif($result->flag === 'critical_low' || $result->flag === 'critical_high')
                                    <span class="flag-critical">Critical</span>
                                    @else
                                    <span class="flag-normal">Normal</span>
                                    @endif
                                </td>
                                <td>{{ $param->getNormalRange($booking->patient->gender) }}</td>
                                <td>{{ $param->unit }}</td>
                            </tr>
                        @endif
                    @endforeach
                @else
                    <!-- Simple single test -->
                    <tr>
                        <td>{{ $bookingTest->test->name }}</td>
                        <td class="value-cell {{ $bookingTest->result->flag !== 'normal' ? ($bookingTest->result->flag === 'low' ? 'flag-low' : 'flag-high') : '' }}" style="text-align: center;">
                            {{ $bookingTest->result->value }}
                        </td>
                        <td style="text-align: center;">
                            @if($bookingTest->result->flag === 'low')
                            <span class="flag-low">Low</span>
                            @elseif($bookingTest->result->flag === 'high')
                            <span class="flag-high">High</span>
                            @else
                            <span class="flag-normal">Normal</span>
                            @endif
                        </td>
                        <td>{{ $bookingTest->test->normal_range }}</td>
                        <td>{{ $bookingTest->test->unit }}</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    @if($bookingTest->result?->remarks)
    <div class="interpretation">
        <strong>Interpretation:</strong> {{ $bookingTest->result->remarks }}
    </div>
    @endif

    @if($lab->report_notes)
    <div class="notes-section">
        <strong>Note:</strong>
        {!! nl2br(e($lab->report_notes)) !!}
    </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <div class="footer-main">
            <div class="end-report">****End of Report****</div>
            <div class="signatures">
                <div class="signature-box">
                    <div class="signature-line"></div>
                    <div class="signature-name">Lab Technician</div>
                    <div class="signature-title">(DMLT, BMLT)</div>
                </div>
                <div class="signature-box">
                    <div class="signature-line"></div>
                    <div class="signature-name">{{ $bookingTest->result?->approvedBy?->name ?? 'Pathologist' }}</div>
                    <div class="signature-title">(MD, Pathologist)</div>
                </div>
                <div class="signature-box">
                    <div class="signature-line"></div>
                    <div class="signature-name">Chief Pathologist</div>
                    <div class="signature-title">(MD, Pathologist)</div>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <div class="footer-col footer-col-left">
                Scan QR Code to Verify Report
            </div>
            <div class="footer-col footer-col-center">
                {{ now()->format('d M, Y h:i A') }}
            </div>
            <div class="footer-col footer-col-right">
                Page {{ $pageNum }} of {{ $totalPages }}
            </div>
        </div>
    </div>
</div>
@endif
@endforeach

</body>
</html>
