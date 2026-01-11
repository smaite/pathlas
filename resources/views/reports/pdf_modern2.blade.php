<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Medical Report - {{ $booking->booking_id }}</title>
    @php
        $mt = $lab->headerless_margin_top;
        $mb = $lab->headerless_margin_bottom;
        $marginTop = min(60, max(10, ($mt !== null && $mt !== '') ? intval($mt) : 40));
        $marginBottom = min(50, max(10, ($mb !== null && $mb !== '') ? intval($mb) : 30));
    @endphp
    <style>
        @page { margin: 0; size: A4; }
        
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, sans-serif; font-size: 10px; color: #334155; line-height: 1.4; }
        
        /* Page structure - each test gets its own page */
        .page { page-break-after: always; }
        .page:last-child { page-break-after: auto; }

        /* Teal Corporate Header */
        .header { background: linear-gradient(135deg, #0f766e 0%, #0d9488 100%); padding: 15px 30px; }
        .header-bar { background: #134e4a; height: 8px; }
        .header-table { width: 100%; }
        .header-logo { width: 70px; vertical-align: middle; }
        .header-info { padding-left: 15px; color: white; }
        .header-qr { width: 80px; text-align: right; vertical-align: middle; }
        .lab-name { font-size: 22px; font-weight: 700; color: white; margin-bottom: 2px; }
        .lab-tagline { font-size: 11px; color: #99f6e4; background: rgba(255,255,255,0.1); display: inline-block; padding: 2px 8px; border-radius: 10px; margin-bottom: 4px; }
        .lab-contact { font-size: 9px; color: #ccfbf1; }
        
        /* Patient Info Strip */
        .patient-strip { background: #f0fdfa; border-bottom: 2px solid #0f766e; padding: 12px 30px; }
        .patient-table { width: 100%; }
        .patient-row td { padding: 3px 0; }
        .patient-label { width: 100px; font-weight: 600; color: #0f766e; font-size: 9px; text-transform: uppercase; }
        .patient-value { color: #334155; font-size: 10px; }
        .patient-col { width: 50%; vertical-align: top; }
        
        /* Test Content */
        .test-content { padding: 15px 30px; }
        .test-category { font-size: 11px; color: #0f766e; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; padding: 5px 0; border-bottom: 1px solid #99f6e4; margin-bottom: 10px; }
        .test-name { font-size: 13px; font-weight: 700; color: #0f766e; padding: 5px 12px; background: #f0fdfa; border-left: 3px solid #0f766e; margin-bottom: 10px; }
        
        /* Results Table */
        .results-table { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        .results-table th { background: #0f766e; color: white; font-size: 9px; font-weight: 600; text-transform: uppercase; padding: 8px 10px; text-align: left; }
        .results-table td { padding: 8px 10px; border-bottom: 1px solid #e2e8f0; font-size: 10px; }
        .results-table tr:nth-child(even) { background: #f8fafc; }
        .param-name { font-weight: 500; color: #1e293b; }
        .param-method { font-size: 8px; color: #64748b; }
        
        /* Value flags */
        .value-normal { color: #059669; font-weight: 600; }
        .value-high { color: #dc2626; font-weight: 700; }
        .value-low { color: #2563eb; font-weight: 700; }
        .value-critical { color: #dc2626; font-weight: 700; background: #fee2e2; padding: 2px 6px; border-radius: 3px; }
        .flag-badge { display: inline-block; font-size: 8px; font-weight: 700; padding: 1px 5px; border-radius: 8px; margin-left: 4px; }
        .flag-high { background: #fee2e2; color: #b91c1c; }
        .flag-low { background: #dbeafe; color: #1d4ed8; }
        
        /* Interpretation/Notes */
        .interpretation { background: #fef3c7; border-left: 3px solid #f59e0b; padding: 10px 12px; margin: 10px 0; font-size: 9px; }
        .interpretation-title { font-weight: 700; color: #b45309; margin-bottom: 3px; }
        .clinical-notes { background: #f0fdf4; border-left: 3px solid #22c55e; padding: 10px 12px; margin: 10px 0; font-size: 9px; color: #166534; }
        
        /* Footer */
        .footer { padding: 15px 30px; margin-top: 20px; border-top: 2px solid #0f766e; }
        .sig-table { width: 100%; }
        .sig-cell { width: 50%; vertical-align: bottom; text-align: center; }
        .sig-image { height: 40px; margin-bottom: 3px; }
        .sig-name { font-weight: 700; color: #0f766e; font-size: 11px; }
        .sig-desg { font-size: 9px; color: #64748b; }
        .footer-bar { background: #0f766e; height: 4px; margin-top: 10px; }
        .footer-meta { margin-top: 5px; font-size: 8px; color: #94a3b8; display: flex; justify-content: space-between; }
        
        /* QR code in header */
        .qr-box { text-align: center; }
        .qr-label { font-size: 7px; color: #99f6e4; margin-top: 2px; }
    </style>
</head>
<body>
@php
    // Logo processing
    $logoPath = $lab->logo ? storage_path('app/public/' . $lab->logo) : null;
    $logoBase64 = null;
    if ($logoPath && file_exists($logoPath)) {
        $ext = pathinfo($logoPath, PATHINFO_EXTENSION);
        $mime = in_array($ext, ['jpg','jpeg']) ? 'image/jpeg' : ($ext === 'png' ? 'image/png' : 'image/webp');
        $logoBase64 = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($logoPath));
    }
    
    // Signature processing
    $sig1Path = $lab->signature_image ? storage_path('app/public/' . $lab->signature_image) : null;
    $sig1Base64 = null;
    if ($sig1Path && file_exists($sig1Path)) {
        $ext = pathinfo($sig1Path, PATHINFO_EXTENSION);
        $mime = in_array($ext, ['jpg','jpeg']) ? 'image/jpeg' : ($ext === 'png' ? 'image/png' : 'image/webp');
        $sig1Base64 = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($sig1Path));
    }
    $sig2Path = $lab->signature_image_2 ? storage_path('app/public/' . $lab->signature_image_2) : null;
    $sig2Base64 = null;
    if ($sig2Path && file_exists($sig2Path)) {
        $ext = pathinfo($sig2Path, PATHINFO_EXTENSION);
        $mime = in_array($ext, ['jpg','jpeg']) ? 'image/jpeg' : ($ext === 'png' ? 'image/png' : 'image/webp');
        $sig2Base64 = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($sig2Path));
    }
    
    // QR processing
    $qrBase64 = null;
    try {
        $q = new \Endroid\QrCode\QrCode(url('/report-pdf/' . ($booking->report?->report_id ?? $booking->booking_id)));
        $q->setSize(70)->setMargin(0);
        $w = new \Endroid\QrCode\Writer\PngWriter();
        $qrBase64 = 'data:image/png;base64,' . base64_encode($w->write($q)->getString());
    } catch(\Exception $e) {}
    
    // Filter valid tests
    $validTests = $booking->bookingTests->filter(function($bt) {
        if ($bt->test->hasParameters() && $bt->parameterResults->where('value', '!=', null)->count() > 0) return true;
        if ($bt->result && $bt->result->value && $bt->result->status === 'approved') return true;
        return false;
    });
    
    $pageNum = 0;
@endphp

{{-- ONE PAGE PER TEST --}}
@foreach($validTests as $bookingTest)
@php $pageNum++; @endphp
<div class="page">
    {{-- HEADER --}}
    @if($showHeader ?? true)
    <div class="header-bar"></div>
    <div class="header">
        <table class="header-table">
            <tr>
                <td class="header-logo">
                    @if($logoBase64)
                    <img src="{{ $logoBase64 }}" style="max-width: {{ $lab->logo_width ?? 60 }}px; max-height: {{ $lab->logo_height ?? 60 }}px; border-radius: 6px;">
                    @else
                    <div style="width:55px;height:55px;background:white;color:#0f766e;text-align:center;line-height:55px;font-weight:bold;font-size:24px;border-radius:8px;">{{ substr($lab->name ?? 'L', 0, 1) }}</div>
                    @endif
                </td>
                <td class="header-info">
                    <div class="lab-name">{{ $lab->name }}</div>
                    <div class="lab-tagline">{{ $lab->tagline ?? 'Excellence in Diagnostics' }}</div>
                    <div class="lab-contact">📍 {{ $lab->address }} | 📞 {{ $lab->phone }} | ✉ {{ $lab->email }}</div>
                </td>
                <td class="header-qr">
                    @if($qrBase64)
                    <div class="qr-box">
                        <img src="{{ $qrBase64 }}" style="width: 55px; height: 55px;">
                        <div class="qr-label">SCAN TO VERIFY</div>
                    </div>
                    @endif
                </td>
            </tr>
        </table>
    </div>
    @else
    {{-- Spacer for pre-printed paper when header is disabled --}}
    <div style="height: {{ $marginTop }}mm;"></div>
    @endif
    
    {{-- PATIENT INFO --}}
    <div class="patient-strip">
        <table class="patient-table">
            <tr class="patient-row">
                <td class="patient-col">
                    <table style="width: 100%">
                        <tr><td class="patient-label">Patient Name</td><td class="patient-value">{{ $booking->patient->name }}</td></tr>
                        <tr><td class="patient-label">Age / Gender</td><td class="patient-value">{{ $booking->patient->age }} / {{ ucfirst(substr($booking->patient->gender ?? '', 0, 1)) }}</td></tr>
                        <tr><td class="patient-label">Referred By</td><td class="patient-value">{{ $booking->referred_by ?: 'Self' }}</td></tr>
                    </table>
                </td>
                <td class="patient-col">
                    <table style="width: 100%">
                        <tr><td class="patient-label">Patient ID</td><td class="patient-value">{{ $booking->patient->patient_id }}</td></tr>
                        <tr><td class="patient-label">Booking ID</td><td class="patient-value">{{ $booking->booking_id }}</td></tr>
                        <tr><td class="patient-label">Report Date</td><td class="patient-value">{{ now()->format('d/m/Y') }}</td></tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>
    
    {{-- TEST CONTENT --}}
    <div class="test-content">
        @if($bookingTest->test->category)
        <div class="test-category">{{ $bookingTest->test->category->name }}</div>
        @endif
        <div class="test-name">{{ $bookingTest->test->name }}</div>
        
        @if($bookingTest->test->hasParameters())
        <table class="results-table">
            <thead>
                <tr>
                    <th style="width: 35%">Parameter</th>
                    <th style="width: 20%">Result</th>
                    <th style="width: 25%">Reference Range</th>
                    <th style="width: 20%">Unit</th>
                </tr>
            </thead>
            <tbody>
                @php $currentGroup = null; @endphp
                @foreach($bookingTest->test->parameters->sortBy('sort_order') as $param)
                    @php
                        $paramResult = $bookingTest->parameterResults->firstWhere('test_parameter_id', $param->id);
                        $value = $paramResult?->value;
                    @endphp
                    @if($value === null) @continue @endif
                    
                    @if($param->group_name && $param->group_name !== $currentGroup)
                        @php $currentGroup = $param->group_name; @endphp
                        <tr style="background: #e0f2fe;">
                            <td colspan="4" style="font-weight: 600; color: #0369a1; padding: 6px 10px;">{{ $currentGroup }}</td>
                        </tr>
                    @endif
                    
                    @php
                        $flag = $paramResult?->flag ?? $param->checkFlag($value, $booking->patient->gender);
                        $valClass = match($flag) {
                            'high' => 'value-high',
                            'low' => 'value-low',
                            'critical_low', 'critical_high' => 'value-critical',
                            default => 'value-normal'
                        };
                        $flagLabel = match($flag) {
                            'high', 'critical_high' => 'HIGH',
                            'low', 'critical_low' => 'LOW',
                            default => null
                        };
                        $flagClass = str_contains($flag ?? '', 'high') ? 'flag-high' : (str_contains($flag ?? '', 'low') ? 'flag-low' : '');
                    @endphp
                    <tr>
                        <td>
                            <span class="param-name">{{ $param->name }}</span>
                            @if($param->method)<br><span class="param-method">{{ $param->method }}</span>@endif
                        </td>
                        <td>
                            <span class="{{ $valClass }}">{{ $value }}</span>
                            @if($flagLabel)<span class="flag-badge {{ $flagClass }}">{{ $flagLabel }}</span>@endif
                        </td>
                        <td>{{ $param->getReferenceRange($booking->patient->gender) }}</td>
                        <td>{{ $param->unit }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        @else
        {{-- Non-parametric test --}}
        <div style="padding: 15px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px;">
            {!! nl2br(e($bookingTest->result?->value ?? 'No result')) !!}
        </div>
        @endif
        
        {{-- Clinical Notes --}}
        @if($bookingTest->result?->notes)
        <div class="clinical-notes">
            <strong>Clinical Notes:</strong> {{ $bookingTest->result->notes }}
        </div>
        @endif
        
        {{-- Interpretation --}}
        @if($bookingTest->result?->interpretation)
        <div class="interpretation">
            <div class="interpretation-title">Clinical Interpretation</div>
            {!! nl2br(e($bookingTest->result->interpretation)) !!}
        </div>
        @endif
    </div>
    
    {{-- FOOTER --}}
    <div class="footer">
        <table class="sig-table">
            <tr>
                @if($sig1Base64 || $lab->signature_name)
                <td class="sig-cell">
                    @if($sig1Base64)<img src="{{ $sig1Base64 }}" class="sig-image" style="width: {{ $lab->signature_width ?? 100 }}px; height: {{ $lab->signature_height ?? 40 }}px;">@endif
                    <div class="sig-name">{{ $lab->signature_name }}</div>
                    <div class="sig-desg">{{ $lab->signature_designation }}</div>
                </td>
                @endif
                @if($sig2Base64 || $lab->signature_name_2)
                <td class="sig-cell">
                    @if($sig2Base64)<img src="{{ $sig2Base64 }}" class="sig-image" style="width: {{ $lab->signature_width_2 ?? 100 }}px; height: {{ $lab->signature_height_2 ?? 40 }}px;">@endif
                    <div class="sig-name">{{ $lab->signature_name_2 }}</div>
                    <div class="sig-desg">{{ $lab->signature_designation_2 }}</div>
                </td>
                @endif
            </tr>
        </table>
        <div class="footer-bar"></div>
        <div class="footer-meta">
            <span>Report ID: {{ $booking->booking_id }}</span>
            <span>Generated: {{ now()->format('d M Y, h:i A') }}</span>
        </div>
    </div>
</div>
@endforeach
</body>
</html>