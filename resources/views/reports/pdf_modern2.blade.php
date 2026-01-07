<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Medical Report</title>
    <style>
        @if($showHeader ?? true)
            @page {
                margin: 0;
                size: A4;
            }
        @else
            @php
                $mt = $lab->headerless_margin_top;
                $mb = $lab->headerless_margin_bottom;
                $marginTop = ($mt !== null && $mt !== '') ? intval($mt) : 40;
                $marginBottom = ($mb !== null && $mb !== '') ? intval($mb) : 30;
            @endphp
            @page {
                margin: {{ $marginTop }}mm 15mm {{ $marginBottom }}mm 15mm;
                size: A4;
            }
        @endif

        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; font-size: 10px; color: #334155; line-height: 1.5; }

        /* Container for continuous flow */
        .report-container {
            width: 100%;
        }

        /* Modern V2 - Teal/Blue Corporate */
        .header-bar { background: #0f766e; height: 15px; width: 100%; }
        .header-main { padding: 25px 40px; background: #f0fdfa; border-bottom: 1px solid #ccfbf1; display: table; width: 100%; }

        .h-logo { display: table-cell; width: 60px; vertical-align: top; }
        .h-content { display: table-cell; padding-left: 15px; vertical-align: top; }
        .h-side { display: table-cell; width: 120px; text-align: right; vertical-align: top; }

        .lab-name { font-size: 24px; font-weight: 700; color: #0f766e; line-height: 1.1; }
        .lab-tag { font-size: 10px; color: #5eead4; background: #134e4a; display: inline-block; padding: 2px 6px; border-radius: 4px; margin-top: 5px; text-transform: uppercase; font-weight: 600; }
        .lab-addr { margin-top: 8px; font-size: 9px; color: #5f6b7c; }

        /* Patient Strip */
        .p-strip { background: #fff; padding: 15px 40px; border-bottom: 2px solid #0f766e; }
        .p-grid { display: table; width: 100%; }
        .p-block { display: table-cell; width: 25%; vertical-align: top; }
        .p-lbl { font-size: 8px; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600; }
        .p-val { font-size: 11px; color: #0f172a; font-weight: 700; margin-bottom: 5px; }

        /* Content Area */
        .content { padding: 30px 40px; }

        .test-box { margin-bottom: 30px; page-break-inside: avoid; }
        .test-header {
            background: linear-gradient(90deg, #0f766e 0%, #14b8a6 100%);
            color: white; padding: 8px 15px; border-radius: 6px 6px 0 0;
            font-size: 12px; font-weight: 700; text-transform: uppercase;
        }

        .res-table { width: 100%; border-collapse: collapse; border: 1px solid #e2e8f0; border-top: none; }
        .res-table th { background: #f1f5f9; color: #475569; font-size: 9px; font-weight: 700; text-transform: uppercase; padding: 8px 15px; text-align: left; }
        .res-table td { padding: 10px 15px; border-bottom: 1px solid #f1f5f9; font-size: 10px; }
        .res-table tr:nth-child(even) { background: #f8fafc; }

        .flag-badge { padding: 2px 6px; border-radius: 10px; font-size: 9px; font-weight: 700; display: inline-block; }
        .flag-high { background: #fee2e2; color: #b91c1c; }
        .flag-low { background: #dbeafe; color: #1d4ed8; }
        .flag-norm { color: #059669; font-weight: 600; }

        /* Footer */
        .footer {
            width: 100%;
            margin-top: 50px;
            page-break-inside: avoid;
        }
        .footer-content { margin: 0 40px; border-top: 1px solid #cbd5e1; padding-top: 15px; }

        .sig-row { display: table; width: 100%; }
        .sig-cell { display: table-cell; width: 50%; vertical-align: bottom; }
        .sig-img { max-height: 40px; display: block; margin-bottom: 5px; }
        .sig-name { font-weight: 700; font-size: 10px; color: #0f766e; }
        .sig-role { font-size: 9px; color: #64748b; }

        .footer-bar { width: 100%; background: #0f766e; color: white; padding: 8px 40px; font-size: 9px; margin-top: 20px; }
        .fb-tbl { width: 100%; }
        .fb-l { text-align: left; }
        .fb-r { text-align: right; }
    </style>
</head>
<body>
@php
    // Logo processing
    $logoBase64 = null;
    if ($lab->logo) {
        $path = storage_path('app/public/' . $lab->logo);
        if (file_exists($path)) {
            $logoBase64 = 'data:' . mime_content_type($path) . ';base64,' . base64_encode(file_get_contents($path));
        }
    }

    // QR processing
    $qrBase64 = null;
    try {
        $q = new \Endroid\QrCode\QrCode(url('/report-pdf/' . ($booking->report?->report_id ?? $booking->booking_id)));
        $q->setSize(80)->setMargin(0);
        $w = new \Endroid\QrCode\Writer\PngWriter();
        $qrBase64 = 'data:image/png;base64,' . base64_encode($w->write($q)->getString());
    } catch(\Exception $e) {}

    // Filter valid tests
    $validTests = $booking->bookingTests->filter(function($bt) {
        if ($bt->test->hasParameters() && $bt->parameterResults->where('value', '!=', null)->count() > 0) return true;
        if ($bt->result && $bt->result->value && $bt->result->status === 'approved') return true;
        return false;
    });
@endphp

<div class="report-container">
    @if($showHeader ?? true)
    <div class="header-bar"></div>
    <div class="header-main">
        <div class="h-logo">
            @if($logoBase64)
            <img src="{{ $logoBase64 }}" style="max-width: 60px; max-height: 60px; border-radius: 6px;">
            @else
            <div style="width:50px;height:50px;background:#0f766e;color:white;text-align:center;line-height:50px;font-weight:bold;font-size:20px;border-radius:6px;">{{ substr($lab->name ?? '', 0, 1) }}</div>
            @endif
        </div>
        <div class="h-content">
            <div class="lab-name">{{ $lab->name }}</div>
            <div class="lab-tag">Excellence in Diagnostics</div>
            <div class="lab-addr">
                {{ $lab->address_street ?? '' }}, {{ $lab->address_city ?? '' }}<br>
                {{ $lab->phone }} | {{ $lab->email }}
            </div>
        </div>
        <div class="h-side">
            @if($qrBase64)
            <img src="{{ $qrBase64 }}" style="width: 60px;">
            <div style="font-size:8px;color:#0f766e;font-weight:600;margin-top:2px;">SCAN TO VERIFY</div>
            @endif
        </div>
    </div>
    @endif

    <div class="p-strip">
        <div class="p-grid">
            <div class="p-block">
                <div class="p-lbl">Patient Name</div>
                <div class="p-val">{{ $booking->patient->name }}</div>
                <div class="p-lbl">Patient ID</div>
                <div class="p-val" style="color:#64748b;">{{ $booking->patient->patient_id }}</div>
            </div>
            <div class="p-block">
                <div class="p-lbl">Age / Sex</div>
                <div class="p-val">{{ $booking->patient->age }} Y / {{ ucfirst(substr($booking->patient->gender ?? '', 0, 1)) }}</div>
                <div class="p-lbl">Contact</div>
                <div class="p-val" style="color:#64748b;">{{ $booking->patient->phone ?? '-' }}</div>
            </div>
             <div class="p-block">
                <div class="p-lbl">Referred By</div>
                <div class="p-val">{{ $booking->referring_doctor_name ?? 'Self' }}</div>
                <div class="p-lbl">Sample Date</div>
                <div class="p-val" style="color:#64748b;">{{ $booking->collection_date ? $booking->collection_date->format('d/m/Y') : '-' }}</div>
            </div>
            <div class="p-block" style="text-align:right;">
                 <div class="p-lbl">Report Generated</div>
                 <div class="p-val">{{ now()->format('d M Y') }}</div>
                 <div class="p-lbl">Booking ID</div>
                 <div class="p-val" style="color:#0f766e;">{{ $booking->booking_id }}</div>
            </div>
        </div>
    </div>

    <div class="content">
        @foreach($validTests as $bookingTest)
        <div class="test-box">
            <div class="test-header">
                {{ $bookingTest->test->name }}
                <span style="float:right; opacity:0.8; font-size:10px; margin-top:1px;">{{ $bookingTest->test->category->name }}</span>
            </div>
            <table class="res-table">
                <thead>
                    <tr>
                        <th width="40%">Parameter</th>
                        <th width="25%">Result</th>
                        <th width="20%">Bio. Ref. Interval</th>
                        <th width="15%">Unit</th>
                    </tr>
                </thead>
                <tbody>
                @if($bookingTest->test->hasParameters())
                    @php $resMap = $bookingTest->parameterResults->keyBy('test_parameter_id'); $currGrp = null; @endphp
                    @foreach($bookingTest->test->parameters()->ordered()->get() as $p)
                        @php $r = $resMap->get($p->id); $v = $r?->value; @endphp
                        @if(empty($v) && $v!=='0' && $v!==0) @continue @endif

                        @if($p->group_name && $p->group_name !== $currGrp)
                            @php $currGrp = $p->group_name; @endphp
                            <tr><td colspan="4" style="background:#f0fdfa;color:#0f766e;font-weight:700;">{{ $currGrp }}</td></tr>
                        @endif

                        @php
                            $f = $r?->flag ?? $p->checkFlag($v, $booking->patient->gender);
                            $badge = match($f) { 'high'=>'flag-high', 'low'=>'flag-low', 'critical_high'=>'flag-high', 'critical_low'=>'flag-low', default=>'flag-norm' };
                        @endphp
                        <tr>
                            <td>
                                <strong>{{ $p->name }}</strong>
                                @if($p->method)<div style="font-size:8px;color:#94a3b8;">{{ $p->method }}</div>@endif
                            </td>
                            <td>
                                @if($f=='normal') <span class="flag-norm">{{ $v }}</span>
                                @else <span class="flag-badge {{ $badge }}">{{ $v }} {{ strtoupper($f) }}</span> @endif
                            </td>
                            <td>{{ $p->getNormalRange($booking->patient->gender) }}</td>
                            <td>{{ $p->unit }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td><strong>{{ $bookingTest->test->name }}</strong></td>
                        <td>{{ $bookingTest->result?->value }}</td>
                        <td>{{ $bookingTest->test->normal_range }}</td>
                        <td>{{ $bookingTest->test->unit }}</td>
                    </tr>
                @endif
                </tbody>
            </table>

            @if($bookingTest->test->interpretation)
            <div style="border-left: 3px solid #0f766e; padding-left: 15px; margin-top: 20px;">
                <div style="font-weight:700; color:#0f766e; font-size:10px; text-transform:uppercase;">Clinical Interpretation</div>
                <div style="color:#475569; font-size:9px; margin-top:5px;">{!! $bookingTest->test->interpretation !!}</div>
            </div>
            @endif
        </div>
        @endforeach
    </div>

    <div class="footer">
        <div class="footer-content">
            <div class="sig-row">
                <div class="sig-cell">
                    @if($lab->signature_image && file_exists(storage_path('app/public/'.$lab->signature_image)))
                    <img src="data:image/png;base64,{{ base64_encode(file_get_contents(storage_path('app/public/'.$lab->signature_image))) }}" class="sig-img">
                    @endif
                    <div class="sig-name">{{ $lab->signature_name }}</div>
                    <div class="sig-role">{{ $lab->signature_designation }}</div>
                </div>
                <div class="sig-cell" style="text-align:right;">
                    @if($lab->signature_image_2 && file_exists(storage_path('app/public/'.$lab->signature_image_2)))
                    <img src="data:image/png;base64,{{ base64_encode(file_get_contents(storage_path('app/public/'.$lab->signature_image_2))) }}" class="sig-img" style="margin-left:auto;">
                    @endif
                    <div class="sig-name">{{ $lab->signature_name_2 }}</div>
                    <div class="sig-role">{{ $lab->signature_designation_2 }}</div>
                </div>
            </div>
        </div>
        <div class="footer-bar">
            <table class="fb-tbl">
                <tr>
                    <td class="fb-l">Report ID: {{ $booking->booking_id }}</td>
                    <td class="fb-r">Generated on {{ now()->format('d M Y, h:i A') }}</td>
                </tr>
            </table>
        </div>
    </div>
</div>
</body>
</html>
