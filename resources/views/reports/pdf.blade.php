<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Lab Report - {{ $booking->booking_id }}</title>
    <style>
        /* Conditional margins: if no header, add space for pre-printed paper */
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
                margin-top: {{ $marginTop }}mm;
                margin-bottom: {{ $marginBottom }}mm;
                margin-left: 15mm;
                margin-right: 15mm;
                size: A4;
            }
        @endif
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html, body { height: 100%; }
        body { font-family: Arial, sans-serif; font-size: 10px; color: #333; line-height: 1.3; position: relative; }

        /* Container for continuous flow */
        .report-container {
            padding: {{ ($showHeader ?? true) ? '0' : '0' }};
        }

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

        /* Test Title */
        .test-section { margin: 0 25px 20px 25px; page-break-inside: avoid; }
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
        .interpretation { margin: 10px 0; padding: 10px 15px; background: #fffbeb; border-left: 4px solid #f59e0b; font-size: 10px; }
        .interpretation-title { font-weight: bold; color: #b45309; margin-bottom: 5px; }

        /* Instruments Section */
        .instruments { margin: 10px 25px; font-size: 9px; color: #666; padding: 8px 0; border-top: 1px dashed #ddd; page-break-inside: avoid; }

        /* Footer */
        .footer {
            width: 100%;
            position: absolute;
            bottom: 0;
            left: 0;
            page-break-inside: avoid;
        }
        .footer-top { padding: 10px 25px; border-top: 1px solid #ddd; }
        .end-report { text-align: center; font-size: 9px; color: #666; padding: 5px; margin-bottom: 10px; }

        .signatures { display: table; width: 100%; }
        .signature-box { display: table-cell; width: 33%; text-align: center; vertical-align: bottom; }
        .signature-line { border-top: 1px solid #333; width: 80%; margin: 0 auto 5px; }
        .signature-name { font-weight: bold; font-size: 10px; color: #333; }
        .signature-title { font-size: 9px; color: #666; }

        .footer-bar { background: {{ $lab->header_color ?? '#0066cc' }}; color: white; padding: 8px 25px; font-size: 9px; width: 100%; }

        /* QR Code */
        .qr-section { text-align: right; }
        .qr-code { width: 60px; height: 60px; }

        /* Page numbering via CSS for DOMPDF */
        .page-number:before {
            content: counter(page);
        }
    </style>
</head>
<body>
@php
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

    // Generate QR code using endroid/qr-code
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

    $validTests = $booking->bookingTests->filter(function($bt) {
        if ($bt->test->hasParameters() && $bt->parameterResults->where('value', '!=', null)->count() > 0) return true;
        if ($bt->result && $bt->result->value && $bt->result->status === 'approved') return true;
        return false;
    });
@endphp

<div class="report-container">
    <!-- Header (conditional based on showHeader) -->
    @if($showHeader ?? true)
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
                <td style="width: 80px; vertical-align: middle; text-align: right;">
                    @if($qrBase64)
                    <div style="text-align: center;">
                        <img src="{{ $qrBase64 }}" style="width: 60px; height: 60px;" alt="QR">
                        <div style="font-size: 7px; color: #666; margin-top: 2px;">Scan for Report</div>
                    </div>
                    @endif
                </td>
            </tr>
        </table>
    </div>
    @endif

    <!-- Patient Section (Only once at top) -->
    <div class="patient-section">
        <table class="patient-table">
            <tr>
                <td class="patient-col">
                    <table style="width: 100%">
                        <tr><td class="patient-label">Name</td><td class="patient-value">:</td><td class="patient-value">{{ $booking->patient->name }}</td></tr>
                        <tr><td class="patient-label">Age/Gender</td><td class="patient-value">:</td><td class="patient-value">{{ $booking->patient->age }}/{{ ucfirst(substr($booking->patient->gender ?? '', 0, 1)) }}</td></tr>
                        <tr><td class="patient-label">Referred By</td><td class="patient-value">:</td><td class="patient-value">{{ $booking->referring_doctor_name ?? 'Self' }}</td></tr>
                        @if($booking->patient->phone)
                        <tr><td class="patient-label">Phone No.</td><td class="patient-value">:</td><td class="patient-value">{{ $booking->patient->phone }}</td></tr>
                        @endif
                </table>
                </td>
                <td class="patient-col">
                    <table style="width: 100%">
                        <tr><td class="patient-label">Patient ID</td><td class="patient-value">:</td><td class="patient-value">{{ $booking->patient->patient_id }}</td></tr>
                        <tr><td class="patient-label">Reg No.</td><td class="patient-value">:</td><td class="patient-value">{{ $booking->booking_id }}</td></tr>
                        <tr><td class="patient-label">Collection Date</td><td class="patient-value">:</td><td class="patient-value">{{ $booking->collection_date ? $booking->collection_date->format('d/m/Y') : now()->format('d/m/Y') }}</td></tr>
                        <tr><td class="patient-label">Report Date</td><td class="patient-value">:</td><td class="patient-value">{{ now()->format('d/m/Y') }}</td></tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>

    <!-- Tests Loop -->
    @foreach($validTests as $bookingTest)
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
                    @php
                        $paramResult = $paramResults->get($param->id);
                        $value = $paramResult?->value;
                    @endphp
                    @if(empty($value) && $value !== '0' && $value !== 0)
                        @continue
                    @endif
                    @if($param->group_name && $param->group_name !== $currentGroup)
                        @php $currentGroup = $param->group_name; @endphp
                        <tr class="group-row">
                            <td colspan="4">{{ $currentGroup }}</td>
                        </tr>
                    @endif
                    @php
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
                        <td class="{{ $flagClass }}">{{ $value }}</td>
                        <td class="ref-range">{{ $param->getNormalRange($booking->patient->gender) }}</td>
                        <td>{{ $param->unit }}</td>
                    </tr>
                    @if($param->interpretation)
                    <tr>
                        <td colspan="4" style="padding: 4px 12px 8px; background: #fffbeb; border-left: 3px solid #f59e0b; font-size: 9px; color: #666;">
                            <em>{{ $param->interpretation }}</em>
                        </td>
                    </tr>
                    @endif
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

        {{-- Clinical Notes / Interpretation (from test) --}}
        @if($bookingTest->test->interpretation)
        <div style="margin: 15px 0; padding: 12px 15px; background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 4px;">
            <div style="font-weight: bold; color: #333; margin-bottom: 8px; font-size: 11px; border-bottom: 1px solid #ccc; padding-bottom: 5px;">Clinical Notes</div>
            <div style="font-size: 10px; color: #444; line-height: 1.5;">{!! $bookingTest->test->interpretation !!}</div>
        </div>
        @endif

        {{-- Custom interpretation from result entry --}}
        @if($bookingTest->result?->notes)
        <div class="interpretation">
            <div class="interpretation-title">Interpretation:</div>
            {{ $bookingTest->result->notes }}
        </div>
        @endif
    </div>
    @endforeach

    <!-- Footer Section (Only at end) -->
    @if($lab->report_notes)
    <div class="instruments">
        <strong>Notes:</strong> {{ $lab->report_notes }}
    </div>
    @endif

    <div class="footer">
        <div class="footer-top">
            <div class="end-report">****End of Report****</div>

            <div class="signatures">
                <div class="signature-box">
                    @if($lab->signature_image)
                    @php
                        $sigPath = storage_path('app/public/' . $lab->signature_image);
                        $sigBase64 = file_exists($sigPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($sigPath)) : null;
                    @endphp
                    @if($sigBase64)
                    <div style="margin-bottom: 5px;"><img src="{{ $sigBase64 }}" style="height: 35px;" alt="Signature"></div>
                    @endif
                    @else
                    <div class="signature-line"></div>
                    @endif
                    <div class="signature-name">{{ $lab->signature_name ?? 'Authorized Signatory' }}</div>
                    <div class="signature-title">{{ $lab->signature_designation ?? '' }}</div>
                </div>

                <div class="signature-box"></div> <!-- Spacer -->

                <div class="signature-box">
                    @if($lab->signature_image_2)
                    @php
                        $sig2Path = storage_path('app/public/' . $lab->signature_image_2);
                        $sig2Base64 = file_exists($sig2Path) ? 'data:image/png;base64,' . base64_encode(file_get_contents($sig2Path)) : null;
                    @endphp
                    @if($sig2Base64)
                    <div style="margin-bottom: 5px;"><img src="{{ $sig2Base64 }}" style="height: 35px;" alt="Signature"></div>
                    @endif
                    @else
                    <div class="signature-line"></div>
                    @endif
                    <div class="signature-name">{{ $lab->signature_name_2 ?? 'Pathologist' }}</div>
                    <div class="signature-title">{{ $lab->signature_designation_2 ?? '' }}</div>
                </div>
            </div>

            <div style="text-align: center; font-size: 9px; color: #666; margin-top: 20px;">
                Report Generated on {{ now()->format('d M Y, h:i A') }}
            </div>
        </div>
    </div>
</div>

</body>
</html>
