<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Invoice {{ $booking->booking_id }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; color: #333; }
        .header { display: flex; justify-content: space-between; margin-bottom: 30px; }
        .logo { font-size: 24px; font-weight: bold; color: #4F46E5; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #eee; }
        th { background: #f9f9f9; }
        .total-row { font-weight: bold; font-size: 14px; }
        .text-right { text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <div>
            <div class="logo">PathLAS</div>
            <p>Pathology Lab Automation System</p>
        </div>
        <div class="text-right">
            <h2>INVOICE</h2>
            <p>{{ $booking->booking_id }}</p>
            <p>{{ $booking->created_at->format('M d, Y') }}</p>
        </div>
    </div>

    <table style="margin-bottom: 30px;">
        <tr>
            <td><strong>Patient:</strong> {{ $booking->patient->name }}</td>
            <td class="text-right"><strong>ID:</strong> {{ $booking->patient->patient_id }}</td>
        </tr>
        <tr>
            <td><strong>Phone:</strong> {{ $booking->patient->phone }}</td>
            <td class="text-right"><strong>Age/Gender:</strong> {{ $booking->patient->age }} / {{ ucfirst($booking->patient->gender ?? '') }}</td>
        </tr>
    </table>

    <table>
        <thead>
            <tr><th>Test Name</th><th class="text-right">Price</th></tr>
        </thead>
        <tbody>
            @foreach($booking->bookingTests as $bt)
            <tr><td>{{ $bt->test->name }}</td><td class="text-right">₹{{ number_format($bt->price, 2) }}</td></tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr><td class="text-right">Subtotal</td><td class="text-right">₹{{ number_format($booking->subtotal, 2) }}</td></tr>
            <tr><td class="text-right">Discount</td><td class="text-right">-₹{{ number_format($booking->discount, 2) }}</td></tr>
            <tr class="total-row"><td class="text-right">Total</td><td class="text-right">₹{{ number_format($booking->total_amount, 2) }}</td></tr>
        </tfoot>
    </table>

    <p style="margin-top: 30px; text-align: center; color: #666;">Thank you for choosing PathLAS!</p>
</body>
</html>
