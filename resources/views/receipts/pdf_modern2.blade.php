<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Receipt - {{ $booking->booking_id }}</title>
    <style>
        @page { margin: 0; size: A4; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 11px; color: #374151; line-height: 1.5; background: #fff; }

        /* Colors */
        :root {
            --primary: #0e7490; /* Cyan 700 */
            --primary-light: #ecfeff; /* Cyan 50 */
            --accent: #155e75; /* Cyan 800 */
            --text-light: #f3f4f6;
        }

        .header-bg {
            background-color: #0e7490;
            color: white;
            padding: 40px;
            height: 180px;
        }

        .container { padding: 40px; margin-top: -100px; }

        /* Header Content */
        .brand-table { width: 100%; margin-bottom: 20px; }
        .brand-name { font-size: 26px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; color: white; }
        .brand-sub { font-size: 11px; color: #cffafe; margin-top: 4px; }

        .receipt-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            padding: 30px;
            margin-bottom: 30px;
            border: 1px solid #e5e7eb;
        }

        .info-grid { display: table; width: 100%; margin-bottom: 30px; }
        .info-col { display: table-cell; width: 50%; vertical-align: top; }

        .label { font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px; color: #6b7280; font-weight: 600; margin-bottom: 4px; }
        .value { font-size: 13px; font-weight: 600; color: #111827; margin-bottom: 15px; }

        .receipt-badge {
            background: #ecfeff;
            color: #0e7490;
            padding: 8px 16px;
            border-radius: 6px;
            font-weight: 700;
            font-size: 14px;
            display: inline-block;
            border: 1px solid #cffafe;
        }

        /* Table */
        .table-container { margin-top: 20px; }
        table { width: 100%; border-collapse: separate; border-spacing: 0; }
        th {
            background: #f3f4f6;
            color: #4b5563;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 10px;
            padding: 12px 15px;
            text-align: left;
            border-top: 1px solid #e5e7eb;
            border-bottom: 1px solid #e5e7eb;
        }
        th:first-child { border-top-left-radius: 8px; border-left: 1px solid #e5e7eb; }
        th:last-child { border-top-right-radius: 8px; border-right: 1px solid #e5e7eb; text-align: right; }

        td { padding: 12px 15px; border-bottom: 1px solid #f3f4f6; font-size: 11px; color: #374151; }
        td:first-child { border-left: 1px solid #f3f4f6; }
        td:last-child { border-right: 1px solid #f3f4f6; text-align: right; }
        tr:last-child td { border-bottom: 1px solid #e5e7eb; }
        tr:last-child td:first-child { border-bottom-left-radius: 8px; }
        tr:last-child td:last-child { border-bottom-right-radius: 8px; }

        .item-name { font-weight: 600; color: #111827; }
        .item-code { font-size: 10px; color: #6b7280; }

        /* Totals */
        .totals-section { display: table; width: 100%; margin-top: 20px; }
        .notes-area { display: table-cell; width: 60%; padding-right: 40px; vertical-align: top; }
        .totals-area { display: table-cell; width: 40%; vertical-align: top; }

        .total-row { display: table; width: 100%; padding: 6px 0; }
        .t-label { display: table-cell; color: #6b7280; font-size: 11px; }
        .t-val { display: table-cell; text-align: right; font-weight: 600; color: #111827; }

        .grand-total {
            background: #0e7490;
            color: white;
            padding: 12px;
            border-radius: 8px;
            margin-top: 10px;
        }
        .grand-total .t-label { color: #cffafe; }
        .grand-total .t-val { color: white; font-size: 16px; font-weight: 700; }

        .status-badge {
            display: inline-block; padding: 4px 10px; border-radius: 20px; font-size: 10px; font-weight: 700; text-transform: uppercase;
        }
        .st-paid { background: #dcfce7; color: #15803d; }
        .st-unpaid { background: #fee2e2; color: #b91c1c; }
        .st-partial { background: #fef3c7; color: #b45309; }

        .footer {
            margin-top: 60px;
            text-align: center;
            font-size: 10px;
            color: #9ca3af;
            border-top: 1px solid #f3f4f6;
            padding-top: 20px;
        }
    </style>
</head>
<body>
    @php
    // Logic for paid/due
    $paid = $booking->payments->sum('amount');
    $due = $booking->total_amount - $paid;
    $status = $due <= 0 ? 'PAID' : ($paid > 0 ? 'PARTIAL' : 'UNPAID');
    $stClass = match($status) { 'PAID'=>'st-paid', 'PARTIAL'=>'st-partial', 'UNPAID'=>'st-unpaid' };

    // Logo
    $logoBase64 = null;
    if ($lab->logo) {
        $path = storage_path('app/public/' . $lab->logo);
        if (file_exists($path)) {
            $logoBase64 = 'data:' . mime_content_type($path) . ';base64,' . base64_encode(file_get_contents($path));
        }
    }
    @endphp

    <div class="header-bg">
        <table class="brand-table">
            <tr>
                <td style="width: 70%; vertical-align: top; border: none; padding: 0;">
                    <div class="brand-name">{{ $lab->name }}</div>
                    <div class="brand-sub">{{ $lab->address }}</div>
                    <div class="brand-sub">{{ $lab->phone }} • {{ $lab->email }}</div>
                </td>
                <td style="width: 30%; text-align: right; vertical-align: top; border: none; padding: 0;">
                     @if($logoBase64)
                    <img src="{{ $logoBase64 }}" style="max-height: 60px; background: white; padding: 4px; border-radius: 6px;">
                    @else
                    <div style="display:inline-block; width:50px; height:50px; background:rgba(255,255,255,0.2); color:white; line-height:50px; text-align:center; font-weight:bold; border-radius:6px; font-size:20px;">
                        {{ substr($lab->name,0,1) }}
                    </div>
                    @endif
                </td>
            </tr>
        </table>
    </div>

    <div class="container">
        <div class="receipt-card">
            <div style="border-bottom: 1px solid #f3f4f6; padding-bottom: 20px; margin-bottom: 20px; display: table; width: 100%;">
                <div style="display: table-cell;">
                    <span class="receipt-badge">RECEIPT #{{ $booking->booking_id }}</span>
                </div>
                <div style="display: table-cell; text-align: right;">
                    <span class="status-badge {{ $stClass }}">{{ $status }}</span>
                </div>
            </div>

            <div class="info-grid">
                <div class="info-col">
                    <div class="label">Billed To</div>
                    <div class="value">
                        {{ $booking->patient->name }}<br>
                        <span style="font-weight: 400; font-size: 11px; color: #4b5563;">
                            {{ $booking->patient->age }} Y / {{ ucfirst($booking->patient->gender) }}<br>
                            {{ $booking->patient->phone ?? '' }}
                        </span>
                    </div>
                    <div class="label">Referred By</div>
                    <div class="value">{{ $booking->referring_doctor_name ?? 'Self' }}</div>
                </div>
                <div class="info-col" style="text-align: right;">
                    <div class="label">Date Issued</div>
                    <div class="value">{{ $booking->created_at->format('d M, Y h:i A') }}</div>

                    <div class="label">Collection Date</div>
                    <div class="value">{{ $booking->collection_date ? $booking->collection_date->format('d M, Y') : '-' }}</div>
                </div>
            </div>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th width="5%">#</th>
                            <th width="70%">Description</th>
                            <th width="25%">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($booking->bookingTests as $i => $test)
                        <tr>
                            <td style="color: #9ca3af;">{{ $i + 1 }}</td>
                            <td>
                                <div class="item-name">{{ $test->test->name }}</div>
                                <div class="item-code">{{ $test->test->code }}</div>
                            </td>
                            <td style="font-weight: 600;">{{ number_format($test->price, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="totals-section">
                <div class="notes-area">
                    @if($booking->notes)
                    <div class="label">Notes</div>
                    <div style="font-size: 11px; color: #4b5563; background: #f9fafb; padding: 10px; border-radius: 6px;">{{ $booking->notes }}</div>
                    @endif
                </div>
                <div class="totals-area">
                    <div class="total-row">
                        <div class="t-label">Subtotal</div>
                        <div class="t-val">{{ number_format($booking->subtotal, 2) }}</div>
                    </div>
                    @if($booking->discount > 0)
                    <div class="total-row">
                        <div class="t-label">Discount</div>
                        <div class="t-val" style="color: #dc2626;">-{{ number_format($booking->discount, 2) }}</div>
                    </div>
                    @endif
                    <div class="total-row grand-total">
                        <div class="t-label">Total Amount</div>
                        <div class="t-val">{{ number_format($booking->total_amount, 2) }}</div>
                    </div>
                    <div class="total-row" style="margin-top: 10px;">
                        <div class="t-label">Paid Amount</div>
                        <div class="t-val" style="color: #15803d;">{{ number_format($paid, 2) }}</div>
                    </div>
                    @if($due > 0)
                    <div class="total-row">
                        <div class="t-label">Balance Due</div>
                        <div class="t-val" style="color: #dc2626;">{{ number_format($due, 2) }}</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="footer">
            <p>Thank you for choosing {{ $lab->name }}.</p>
            <p style="margin-top: 4px;">This is a computer generated receipt and does not require a physical signature.</p>
        </div>
    </div>
</body>
</html>