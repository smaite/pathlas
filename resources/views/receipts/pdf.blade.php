<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Receipt - {{ $booking->booking_id }}</title>
    <style>
        @page { margin: 15mm 12mm; size: A4; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Arial, sans-serif; font-size: 11px; color: #1e293b; line-height: 1.4; }
        
        /* Modern Header */
        .header { display: table; width: 100%; margin-bottom: 20px; }
        .header-logo { display: table-cell; width: 80px; vertical-align: middle; }
        .header-logo img { max-width: 70px; max-height: 70px; border-radius: 8px; }
        .header-info { display: table-cell; vertical-align: middle; padding-left: 15px; }
        .lab-name { font-size: 22px; font-weight: 700; color: #0f172a; letter-spacing: -0.5px; }
        .lab-tagline { font-size: 10px; color: #64748b; margin-top: 2px; }
        .lab-contact { font-size: 10px; color: #475569; margin-top: 4px; }
        .header-qr { display: table-cell; width: 100px; vertical-align: middle; text-align: right; }
        .qr-container { display: inline-block; padding: 6px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; }
        .qr-container img { width: 75px; height: 75px; }
        .qr-label { font-size: 8px; color: #64748b; text-align: center; margin-top: 3px; }
        
        /* Receipt Badge */
        .receipt-badge { background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%); color: white; padding: 12px 20px; border-radius: 10px; margin-bottom: 20px; display: table; width: 100%; }
        .badge-left { display: table-cell; vertical-align: middle; }
        .badge-title { font-size: 11px; opacity: 0.9; text-transform: uppercase; letter-spacing: 1px; }
        .badge-id { font-size: 20px; font-weight: 700; font-family: 'Consolas', monospace; margin-top: 2px; }
        .badge-right { display: table-cell; text-align: right; vertical-align: middle; }
        .badge-date { font-size: 11px; opacity: 0.9; }
        .badge-sample { font-size: 14px; font-weight: 600; padding: 4px 12px; background: rgba(255,255,255,0.2); border-radius: 20px; display: inline-block; margin-top: 4px; }
        
        /* Patient Card */
        .patient-card { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 15px; margin-bottom: 20px; }
        .patient-grid { display: table; width: 100%; }
        .patient-col { display: table-cell; width: 50%; vertical-align: top; }
        .patient-row { margin-bottom: 8px; }
        .patient-label { font-size: 9px; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; }
        .patient-value { font-size: 12px; color: #0f172a; font-weight: 500; margin-top: 1px; }
        
        /* Tests Table */
        .section-title { font-size: 11px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 10px; padding-bottom: 8px; border-bottom: 2px solid #e2e8f0; }
        .tests-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .tests-table th { background: #0f172a; color: white; padding: 10px 12px; text-align: left; font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
        .tests-table th:first-child { border-radius: 6px 0 0 6px; width: 40px; text-align: center; }
        .tests-table th:last-child { border-radius: 0 6px 6px 0; text-align: right; width: 100px; }
        .tests-table td { padding: 12px; border-bottom: 1px solid #e2e8f0; }
        .tests-table td:first-child { text-align: center; font-weight: 500; color: #64748b; }
        .tests-table td:last-child { text-align: right; font-weight: 600; color: #0f172a; }
        .tests-table tr:last-child td { border-bottom: none; }
        .test-name { font-weight: 500; color: #1e293b; }
        
        /* Totals */
        .totals-section { background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%); border-radius: 10px; padding: 15px; margin-bottom: 25px; }
        .totals-row { display: table; width: 100%; padding: 6px 0; }
        .totals-label { display: table-cell; width: 70%; text-align: right; padding-right: 20px; font-size: 12px; color: #475569; }
        .totals-value { display: table-cell; text-align: right; font-size: 12px; color: #1e293b; font-weight: 500; }
        .totals-row.main { background: #0f172a; margin: 10px -15px -15px; padding: 12px 15px; border-radius: 0 0 10px 10px; }
        .totals-row.main .totals-label, .totals-row.main .totals-value { color: white; font-size: 14px; font-weight: 700; }
        .totals-row.due { color: #dc2626 !important; }
        .totals-row.due .totals-label, .totals-row.due .totals-value { color: #dc2626; font-weight: 600; }
        
        /* Signatures */
        .signatures { display: table; width: 100%; margin-top: 50px; }
        .sig-box { display: table-cell; width: 50%; text-align: center; }
        .sig-line { width: 150px; border-top: 1px dashed #94a3b8; margin: 0 auto 8px; }
        .sig-label { font-size: 10px; color: #64748b; }
        
        /* Footer */
        .footer { text-align: center; margin-top: 30px; padding-top: 15px; border-top: 1px solid #e2e8f0; }
        .footer-thanks { font-size: 13px; color: #0ea5e9; font-weight: 600; }
        .footer-note { font-size: 9px; color: #94a3b8; margin-top: 5px; }
        
        /* PAN */
        .pan-box { background: #fef3c7; border: 1px solid #fcd34d; border-radius: 6px; padding: 6px 12px; display: inline-block; font-size: 10px; color: #92400e; margin-top: 10px; }
    </style>
</head>
<body>
    @php
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
        $reportId = $booking->report?->report_id ?? $booking->booking_id;
        $qrUrl = url('/report-pdf/' . $reportId);
        
        $qrCode = new \Endroid\QrCode\QrCode($qrUrl);
        $qrCode->setSize(150);
        $qrCode->setMargin(5);
        $writer = new \Endroid\QrCode\Writer\PngWriter();
        $result = $writer->write($qrCode);
        $qrBase64 = 'data:image/png;base64,' . base64_encode($result->getString());
    @endphp

    <!-- Header -->
    <div class="header">
        <div class="header-logo">
            @if($logoBase64)
            <img src="{{ $logoBase64 }}" alt="Logo">
            @endif
        </div>
        <div class="header-info">
            <div class="lab-name">{{ $lab->name }}</div>
            <div class="lab-tagline">Pathology & Diagnostic Center</div>
            <div class="lab-contact">{{ $lab->address }}{{ $lab->city ? ', '.$lab->city : '' }} | {{ $lab->phone }}</div>
            @if($lab->pan_number)
            <div class="pan-box">PAN: {{ $lab->pan_number }}</div>
            @endif
        </div>
        <div class="header-qr">
            <div class="qr-container">
                <img src="{{ $qrBase64 }}" alt="QR">
            </div>
            <div class="qr-label">Scan for Report</div>
        </div>
    </div>

    <!-- Receipt Badge -->
    <div class="receipt-badge">
        <div class="badge-left">
            <div class="badge-title">Receipt / Registration</div>
            <div class="badge-id">{{ $booking->booking_id }}</div>
        </div>
        <div class="badge-right">
            <div class="badge-date">{{ $booking->created_at->format('d M Y, h:i A') }}</div>
            <div class="badge-sample">Sample ID: {{ $booking->bookingTests->first()?->sample_id ?? 'L'.$booking->id }}</div>
        </div>
    </div>

    <!-- Patient Info -->
    <div class="patient-card">
        <div class="patient-grid">
            <div class="patient-col">
                <div class="patient-row">
                    <div class="patient-label">Patient Name</div>
                    <div class="patient-value">{{ $booking->patient->name ?? 'Walk-in Patient' }}</div>
                </div>
                <div class="patient-row">
                    <div class="patient-label">Age / Gender</div>
                    <div class="patient-value">{{ $booking->patient->age ?? '-' }} Years / {{ ucfirst($booking->patient->gender ?? 'Other') }}</div>
                </div>
                <div class="patient-row">
                    <div class="patient-label">Mobile</div>
                    <div class="patient-value">{{ $booking->patient->phone ?? '-' }}</div>
                </div>
            </div>
            <div class="patient-col">
                <div class="patient-row">
                    <div class="patient-label">Referred By</div>
                    <div class="patient-value">{{ $booking->referring_doctor ?? 'Self' }}</div>
                </div>
                <div class="patient-row">
                    <div class="patient-label">Received By</div>
                    <div class="patient-value">{{ $booking->received_by ?? 'Staff' }}</div>
                </div>
                <div class="patient-row">
                    <div class="patient-label">Reg. No</div>
                    <div class="patient-value">#{{ $booking->id }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tests -->
    <div class="section-title">Investigations</div>
    <table class="tests-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Test Name</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($booking->bookingTests as $index => $bt)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td><span class="test-name">{{ $bt->test->name }}</span></td>
                <td>Rs. {{ number_format($bt->price, 0) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Totals -->
    <div class="totals-section">
        <div class="totals-row">
            <div class="totals-label">Subtotal</div>
            <div class="totals-value">Rs. {{ number_format($booking->subtotal, 0) }}</div>
        </div>
        @if($booking->discount > 0)
        <div class="totals-row">
            <div class="totals-label">Discount</div>
            <div class="totals-value">- Rs. {{ number_format($booking->discount, 0) }}</div>
        </div>
        @endif
        <div class="totals-row">
            <div class="totals-label">Total Amount</div>
            <div class="totals-value">Rs. {{ number_format($booking->total_amount, 0) }}</div>
        </div>
        <div class="totals-row">
            <div class="totals-label">Amount Paid</div>
            <div class="totals-value">Rs. {{ number_format($booking->payments->sum('amount'), 0) }}</div>
        </div>
        @php $due = $booking->total_amount - $booking->payments->sum('amount'); @endphp
        @if($due > 0)
        <div class="totals-row due">
            <div class="totals-label">Balance Due</div>
            <div class="totals-value">Rs. {{ number_format($due, 0) }}</div>
        </div>
        @endif
        <div class="totals-row main">
            <div class="totals-label">Net Payable</div>
            <div class="totals-value">Rs. {{ number_format($booking->total_amount, 0) }}</div>
        </div>
    </div>

    <!-- Signatures -->
    <div class="signatures">
        <div class="sig-box">
            <div class="sig-line"></div>
            <div class="sig-label">Authorized Signature</div>
        </div>
        <div class="sig-box">
            <div class="sig-line"></div>
            <div class="sig-label">Cashier</div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <div class="footer-thanks">Thank You for Choosing Us!</div>
        <div class="footer-note">This is a computer generated receipt. Please keep for your records.</div>
    </div>
</body>
</html>
