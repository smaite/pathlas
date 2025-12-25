<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Lab Report - {{ $booking->booking_id }}</title>
    <style>
        @page { margin: 0; size: A4; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 10px; color: #333; line-height: 1.3; }
        
        .page { page-break-after: always; min-height: 297mm; position: relative; }
        .page:last-child { page-break-after: auto; }
        
        /* Header */
        .header { border-bottom: 3px solid {{ $lab->header_color ?? '#0066cc' }}; padding: 15px 25px; background: #fff; }
        .header-table { width: 100%; border-collapse: collapse; }
        .header-logo { width: 120px; vertical-align: middle; }
        .header-logo img { max-width: 100px; max-height: 60px; }
        .header-center { vertical-align: middle; padding-left: 15px; }
        .header-contact { width: 180px; vertical-align: middle; text-align: right; }
        
        .lab-name { font-size: 24px; font-weight: bold; color: {{ $lab->header_color ?? '#0066cc' }}; margin-bottom: 2px; }
        .lab-tagline { font-size: 11px; color: #666; margin-bottom: 4px; }
        .lab-address { font-size: 9px; color: #555; line-height: 1.3; }
        
        .contact-item { font-size: 9px; margin-bottom: 3px; color: #333; }
        .contact-icon { color: {{ $lab->header_color ?? '#0066cc' }}; margin-right: 5px; }
        
        /* Patient Section */
        .patient-section { background: {{ $lab->header_color ?? '#0066cc' }}15; border: 1px solid {{ $lab->header_color ?? '#0066cc' }}40; margin: 10px 25px; padding: 12px; }
        .patient-table { width: 100%; border-collapse: collapse; }
        .patient-label { font-size: 9px; color: #666; width: 80px; padding: 2px 0; }
        .patient-value { font-size: 10px; color: #333; font-weight: 500; padding: 2px 0; padding-right: 20px; }
        .patient-col { vertical-align: top; width: 50%; }
        
        /* Dates Section */
        .dates-section { margin: 0 25px 10px; display: table; width: calc(100% - 50px); }
        .dates-left { display: table-cell; width: 50%; vertical-align: top; }
        .dates-right { display: table-cell; width: 50%; text-align: right; vertical-align: top; }
        .date-item { font-size: 9px; margin: 2px 0; }
        .date-label { color: #666; }
        .date-value { color: #333; font-weight: 500; }
        
        /* Test Title */
        .test-section { margin: 0 25px; }
        .test-category { font-size: 11px; color: {{ $lab->header_color ?? '#0066cc' }}; font-weight: bold; text-transform: uppercase; text-align: center; padding: 8px 0; border-bottom: 2px solid {{ $lab->header_color ?? '#0066cc' }}; margin-bottom: 3px; }
        .test-name { font-size: 14px; font-weight: bold; color: #333; text-align: center; padding: 5px 0 10px; }
        
        /* Results Table */
        .results-table { width: 100%; border-collapse: collapse; margin: 0; }
        .results-table th { background: {{ $lab->header_color ?? '#0066cc' }}; color: white; padding: 8px 12px; text-align: left; font-size: 10px; font-weight: bold; }
        .results-table td { padding: 6px 12px; border-bottom: 1px solid #e0e0e0; font-size: 10px; }
        .results-table tr:nth-child(even) { background: #f9f9f9; }
        
        .group-row { background: #e8f4fc !important; }
        .group-row td { font-weight: bold; color: {{ $lab->header_color ?? '#0066cc' }}; font-size: 10px; padding: 6px 12px; border-bottom: 1px solid #cce0f0; }
        
        .param-name { font-weight: 500; }
        .param-subtext { font-size: 8px; color: #888; font-style: italic; }
        
        .value-normal { color: #16a34a; font-weight: bold; }
        .value-low { color: #2563eb; font-weight: bold; }
        .value-high { color: #dc2626; font-weight: bold; }
        .value-critical { color: #dc2626; font-weight: bold; background: #fee2e2; padding: 2px 6px; }
        
        .ref-range { color: #666; }
        
        /* Interpretation */
        .interpretation { margin: 15px 25px; padding: 10px 15px; background: #fffbeb; border-left: 4px solid #f59e0b; font-size: 10px; }
        .interpretation-title { font-weight: bold; color: #b45309; margin-bottom: 5px; }
        
        /* Instruments Section */
        .instruments { margin: 10px 25px; font-size: 9px; color: #666; padding: 8px 0; border-top: 1px dashed #ddd; }
        
        /* Footer */
        .footer { position: absolute; bottom: 0; left: 0; right: 0; }
        .footer-top { padding: 10px 25px; border-top: 1px solid #ddd; }
        .end-report { text-align: center; font-size: 9px; color: #666; padding: 5px; margin-bottom: 10px; }
        
        .signatures { display: table; width: 100%; }
        .signature-box { display: table-cell; width: 33%; text-align: center; vertical-align: bottom; }
        .signature-line { border-top: 1px solid #333; width: 80%; margin: 0 auto 5px; }
        .signature-name { font-weight: bold; font-size: 10px; color: #333; }
        .signature-title { font-size: 9px; color: #666; }
        
        .footer-bar { background: {{ $lab->header_color ?? '#0066cc' }}; color: white; padding: 8px 25px; font-size: 9px; }
        .footer-bar-table { width: 100%; }
        .footer-left { text-align: left; }
        .footer-center { text-align: center; }
        .footer-right { text-align: right; }
        
        /* QR Code */
        .qr-section { text-align: right; }
        .qr-code { width: 60px; height: 60px; }
    </style>
</head>
<body>
@php 
    $pageNum = 0; 
    $totalPages = $booking->bookingTests->count();
    
    // Get logo as base64 for DomPDF
    $logoBase64 = null;
    if ($lab->logo) {
        $logoFullPath = storage_path('app/public/' . $lab->logo);
        if (file_exists($logoFullPath)) {
            $logoData = file_get_contents($logoFullPath);
            $logoMime = mime_content_type($logoFullPath);
            $logoBase64 = 'data:' . $logoMime . ';base64,' . base64_encode($logoData);
        }
    }
@endphp

@foreach($booking->bookingTests as $bookingTest)
@php $pageNum++; @endphp
@if(($bookingTest->test->hasParameters() && $bookingTest->parameterResults->where('value', '!=', null)->count() > 0) || ($bookingTest->result && $bookingTest->result->value && $bookingTest->result->status === 'approved'))
<div class="page">
    <!-- Header -->
    <div class="header">
        <table class="header-table">
            <tr>
                <td class="header-logo">
                    @if($logoBase64)
                    <img src="{{ $logoBase64 }}" alt="Logo" style="max-width: 100px; max-height: 60px;">
                    @else
                    <div style="width: 80px; height: 50px; background: {{ $lab->header_color ?? '#0066cc' }}; color: white; text-align: center; line-height: 50px; font-size: 18px; font-weight: bold;">{{ strtoupper(substr($lab->name ?? 'L', 0, 2)) }}</div>
                    @endif
                </td>
                <td class="header-center">
                    <div class="lab-name">{{ $lab->name ?? config('app.name', 'PathLAS') }}</div>
                    @if($lab->tagline)
                    <div class="lab-tagline">{{ $lab->tagline }}</div>
                    @endif
                    <div class="lab-address">{{ $lab->full_address ?? $lab->address_street . ', ' . $lab->address_city . ' - ' . $lab->address_pincode }}</div>
                </td>
                <td class="header-contact">
                    @if($lab->email)
                    <div class="contact-item"><strong>Email:</strong> {{ $lab->email }}</div>
                    @endif
                    @if($lab->phone)
                    <div class="contact-item"><strong>Phone:</strong> {{ $lab->phone }}</div>
                    @endif
                    @if($lab->website)
                    <div class="contact-item"><strong>Web:</strong> {{ $lab->website }}</div>
                    @endif
                </td>
            </tr>
        </table>
    </div>

    <!-- Patient Section -->
    <div class="patient-section">
        <table class="patient-table">
            <tr>
                <td class="patient-col">
                    <table style="width: 100%">
                        <tr><td class="patient-label">Name</td><td class="patient-value">:</td><td class="patient-value">{{ $booking->patient->name }}</td></tr>
                        <tr><td class="patient-label">Age/Gender</td><td class="patient-value">:</td><td class="patient-value">{{ $booking->patient->age }}/{{ ucfirst(substr($booking->patient->gender, 0, 1)) }}</td></tr>
                        <tr><td class="patient-label">Referred By</td><td class="patient-value">:</td><td class="patient-value">{{ $booking->referring_doctor_name ?? 'Self' }}</td></tr>
                        @if($booking->patient->phone)
                        <tr><td class="patient-label">Phone No.</td><td class="patient-value">:</td><td class="patient-value">{{ $booking->patient->phone }}</td></tr>
                        @endif
                    </table>
                </td>
                <td class="patient-col">
                    <table style="width: 100%">
                        <tr><td class="patient-label">Patient ID</td><td class="patient-value">:</td><td class="patient-value">{{ $booking->patient->patient_id }}</td></tr>
                        <tr><td class="patient-label">Report ID</td><td class="patient-value">:</td><td class="patient-value">{{ $booking->booking_id }}</td></tr>
                        <tr><td class="patient-label">Collection Date</td><td class="patient-value">:</td><td class="patient-value">{{ $booking->collection_date ? $booking->collection_date->format('d/m/Y h:i A') : now()->format('d/m/Y h:i A') }}</td></tr>
                        <tr><td class="patient-label">Report Date</td><td class="patient-value">:</td><td class="patient-value">{{ now()->format('d/m/Y h:i A') }}</td></tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>

    <!-- Test Section -->
    <div class="test-section">
        <div class="test-category">{{ $bookingTest->test->category->name ?? 'LABORATORY' }}</div>
        <div class="test-name">{{ strtoupper($bookingTest->test->name) }}</div>

        <table class="results-table">
            <thead>
                <tr>
                    <th style="width: 40%">TEST DESCRIPTION</th>
                    <th style="width: 20%">RESULT</th>
                    <th style="width: 25%">REF. RANGE</th>
                    <th style="width: 15%">UNIT</th>
                </tr>
            </thead>
            <tbody>
            @if($bookingTest->test->hasParameters())
                @php
                    $paramResults = $bookingTest->parameterResults->keyBy('test_parameter_id');
                    $params = $bookingTest->test->parameters()->ordered()->get();
                    $currentGroup = null;
                @endphp
                @foreach($params as $param)
                    @if($param->group_name && $param->group_name !== $currentGroup)
                        @php $currentGroup = $param->group_name; @endphp
                        <tr class="group-row">
                            <td colspan="4">{{ $currentGroup }}</td>
                        </tr>
                    @endif
                    @php
                        $paramResult = $paramResults->get($param->id);
                        $value = $paramResult?->value;
                        $flag = $paramResult?->flag ?? $param->checkFlag($value, $booking->patient->gender);
                        $flagClass = match($flag) {
                            'low' => 'value-low',
                            'high' => 'value-high',
                            'critical_low', 'critical_high' => 'value-critical',
                            default => 'value-normal'
                        };
                    @endphp
                    <tr>
                        <td>
                            <span class="param-name">{{ $param->name }}</span>
                            @if($param->method)<br><span class="param-subtext">{{ $param->method }}</span>@endif
                        </td>
                        <td class="{{ $flagClass }}">{{ $value ?? '-' }}</td>
                        <td class="ref-range">{{ $param->getNormalRange($booking->patient->gender) }}</td>
                        <td>{{ $param->unit }}</td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td>{{ $bookingTest->test->name }}</td>
                    <td><strong>{{ $bookingTest->result?->value ?? '-' }}</strong></td>
                    <td class="ref-range">{{ $bookingTest->test->normal_range ?? '-' }}</td>
                    <td>{{ $bookingTest->test->unit }}</td>
                </tr>
            @endif
            </tbody>
        </table>

        @if($bookingTest->result?->notes)
        <div class="interpretation">
            <div class="interpretation-title">Interpretation:</div>
            {{ $bookingTest->result->notes }}
        </div>
        @endif
    </div>

    @if($lab->report_notes)
    <div class="instruments">
        <strong>Notes:</strong> {{ $lab->report_notes }}
    </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <div class="footer-top">
            <div class="end-report">****End of Report****</div>
            <div class="signatures">
                <div class="signature-box">
                    <div class="signature-line"></div>
                    <div class="signature-name">Medical Lab Technician</div>
                    <div class="signature-title">(DMLT, BMLT)</div>
                </div>
                <div class="signature-box">
                    @if(isset($qrCode))
                    <img src="data:image/svg+xml;base64,{{ $qrCode }}" alt="QR Code" style="width: 50px; height: 50px;">
                    @endif
                </div>
                <div class="signature-box">
                    <div class="signature-line"></div>
                    <div class="signature-name">{{ $bookingTest->result?->approvedBy?->name ?? 'Dr. Pathologist' }}</div>
                    <div class="signature-title">(MD, Pathologist)</div>
                </div>
            </div>
        </div>
        <div class="footer-bar">
            <table class="footer-bar-table">
                <tr>
                    <td class="footer-left">{{ $lab->website ?? 'www.pathlas.com' }}</td>
                    <td class="footer-center">Generated on: {{ now()->format('d M, Y h:i A') }}</td>
                    <td class="footer-right">Page {{ $pageNum }} of {{ $totalPages }}</td>
                </tr>
            </table>
        </div>
    </div>
</div>
@endif
@endforeach
</body>
</html>
