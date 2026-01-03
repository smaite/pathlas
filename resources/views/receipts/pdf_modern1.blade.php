<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Receipt - {{ $booking->booking_id }}</title>
    <style>
        @page { margin: 0; size: A4; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 11px; color: #374151; line-height: 1.5; background: #fff; }

        .container { padding: 40px; }

        /* Header */
        .header { margin-bottom: 40px; }
        .brand-section { display: table; width: 100%; border-bottom: 2px solid #111827; padding-bottom: 20px; }
        .logo-col { display: table-cell; width: 60px; vertical-align: top; }
        .info-col { display: table-cell; padding-left: 20px; vertical-align: top; }
        .meta-col { display: table-cell; width: 150px; text-align: right; vertical-align: top; }

        .lab-title { font-size: 24px; font-weight: 800; color: #111827; text-transform: uppercase; letter-spacing: -0.5px; }
        .lab-sub { font-size: 10px; color: #6b7280; font-weight: 500; text-transform: uppercase; letter-spacing: 1px; margin-top: 2px; }
        .lab-contact { margin-top: 10px; color: #4b5563; font-size: 10px; }

        .receipt-tag { font-size: 28px; font-weight: 300; color: #d1d5db; text-transform: uppercase; letter-spacing: 2px; }

        /* Invoice Meta */
        .inv-meta { margin-top: 30px; display: table; width: 100%; }
        .inv-left { display: table-cell; width: 50%; vertical-align: top; }
        .inv-right { display: table-cell; width: 50%; text-align: right; vertical-align: top; }

        .meta-group { margin-bottom: 15px; }
        .meta-label { font-size: 9px; color: #9ca3af; text-transform: uppercase; font-weight: 700; margin-bottom: 3px; }
        .meta-value { font-size: 12px; font-weight: 600; color: #111827; }

        /* Table */
        .items-table { width: 100%; border-collapse: collapse; margin-top: 40px; }
        .items-table th { text-align: left; padding: 12px 0; border-bottom: 2px solid #e5e7eb; font-size: 10px; font-weight: 700; text-transform: uppercase; color: #6b7280; }
        .items-table td { padding: 15px 0; border-bottom: 1px solid #f3f4f6; }
        .items-table tr:last-child td { border-bottom: none; }

        .item-name { font-weight: 600; color: #111827; font-size: 12px; }
        .item-code { font-size: 10px; color: #9ca3af; }

        /* Summary */
        .summary-section { margin-top: 30px; border-top: 2px solid #111827; padding-top: 20px; display: table; width: 100%; }
        .notes-col { display: table-cell; width: 60%; vertical-align: top; padding-right: 40px; }
        .totals-col { display: table-cell; width: 40%; vertical-align: top; }

        .total-row { display: table; width: 100%; margin-bottom: 8px; }
        .t-label { display: table-cell; text-align: left; color: #6b7280; font-size: 11px; }
        .t-value { display: table-cell; text-align: right; font-weight: 600; color: #374151; font-size: 11px; }

        .grand-total { margin-top: 15px; padding-top: 15px; border-top: 1px dashed #d1d5db; font-size: 16px; color: #111827; font-weight: 800; }
        .grand-total .t-label { color: #111827; font-size: 14px; }
        .grand-total .t-value { font-size: 18px; }

        .payment-status {
            display: inline-block; padding: 6px 12px; border-radius: 4px; font-size: 10px; font-weight: 700; text-transform: uppercase; margin-top: 10px;
            background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0;
        }
        .status-unpaid { background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; }
        .status-partial { background: #fffbeb; color: #b45309; border: 1px solid #fde68a; }

        /* Footer */
        .footer { position: absolute; bottom: 40px; left: 40px; right: 40px; text-align: center; color: #9ca3af; font-size: 9px; }
    </style>
</head>
<body>
    @php
    $logoBase64 = null;
    if ($lab->logo) {
        $path = storage_path('app/public/' . $lab->logo);
        if (file_exists($path)) {
            $logoBase64 = 'data:' . mime_content_type($path) . ';base64,' . base64_encode(file_get_contents($path));
        }
    }
    @endphp

    <div class="container">
        <div class="header">
            <div class="brand-section">
                <div class="logo-col">
                    @if($logoBase64)
                    <img src="{{ $logoBase64 }}" style="width: 50px; height: 50px; border-radius: 6px;">
                    @else
                    <div style="width:50px;height:50px;background:#111827;color:#fff;line-height:50px;text-align:center;font-weight:bold;border-radius:6px;">{{ substr($lab->name,0,1) }}</div>
                    @endif
                </div>
                <div class="info-col">
                    <div class="lab-title">{{ $lab->name }}</div>
                    <div class="lab-sub">Official Receipt</div>
                    <div class="lab-contact">{{ $lab->address }} | {{ $lab->phone }}</div>
                </div>
                <div class="meta-col">
                    <div class="receipt-tag">RECEIPT</div>
                </div>
            </div>

            <div class="inv-meta">
                <div class="inv-left">
                    <div class="meta-group">
                        <div class="meta-label">Bill To</div>
                        <div class="meta-value">{{ $booking->patient->name }}</div>
                        <div style="font-size:10px;color:#6b7280;margin-top:2px;">
                            {{ $booking->patient->age }} Y / {{ ucfirst($booking->patient->gender) }}<br>
                            {{ $booking->patient->phone ?? 'No Contact' }}
                        </div>
                    </div>
                </div>
                <div class="inv-right">
                    <div class="meta-group">
                        <div class="meta-label">Receipt Number</div>
                        <div class="meta-value">#{{ $booking->booking_id }}</div>
                    </div>
                    <div class="meta-group">
                        <div class="meta-label">Date Issued</div>
                        <div class="meta-value">{{ $booking->created_at->format('d M, Y') }}</div>
                    </div>
                    <div class="meta-group">
                        <div class="meta-label">Payment Status</div>
                        @php
                            $paid = $booking->payments->sum('amount');
                            $due = $booking->total_amount - $paid;
                            $status = $due <= 0 ? 'PAID' : ($paid > 0 ? 'PARTIAL' : 'UNPAID');
                            $sClass = match($status) { 'PAID'=>'', 'PARTIAL'=>'status-partial', 'UNPAID'=>'status-unpaid' };
                        @endphp
                        <div class="payment-status {{ $sClass }}">{{ $status }}</div>
                    </div>
                </div>
            </div>
        </div>

        <table class="items-table">
            <thead>
                <tr>
                    <th width="5%">#</th>
                    <th width="75%">Description</th>
                    <th width="20%" style="text-align:right;">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($booking->bookingTests as $i => $test)
                <tr>
                    <td style="color:#9ca3af;">{{ $i + 1 }}</td>
                    <td>
                        <div class="item-name">{{ $test->test->name }}</div>
                        <div class="item-code">{{ $test->test->code }}</div>
                    </td>
                    <td style="text-align:right; font-weight:600;">{{ number_format($test->price, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="summary-section">
            <div class="notes-col">
                @if($booking->notes)
                <div style="margin-bottom: 20px;">
                    <div class="meta-label">Notes</div>
                    <div style="font-size:10px; color:#4b5563;">{{ $booking->notes }}</div>
                </div>
                @endif
                <div style="font-size: 10px; color: #9ca3af;">
                    Thank you for choosing {{ $lab->name }}. This receipt is computer generated and valid without signature.
                </div>
                @if($lab->pan_number)
                <div style="margin-top: 10px; font-size: 10px; font-weight: 600;">PAN: {{ $lab->pan_number }}</div>
                @endif
            </div>
            <div class="totals-col">
                <div class="total-row">
                    <div class="t-label">Subtotal</div>
                    <div class="t-value">{{ number_format($booking->subtotal, 2) }}</div>
                </div>
                @if($booking->discount > 0)
                <div class="total-row">
                    <div class="t-label">Discount</div>
                    <div class="t-value" style="color:#dc2626;">-{{ number_format($booking->discount, 2) }}</div>
                </div>
                @endif
                <div class="total-row grand-total">
                    <div class="t-label">Total</div>
                    <div class="t-value">{{ number_format($booking->total_amount, 2) }}</div>
                </div>
                <div class="total-row" style="margin-top: 10px;">
                    <div class="t-label">Paid</div>
                    <div class="t-value" style="color:#059669;">{{ number_format($paid, 2) }}</div>
                </div>
                @if($due > 0)
                <div class="total-row">
                    <div class="t-label">Balance Due</div>
                    <div class="t-value" style="color:#dc2626;">{{ number_format($due, 2) }}</div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="footer">
        {{ $lab->address }} • {{ $lab->email }} • {{ $lab->website }}
    </div>
</body>
</html>