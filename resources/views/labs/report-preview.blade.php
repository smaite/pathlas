<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Report Preview</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 10px; color: #333; padding: 20px; background: #fff; }
        
        .header { 
            border-bottom: 3px solid {{ $lab->header_color ?? '#0066cc' }}; 
            padding: 15px 0; 
            margin-bottom: 20px; 
        }
        .header-table { width: 100%; border-collapse: collapse; }
        .header-logo { width: 120px; vertical-align: middle; }
        .header-logo img { 
            max-width: {{ $lab->logo_width ?? 100 }}px; 
            max-height: {{ $lab->logo_height ?? 60 }}px; 
        }
        .header-center { vertical-align: middle; padding-left: 15px; }
        .header-contact { width: 180px; vertical-align: middle; text-align: right; }
        
        .lab-name { 
            font-size: 22px; 
            font-weight: bold; 
            color: {{ $lab->header_color ?? '#0066cc' }}; 
            margin-bottom: 2px; 
        }
        .lab-tagline { font-size: 11px; color: #666; margin-bottom: 4px; }
        .lab-address { font-size: 9px; color: #555; }
        .contact-item { font-size: 9px; margin-bottom: 3px; }
        
        .patient-section { 
            background: {{ $lab->header_color ?? '#0066cc' }}15; 
            border: 1px solid {{ $lab->header_color ?? '#0066cc' }}40; 
            padding: 12px; 
            margin-bottom: 20px; 
        }
        .patient-table { width: 100%; border-collapse: collapse; }
        .patient-label { font-size: 9px; color: #666; width: 80px; }
        .patient-value { font-size: 10px; color: #333; font-weight: 500; }
        
        .test-section { margin-bottom: 20px; }
        .test-category { 
            font-size: 11px; 
            color: {{ $lab->header_color ?? '#0066cc' }}; 
            font-weight: bold; 
            text-align: center; 
            padding: 8px 0; 
            border-bottom: 2px solid {{ $lab->header_color ?? '#0066cc' }}; 
            margin-bottom: 5px; 
        }
        .test-name { font-size: 14px; font-weight: bold; text-align: center; padding: 5px 0 10px; }
        
        .results-table { width: 100%; border-collapse: collapse; }
        .results-table th { 
            background: {{ $lab->header_color ?? '#0066cc' }}; 
            color: white; 
            padding: 8px 12px; 
            text-align: left; 
            font-size: 10px; 
        }
        .results-table td { padding: 6px 12px; border-bottom: 1px solid #e0e0e0; }
        .results-table tr:nth-child(even) { background: #f9f9f9; }
        
        .signatures { display: table; width: 100%; margin-top: 40px; }
        .signature-box { display: table-cell; width: 50%; text-align: center; vertical-align: bottom; }
        .sig-img { margin-bottom: 5px; }
        .sig-img img { 
            width: {{ $lab->signature_width ?? 100 }}px; 
            height: {{ $lab->signature_height ?? 35 }}px; 
            object-fit: contain;
        }
        .sig-line { border-bottom: 1px solid #333; width: 120px; margin: 0 auto 5px; height: 35px; }
        .sig-name { font-weight: bold; font-size: 10px; }
        .sig-designation { font-size: 9px; color: #666; }
        
        .report-notes { margin-top: 20px; padding: 10px; background: #f9f9f9; font-size: 9px; border-top: 1px dashed #ccc; }
        
        .qr-box { text-align: center; }
        .qr-box img { width: 50px; height: 50px; }
        .qr-label { font-size: 7px; color: #666; }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <table class="header-table">
            <tr>
                <td class="header-logo">
                    @if($lab->logo)
                    @php
                        $logoPath = storage_path('app/public/' . $lab->logo);
                        $logoBase64 = file_exists($logoPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath)) : null;
                    @endphp
                    @if($logoBase64)
                    <img src="{{ $logoBase64 }}" alt="Logo">
                    @endif
                    @else
                    <div style="width: {{ $lab->logo_width ?? 80 }}px; height: {{ $lab->logo_height ?? 50 }}px; background: {{ $lab->header_color ?? '#0066cc' }}; color: white; text-align: center; line-height: {{ $lab->logo_height ?? 50 }}px; font-size: 18px; font-weight: bold;">
                        {{ strtoupper(substr($lab->name ?? 'L', 0, 2)) }}
                    </div>
                    @endif
                </td>
                <td class="header-center">
                    <div class="lab-name">{{ $lab->name ?? 'Your Lab Name' }}</div>
                    @if($lab->tagline)
                    <div class="lab-tagline">{{ $lab->tagline }}</div>
                    @endif
                    <div class="lab-address">{{ $lab->address }}{{ $lab->city ? ', '.$lab->city : '' }}</div>
                </td>
                <td class="header-contact">
                    @if($lab->email)
                    <div class="contact-item"><strong>Email:</strong> {{ $lab->email }}</div>
                    @endif
                    @if($lab->phone)
                    <div class="contact-item"><strong>Phone:</strong> {{ $lab->phone }}</div>
                    @endif
                </td>
                <td class="qr-box" style="width: 70px;">
                    <div style="width: 50px; height: 50px; border: 1px solid #ddd; margin: 0 auto; display: flex; align-items: center; justify-content: center;">
                        <span style="font-size: 8px; color: #999;">QR</span>
                    </div>
                    <div class="qr-label">Scan for Report</div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Patient Section (Sample) -->
    <div class="patient-section">
        <table class="patient-table">
            <tr>
                <td style="width: 50%; vertical-align: top;">
                    <table>
                        <tr><td class="patient-label">Name</td><td class="patient-value">: Sample Patient</td></tr>
                        <tr><td class="patient-label">Age/Gender</td><td class="patient-value">: 32/M</td></tr>
                        <tr><td class="patient-label">Phone</td><td class="patient-value">: 9876543210</td></tr>
                    </table>
                </td>
                <td style="width: 50%; vertical-align: top;">
                    <table>
                        <tr><td class="patient-label">Patient ID</td><td class="patient-value">: P-000001</td></tr>
                        <tr><td class="patient-label">Reg No.</td><td class="patient-value">: BK-2025-0001</td></tr>
                        <tr><td class="patient-label">Referred By</td><td class="patient-value">: Dr. Sample</td></tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>

    <!-- Sample Test Results -->
    <div class="test-section">
        <div class="test-category">BIOCHEMISTRY</div>
        <div class="test-name">LIVER FUNCTION TEST (LFT)</div>
        
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
                <tr><td>Total Bilirubin</td><td style="color: #16a34a; font-weight: bold;">0.8</td><td>0.20 - 1.20</td><td>mg/dL</td></tr>
                <tr><td>Direct Bilirubin</td><td style="color: #16a34a; font-weight: bold;">0.2</td><td>0.00 - 0.30</td><td>mg/dL</td></tr>
                <tr><td>SGOT (AST)</td><td style="color: #16a34a; font-weight: bold;">28</td><td>0 - 40</td><td>U/L</td></tr>
                <tr><td>SGPT (ALT)</td><td style="color: #dc2626; font-weight: bold;">52</td><td>0 - 40</td><td>U/L</td></tr>
                <tr><td>Total Protein</td><td style="color: #16a34a; font-weight: bold;">7.2</td><td>6.00 - 8.30</td><td>g/dL</td></tr>
                <tr><td>Albumin</td><td style="color: #16a34a; font-weight: bold;">4.1</td><td>3.50 - 5.00</td><td>g/dL</td></tr>
            </tbody>
        </table>
    </div>

    <!-- Signatures -->
    <div class="signatures">
        <div class="signature-box">
            @if($lab->signature_image)
            @php
                $sigPath = storage_path('app/public/' . $lab->signature_image);
                $sigBase64 = file_exists($sigPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($sigPath)) : null;
            @endphp
            @if($sigBase64)
            <div class="sig-img"><img src="{{ $sigBase64 }}" style="width: {{ $lab->signature_width ?? 100 }}px; height: {{ $lab->signature_height ?? 35 }}px;" alt="Signature"></div>
            @endif
            @else
            <div class="sig-line"></div>
            @endif
            <div class="sig-name">{{ $lab->signature_name ?? 'Authorized Signatory' }}</div>
            <div class="sig-designation">{{ $lab->signature_designation ?? '' }}</div>
        </div>
        <div class="signature-box">
            @if($lab->signature_image_2)
            @php
                $sig2Path = storage_path('app/public/' . $lab->signature_image_2);
                $sig2Base64 = file_exists($sig2Path) ? 'data:image/png;base64,' . base64_encode(file_get_contents($sig2Path)) : null;
            @endphp
            @if($sig2Base64)
            <div class="sig-img"><img src="{{ $sig2Base64 }}" style="width: {{ $lab->signature_width_2 ?? 100 }}px; height: {{ $lab->signature_height_2 ?? 35 }}px;" alt="Signature"></div>
            @endif
            @else
            <div class="sig-line"></div>
            @endif
            <div class="sig-name">{{ $lab->signature_name_2 ?? 'Pathologist' }}</div>
            <div class="sig-designation">{{ $lab->signature_designation_2 ?? '' }}</div>
        </div>
    </div>

    <!-- Report Notes -->
    @if($lab->report_notes)
    <div class="report-notes">
        <strong>Notes:</strong> {{ $lab->report_notes }}
    </div>
    @endif
</body>
</html>
