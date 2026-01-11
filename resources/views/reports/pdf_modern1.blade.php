<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Lab Report - {{ $booking->booking_id }}</title>
    @php
        $mt = $lab->headerless_margin_top;
        $mb = $lab->headerless_margin_bottom;
        $marginTop = min(60, max(10, ($mt !== null && $mt !== '') ? intval($mt) : 40));
        $marginBottom = min(50, max(10, ($mb !== null && $mb !== '') ? intval($mb) : 30));
    @endphp
    <style>
        @page { margin: 0; size: A4; }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Arial, sans-serif; font-size: 10px; color: #333; line-height: 1.4; }
        
        /* Page structure - each test gets its own page */
        .page { 
            page-break-after: always; 
        }
        .page:last-child { page-break-after: auto; }

        /* Header - Modern Clean Style */
        .header {
            padding: 25px 40px;
            background: #fff;
            border-bottom: 2px solid #e5e7eb;
        }
        .header-top { display: table; width: 100%; }
        .logo-cell { display: table-cell; width: 80px; vertical-align: middle; }
        .info-cell { display: table-cell; vertical-align: middle; padding-left: 20px; }
        .qr-cell { display: table-cell; width: 70px; vertical-align: middle; text-align: right; }
        
        .lab-title { font-size: 22px; font-weight: 800; color: #111827; margin-bottom: 3px; }
        .lab-sub { font-size: 10px; color: #6b7280; font-weight: 600; letter-spacing: 1px; text-transform: uppercase; }
        .lab-details { font-size: 9px; color: #9ca3af; margin-top: 5px; line-height: 1.4; }

        /* Patient Card - Modern floating style */
        .patient-container { padding: 20px 40px; }
        .patient-card {
            background: #f9fafb;
            border-radius: 12px;
            padding: 20px;
            border: 1px solid #e5e7eb;
            display: table;
            width: 100%;
        }
        .p-col { display: table-cell; width: 33.33%; vertical-align: top; }
        .p-group { margin-bottom: 12px; }
        .p-label { font-size: 8px; color: #9ca3af; text-transform: uppercase; font-weight: 600; letter-spacing: 0.5px; margin-bottom: 2px; }
        .p-value { font-size: 11px; color: #111827; font-weight: 600; }

        /* Test Content */
        .test-container { padding: 10px 40px; }
        .category-badge {
            display: inline-block;
            padding: 6px 12px;
            background: #f3f4f6;
            border-radius: 20px;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 600;
            color: #6b7280;
            margin-bottom: 10px;
        }
        .test-title { font-size: 16px; font-weight: 700; color: #111827; margin-bottom: 15px; }

        /* Results Table */
        .results-table { width: 100%; border-collapse: collapse; }
        .results-table th { 
            background: #374151; 
            color: white; 
            padding: 10px 15px; 
            text-align: left; 
            font-size: 9px; 
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .results-table td { 
            padding: 12px 15px; 
            border-bottom: 1px dashed #f3f4f6; 
            font-size: 11px; 
        }
        .results-table tr:last-child td { border-bottom: none; }

        .t-name { font-weight: 600; color: #374151; }
        .t-method { font-size: 8px; color: #9ca3af; font-style: italic; }

        .v-normal { color: #059669; font-weight: 700; }
        .v-high { color: #dc2626; font-weight: 700; }
        .v-low { color: #2563eb; font-weight: 700; }
        .v-critical { color: #dc2626; font-weight: 800; background: #fef2f2; padding: 2px 8px; border-radius: 4px; }

        .ref-range { color: #9ca3af; font-size: 10px; }
        .unit-col { color: #6b7280; }

        .group-header { 
            background: #f3f4f6 !important; 
            font-weight: 700 !important; 
            color: #374151 !important;
            font-size: 10px !important;
        }

        /* Clinical Notes */
        .clinical-notes {
            margin: 15px 0;
            padding: 12px 15px;
            background: #f8f9fa;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
        }
        .clinical-notes-title { font-weight: 700; color: #374151; font-size: 10px; margin-bottom: 8px; }
        .clinical-notes-content { font-size: 9px; color: #6b7280; line-height: 1.5; }

        /* Interpretation */
        .interpretation {
            margin: 15px 0;
            padding: 12px 15px;
            background: #fef3c7;
            border-left: 3px solid #f59e0b;
            border-radius: 0 8px 8px 0;
        }
        .interpretation-title { font-weight: 700; color: #b45309; font-size: 9px; margin-bottom: 5px; }
        .interpretation-content { font-size: 10px; color: #92400e; }

        /* Footer - flows after content */
        .footer {
            margin-top: 30px;
            padding: 15px 40px;
        }
        .footer-line { border-top: 2px solid #111827; margin-bottom: 15px; }
        .doc-grid { display: table; width: 100%; }
        .doc-box { display: table-cell; width: 50%; vertical-align: bottom; }
        .doc-sig-img { height: 35px; margin-bottom: 5px; }
        .doc-name { font-size: 11px; font-weight: 700; color: #111827; }
        .doc-role { font-size: 9px; color: #6b7280; }

        .footer-bottom { 
            text-align: center; 
            font-size: 9px; 
            color: #9ca3af; 
            margin-top: 15px; 
            padding-top: 10px;
            border-top: 1px dashed #e5e7eb;
        }

        /* Notes Section */
        .notes-section {
            margin: 10px 40px;
            padding: 8px 12px;
            background: #f9fafb;
            border-radius: 6px;
            font-size: 9px;
            color: #6b7280;
        }

        /* End of report marker */
        .end-report {
            text-align: center;
            font-size: 9px;
            color: #9ca3af;
            margin-bottom: 15px;
        }

        /* High/Low flags */
        .flag-badge {
            font-size: 8px;
            padding: 2px 6px;
            border-radius: 10px;
            font-weight: 700;
            display: inline-block;
        }
        .flag-high { background: #fee2e2; color: #b91c1c; }
        .flag-low { background: #dbeafe; color: #1d4ed8; }
    </style>
</head>
<body>
@php
    // Prepare logo and QR code
    $logoBase64 = null;
    if ($lab->logo) {
        $path = storage_path('app/public/' . $lab->logo);
        if (file_exists($path)) {
            $logoBase64 = 'data:' . mime_content_type($path) . ';base64,' . base64_encode(file_get_contents($path));
        }
    }

    $qrBase64 = null;
    try {
        $reportId = $booking->report?->report_id ?? $booking->booking_id;
        $qrUrl = url('/report-pdf/' . $reportId);
        $qrCode = new \Endroid\QrCode\QrCode($qrUrl);
        $qrCode->setSize(120);
        $qrCode->setMargin(5);
        $writer = new \Endroid\QrCode\Writer\PngWriter();
        $result = $writer->write($qrCode);
        $qrBase64 = 'data:image/png;base64,' . base64_encode($result->getString());
    } catch (\Exception $e) {
        $qrBase64 = null;
    }

    // Prepare signature images
    $sig1Base64 = null;
    if ($lab->signature_image) {
        $sigPath = storage_path('app/public/' . $lab->signature_image);
        if (file_exists($sigPath)) {
            $sig1Base64 = 'data:image/png;base64,' . base64_encode(file_get_contents($sigPath));
        }
    }

    $sig2Base64 = null;
    if ($lab->signature_image_2) {
        $sig2Path = storage_path('app/public/' . $lab->signature_image_2);
        if (file_exists($sig2Path)) {
            $sig2Base64 = 'data:image/png;base64,' . base64_encode(file_get_contents($sig2Path));
        }
    }

    // Filter tests with valid results
    $validTests = $booking->bookingTests->filter(function($bt) {
        if ($bt->test->hasParameters() && $bt->parameterResults->where('value', '!=', null)->count() > 0) return true;
        if ($bt->result && $bt->result->value && $bt->result->status === 'approved') return true;
        return false;
    });

    $pageNum = 0;
    $totalPages = $validTests->count();
@endphp

{{-- Each test gets its own page with header, patient info, and footer --}}
@foreach($validTests as $bookingTest)
@php $pageNum++; @endphp
<div class="page">

    {{-- HEADER - Conditional based on showHeader --}}
    @if($showHeader ?? true)
    <div class="header">
        <div class="header-top">
            <div class="logo-cell">
                @if($logoBase64)
                <img src="{{ $logoBase64 }}" style="max-height: 50px; max-width: 80px;">
                @else
                <div style="background:#111827;color:#fff;width:50px;height:50px;line-height:50px;text-align:center;font-weight:800;border-radius:8px;">
                    {{ substr($lab->name ?? 'L', 0, 1) }}
                </div>
                @endif
            </div>
            <div class="info-cell">
                <div class="lab-title">{{ $lab->name ?? 'Diagnostic Center' }}</div>
                <div class="lab-sub">Medical Laboratory Report</div>
                <div class="lab-details">
                    {{ $lab->address_street ?? '' }} {{ $lab->address_city ?? '' }}<br>
                    {{ $lab->phone ? 'Tel: '.$lab->phone : '' }} {{ $lab->email ? ' • '.$lab->email : '' }}
                </div>
            </div>
            <div class="qr-cell">
                @if($qrBase64)
                <img src="{{ $qrBase64 }}" style="width: 50px; height: 50px;">
                @endif
            </div>
        </div>
    </div>
    @else
    {{-- Spacer for pre-printed paper when header is disabled --}}
    <div style="height: {{ $marginTop }}mm;"></div>
    @endif

    {{-- PATIENT INFO - On every page --}}
    <div class="patient-container">
        <div class="patient-card">
            <div class="p-col">
                <div class="p-group">
                    <div class="p-label">Patient Name</div>
                    <div class="p-value">{{ $booking->patient->name }}</div>
                </div>
                <div class="p-group">
                    <div class="p-label">Patient ID</div>
                    <div class="p-value">{{ $booking->patient->patient_id }}</div>
                </div>
            </div>
            <div class="p-col">
                <div class="p-group">
                    <div class="p-label">Age / Gender</div>
                    <div class="p-value">{{ $booking->patient->age }} / {{ ucfirst($booking->patient->gender ?? '') }}</div>
                </div>
                <div class="p-group">
                    <div class="p-label">Referred By</div>
                    <div class="p-value">{{ $booking->referring_doctor_name ?? 'Self' }}</div>
                </div>
            </div>
            <div class="p-col" style="text-align: right;">
                <div class="p-group">
                    <div class="p-label">Sample Date</div>
                    <div class="p-value">{{ $booking->collection_date ? $booking->collection_date->format('d/m/Y') : '-' }}</div>
                </div>
                <div class="p-group">
                    <div class="p-label">Report ID</div>
                    <div class="p-value">#{{ $booking->report?->report_id ?? $booking->booking_id }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- TEST CONTENT --}}
    <div class="test-container">
        <span class="category-badge">{{ $bookingTest->test->category->name ?? 'Laboratory' }}</span>
        <div class="test-title">{{ $bookingTest->test->name }}</div>

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
                    @php
                        $paramResult = $paramResults->get($param->id);
                        $value = $paramResult?->value;
                    @endphp
                    @if(empty($value) && $value !== '0' && $value !== 0)
                        @continue
                    @endif
                    @if($param->group_name && $param->group_name !== $currentGroup)
                        @php $currentGroup = $param->group_name; @endphp
                        <tr class="group-header">
                            <td colspan="4">{{ $currentGroup }}</td>
                        </tr>
                    @endif
                    @php
                        $flag = $paramResult?->flag ?? $param->checkFlag($value, $booking->patient->gender);
                        $flagClass = match($flag) {
                            'low' => 'v-low',
                            'high' => 'v-high',
                            'critical_low', 'critical_high' => 'v-critical',
                            default => 'v-normal'
                        };
                    @endphp
                    <tr>
                        <td>
                            <span class="t-name">{{ $param->name }}</span>
                            @if($param->method)<br><span class="t-method">{{ $param->method }}</span>@endif
                        </td>
                        <td class="{{ $flagClass }}">{{ $value }}</td>
                        <td class="ref-range">{{ $param->getNormalRange($booking->patient->gender) }}</td>
                        <td class="unit-col">{{ $param->unit }}</td>
                    </tr>
                    @if($param->interpretation)
                    <tr>
                        <td colspan="4" style="padding: 4px 15px 8px; background: #fffbeb; border-left: 3px solid #f59e0b; font-size: 9px; color: #92400e;">
                            <em>{{ $param->interpretation }}</em>
                        </td>
                    </tr>
                    @endif
                @endforeach
            @else
                <tr>
                    <td class="t-name">{{ $bookingTest->test->name }}</td>
                    <td><strong>{{ $bookingTest->result?->value ?? '-' }}</strong></td>
                    <td class="ref-range">{{ $bookingTest->test->normal_range ?? '-' }}</td>
                    <td class="unit-col">{{ $bookingTest->test->unit }}</td>
                </tr>
            @endif
            </tbody>
        </table>

        {{-- Clinical Notes --}}
        @if($bookingTest->test->interpretation)
        <div class="clinical-notes">
            <div class="clinical-notes-title">Clinical Notes</div>
            <div class="clinical-notes-content">{!! $bookingTest->test->interpretation !!}</div>
        </div>
        @endif

        {{-- Custom Interpretation --}}
        @if($bookingTest->result?->notes)
        <div class="interpretation">
            <div class="interpretation-title">Interpretation</div>
            <div class="interpretation-content">{{ $bookingTest->result->notes }}</div>
        </div>
        @endif
    </div>

    {{-- Lab Notes --}}
    @if($lab->report_notes)
    <div class="notes-section">
        <strong>Notes:</strong> {{ $lab->report_notes }}
    </div>
    @endif

    {{-- FOOTER - On every page --}}
    <div class="footer">
        <div class="footer-line"></div>
        <div class="end-report">****End of Report****</div>
        
        <div class="doc-grid">
            <div class="doc-box" style="text-align: left;">
                @if($sig1Base64)
                <img src="{{ $sig1Base64 }}" class="doc-sig-img">
                @else
                <div style="border-bottom: 1px solid #333; width: 120px; margin-bottom: 5px;">&nbsp;</div>
                @endif
                <div class="doc-name">{{ $lab->signature_name ?? 'Authorized Signatory' }}</div>
                <div class="doc-role">{{ $lab->signature_designation ?? '' }}</div>
            </div>
            <div class="doc-box" style="text-align: right;">
                @if($sig2Base64)
                <img src="{{ $sig2Base64 }}" class="doc-sig-img">
                @else
                <div style="border-bottom: 1px solid #333; width: 120px; margin-left: auto; margin-bottom: 5px;">&nbsp;</div>
                @endif
                <div class="doc-name">{{ $lab->signature_name_2 ?? 'Pathologist' }}</div>
                <div class="doc-role">{{ $lab->signature_designation_2 ?? '' }}</div>
            </div>
        </div>

        <div class="footer-bottom">
            Page {{ $pageNum }} of {{ $totalPages }} • Report Generated: {{ now()->format('d M Y, h:i A') }}
        </div>
    </div>

</div>
@endforeach
</body>
</html>