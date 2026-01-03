<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Lab Report - {{ $booking->booking_id }}</title>
    <style>
        @if($showHeader ?? true)
        @page { margin: 0; size: A4; }
        @else
        @php
            $marginTop = $lab->headerless_margin_top ?? 40;
            $marginBottom = $lab->headerless_margin_bottom ?? 30;
        @endphp
        @page { margin: {{ $marginTop }}mm 15mm {{ $marginBottom }}mm 15mm; size: A4; }
        @endif

        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 10px; color: #1f2937; line-height: 1.4; background: #fff; }

        .page { page-break-after: always; min-height: {{ ($showHeader ?? true) ? '297mm' : 'auto' }}; position: relative; }
        .page:last-child { page-break-after: auto; }

        /* Modern Header V1 - Clean & Minimal */
        .header { padding: 30px 40px; background: #fff; border-bottom: 2px solid #f3f4f6; }
        .header-top { display: table; width: 100%; margin-bottom: 15px; }
        .logo-cell { display: table-cell; width: 20%; vertical-align: middle; }
        .info-cell { display: table-cell; width: 60%; vertical-align: middle; padding-left: 20px; }
        .qr-cell { display: table-cell; width: 20%; text-align: right; vertical-align: middle; }

        .lab-title { font-size: 20px; font-weight: 800; color: #111827; letter-spacing: -0.5px; margin-bottom: 4px; text-transform: uppercase; }
        .lab-sub { font-size: 10px; color: #6b7280; font-weight: 500; text-transform: uppercase; letter-spacing: 1px; }
        .lab-details { margin-top: 8px; font-size: 9px; color: #4b5563; }

        /* Patient Card - Floating Style */
        .patient-container { padding: 20px 40px; }
        .patient-card { background: #f9fafb; border-radius: 12px; padding: 20px; border: 1px solid #e5e7eb; display: table; width: 100%; }
        .p-col { display: table-cell; width: 33.33%; vertical-align: top; }
        .p-group { margin-bottom: 8px; }
        .p-label { font-size: 8px; color: #9ca3af; text-transform: uppercase; font-weight: 600; letter-spacing: 0.5px; margin-bottom: 2px; }
        .p-value { font-size: 11px; color: #111827; font-weight: 600; }

        /* Test Section */
        .test-container { padding: 10px 40px; }
        .cat-header {
            background: #111827; color: #fff;
            padding: 8px 15px; border-radius: 6px;
            font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;
            margin-bottom: 15px; display: table; width: 100%;
        }

        .results-table { width: 100%; border-collapse: separate; border-spacing: 0; }
        .results-table th {
            text-align: left; color: #6b7280; font-size: 9px; font-weight: 700; text-transform: uppercase;
            padding: 10px 15px; border-bottom: 1px solid #e5e7eb;
        }
        .results-table td { padding: 12px 15px; border-bottom: 1px dashed #f3f4f6; font-size: 11px; }
        .results-table tr:last-child td { border-bottom: none; }

        .t-name { font-weight: 600; color: #374151; }
        .t-method { font-size: 8px; color: #9ca3af; font-weight: 400; display: block; margin-top: 2px; }

        /* Status Badges */
        .val-normal { color: #059669; font-weight: 700; }
        .val-high { color: #dc2626; font-weight: 700; background: #fef2f2; padding: 2px 6px; border-radius: 4px; display: inline-block; }
        .val-low { color: #2563eb; font-weight: 700; background: #eff6ff; padding: 2px 6px; border-radius: 4px; display: inline-block; }

        /* Footer */
        .footer { position: absolute; bottom: 0; left: 0; right: 0; padding: 0 40px 30px; }
        .footer-line { border-top: 2px solid #111827; margin-bottom: 15px; }
        .doc-grid { display: table; width: 100%; }
        .doc-box { display: table-cell; width: 50%; vertical-align: bottom; }
        .doc-sig-img { height: 40px; margin-bottom: 5px; }
        .doc-name { font-size: 11px; font-weight: 700; color: #111827; }
        .doc-role { font-size: 9px; color: #6b7280; text-transform: uppercase; }

        .page-info { position: absolute; bottom: 10px; right: 40px; font-size: 8px; color: #d1d5db; }
        .report-id { position: absolute; bottom: 10px; left: 40px; font-size: 8px; color: #d1d5db; }

        .group-header td { background: #f9fafb; font-weight: 700; color: #374151; font-size: 10px; padding-top: 15px !important; }
    </style>
</head>
<body>
@php
    $pageNum = 0;
    $totalPages = $booking->bookingTests->count();

    // Logic to prepare images (Logo/QR) reused from original
    $logoBase64 = null;
    if ($lab->logo) {
        $path = storage_path('app/public/' . $lab->logo);
        if (file_exists($path)) {
            $logoBase64 = 'data:' . mime_content_type($path) . ';base64,' . base64_encode(file_get_contents($path));
        }
    }

    $qrBase64 = null;
    try {
        $rId = $booking->report?->report_id ?? $booking->booking_id;
        $q = new \Endroid\QrCode\QrCode(url('/report-pdf/' . $rId));
        $q->setSize(100)->setMargin(0);
        $w = new \Endroid\QrCode\Writer\PngWriter();
        $qrBase64 = 'data:image/png;base64,' . base64_encode($w->write($q)->getString());
    } catch(\Exception $e) {}
@endphp

@foreach($booking->bookingTests as $bookingTest)
@php $pageNum++; @endphp
@if(($bookingTest->test->hasParameters() && $bookingTest->parameterResults->where('value', '!=', null)->count() > 0) || ($bookingTest->result && $bookingTest->result->value && $bookingTest->result->status === 'approved'))
<div class="page">
    @if($showHeader ?? true)
    <div class="header">
        <div class="header-top">
            <div class="logo-cell">
                @if($logoBase64)
                    <img src="{{ $logoBase64 }}" style="max-height: 50px; max-width: 100px;">
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
    @endif

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
                    <div class="p-value">{{ $booking->patient->age }} / {{ ucfirst($booking->patient->gender) }}</div>
                </div>
                <div class="p-group">
                    <div class="p-label">Referred By</div>
                    <div class="p-value">{{ $booking->referring_doctor_name ?? 'Self' }}</div>
                </div>
            </div>
            <div class="p-col" style="text-align: right;">
                <div class="p-group">
                    <div class="p-label">Sample Date</div>
                    <div class="p-value">{{ $booking->collection_date ? $booking->collection_date->format('d M, Y') : '-' }}</div>
                </div>
                <div class="p-group">
                    <div class="p-label">Report ID</div>
                    <div class="p-value">#{{ $booking->report?->report_id ?? $booking->booking_id }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="test-container">
        <div class="cat-header">
            <span style="float:left;">{{ $bookingTest->test->category->name ?? 'Pathology' }} / {{ $bookingTest->test->name }}</span>
            <span style="float:right; opacity: 0.7; font-weight: 400;">Final Report</span>
        </div>

        <table class="results-table">
            <thead>
                <tr>
                    <th width="45%">Investigation</th>
                    <th width="20%">Result</th>
                    <th width="20%">Ref. Range</th>
                    <th width="15%">Unit</th>
                </tr>
            </thead>
            <tbody>
            @if($bookingTest->test->hasParameters())
                @php
                    $paramResults = $bookingTest->parameterResults->keyBy('test_parameter_id');
                    $currentGroup = null;
                @endphp
                @foreach($bookingTest->test->parameters()->ordered()->get() as $param)
                    @php $res = $paramResults->get($param->id); $val = $res?->value; @endphp
                    @if(empty($val) && $val !== '0' && $val !== 0) @continue @endif

                    @if($param->group_name && $param->group_name !== $currentGroup)
                        @php $currentGroup = $param->group_name; @endphp
                        <tr class="group-header"><td colspan="4">{{ $currentGroup }}</td></tr>
                    @endif

                    @php
                        $flag = $res?->flag ?? $param->checkFlag($val, $booking->patient->gender);
                        $cls = match($flag) { 'high'=>'val-high', 'low'=>'val-low', 'critical_high'=>'val-high', 'critical_low'=>'val-low', default=>'val-normal' };
                    @endphp
                    <tr>
                        <td>
                            <div class="t-name">{{ $param->name }}</div>
                            @if($param->method)<span class="t-method">Method: {{ $param->method }}</span>@endif
                        </td>
                        <td><span class="{{ $cls }}">{{ $val }}</span></td>
                        <td>{{ $param->getNormalRange($booking->patient->gender) }}</td>
                        <td>{{ $param->unit }}</td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td><div class="t-name">{{ $bookingTest->test->name }}</div></td>
                    <td><span class="val-normal">{{ $bookingTest->result?->value ?? '-' }}</span></td>
                    <td>{{ $bookingTest->test->normal_range ?? '-' }}</td>
                    <td>{{ $bookingTest->test->unit }}</td>
                </tr>
            @endif
            </tbody>
        </table>

        @if($bookingTest->test->interpretation || $bookingTest->result?->notes)
        <div style="margin-top: 20px; padding: 15px; background: #fff7ed; border-radius: 8px; border: 1px solid #ffedd5;">
            <div style="color: #c2410c; font-weight: 700; font-size: 9px; text-transform: uppercase; margin-bottom: 5px;">Interpretation & Notes</div>
            <div style="font-size: 10px; color: #431407;">
                {!! $bookingTest->test->interpretation !!}
                {{ $bookingTest->result?->notes }}
            </div>
        </div>
        @endif
    </div>

    <div class="footer">
        <div class="footer-line"></div>
        <div class="doc-grid">
            <div class="doc-box">
                @if($lab->signature_image && file_exists(storage_path('app/public/'.$lab->signature_image)))
                    <img src="data:image/png;base64,{{ base64_encode(file_get_contents(storage_path('app/public/'.$lab->signature_image))) }}" class="doc-sig-img">
                @endif
                <div class="doc-name">{{ $lab->signature_name ?? 'Lab Technologist' }}</div>
                <div class="doc-role">{{ $lab->signature_designation ?? 'Technician' }}</div>
            </div>
            <div class="doc-box" style="text-align: right;">
                 @if($lab->signature_image_2 && file_exists(storage_path('app/public/'.$lab->signature_image_2)))
                    <img src="data:image/png;base64,{{ base64_encode(file_get_contents(storage_path('app/public/'.$lab->signature_image_2))) }}" class="doc-sig-img">
                @endif
                <div class="doc-name">{{ $lab->signature_name_2 ?? 'Pathologist' }}</div>
                <div class="doc-role">{{ $lab->signature_designation_2 ?? 'Doctor' }}</div>
            </div>
        </div>
        <div class="report-id">{{ $booking->booking_id }}</div>
        <div class="page-info">Page {{ $pageNum }} of {{ $totalPages }}</div>
    </div>
</div>
@endif
@endforeach
</body>
</html>