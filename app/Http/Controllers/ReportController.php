<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\Booking;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $query = Report::with(['booking.patient', 'generatedBy'])->latest();

        if ($request->filled('date')) {
            $query->whereDate('generated_at', $request->date);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('report_id', 'like', "%{$search}%")
                  ->orWhereHas('booking', function($bq) use ($search) {
                      $bq->where('booking_id', 'like', "%{$search}%");
                  })
                  ->orWhereHas('booking.patient', function($pq) use ($search) {
                      $pq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $reports = $query->paginate(20);
        return view('reports.index', compact('reports'));
    }

    public function generate(Booking $booking)
    {
        // Check if all results are approved
        $unapproved = $booking->bookingTests()
            ->whereDoesntHave('result', function($q) {
                $q->where('status', 'approved');
            })
            ->count();

        if ($unapproved > 0) {
            return back()->with('error', 'All results must be approved before generating report.');
        }

        $booking->load([
            'patient',
            'lab',
            'bookingTests.test.category',
            'bookingTests.test.parameters',
            'bookingTests.result.approvedBy',
            'bookingTests.parameterResults',
            'createdBy'
        ]);

        // Get lab or use default
        $lab = $booking->lab ?? \App\Models\Lab::first();

        // Generate QR code (using SVG - no imagick needed)
        $qrContent = route('reports.verify', ['report' => $booking->report?->report_id ?? 'temp']);
        $qrCode = base64_encode(QrCode::format('svg')->size(100)->generate($qrContent));

        // Generate PDF
        $pdf = Pdf::loadView('reports.pdf', [
            'booking' => $booking,
            'lab' => $lab,
            'qrCode' => $qrCode,
        ]);

        // Save PDF
        $filename = "reports/report-{$booking->booking_id}-" . now()->timestamp . ".pdf";
        Storage::disk('public')->put($filename, $pdf->output());

        // Create or update report record
        $report = Report::updateOrCreate(
            ['booking_id' => $booking->id],
            [
                'pdf_path' => $filename,
                'qr_code' => $qrContent,
                'generated_by' => auth()->id(),
                'generated_at' => now(),
                'is_final' => true,
            ]
        );

        ActivityLog::log('report_generated', $report);

        return redirect()->route('reports.show', $report)
            ->with('success', 'Report generated successfully.');
    }

    public function show(Report $report)
    {
        $report->load([
            'booking.patient',
            'booking.bookingTests.test.category',
            'booking.bookingTests.result',
            'generatedBy'
        ]);

        return view('reports.show', compact('report'));
    }

    public function download(Report $report, Request $request)
    {
        // Check if custom generation is requested
        if ($request->has('header')) {
            return $this->downloadCustom($report, $request->get('header', 'yes'));
        }

        if (!$report->pdf_path || !Storage::disk('public')->exists($report->pdf_path)) {
            return back()->with('error', 'Report file not found.');
        }

        ActivityLog::log('report_downloaded', $report);

        return Storage::disk('public')->download($report->pdf_path, "report-{$report->report_id}.pdf");
    }

    /**
     * Download report with/without header/footer option
     */
    public function downloadCustom(Report $report, string $headerOption = 'yes')
    {
        $report->load([
            'booking.patient',
            'booking.lab',
            'booking.bookingTests.test.category',
            'booking.bookingTests.test.parameters',
            'booking.bookingTests.result.approvedBy',
            'booking.bookingTests.parameterResults',
            'booking.createdBy'
        ]);

        $booking = $report->booking;
        $lab = $booking->lab ?? auth()->user()->lab ?? \App\Models\Lab::first();

        // Generate QR code
        $qrCode = base64_encode(QrCode::format('svg')->size(100)->generate(route('reports.verify', $report->report_id)));

        $pdf = Pdf::loadView('reports.pdf', [
            'booking' => $booking,
            'lab' => $lab,
            'qrCode' => $qrCode,
            'showHeader' => $headerOption === 'yes',
        ]);

        ActivityLog::log('report_downloaded', $report);

        return $pdf->download("report-{$report->report_id}.pdf");
    }

    public function preview(Booking $booking)
    {
        $booking->load([
            'patient',
            'lab',
            'bookingTests.test.category',
            'bookingTests.test.parameters',
            'bookingTests.result.approvedBy',
            'bookingTests.parameterResults',
            'createdBy'
        ]);

        // Get lab or use default
        $lab = $booking->lab ?? auth()->user()->lab ?? \App\Models\Lab::first();

        // Generate QR code (using SVG - no imagick needed)
        $qrCode = base64_encode(QrCode::format('svg')->size(100)->generate(route('reports.index')));

        return view('reports.pdf', [
            'booking' => $booking,
            'lab' => $lab,
            'qrCode' => $qrCode,
            'preview' => true,
        ]);
    }

    public function verify(Request $request, string $reportId = null)
    {
        if (!$reportId) {
            $reportId = $request->get('id');
        }

        $report = Report::where('report_id', $reportId)->first();

        if (!$report) {
            return view('reports.verify', ['valid' => false]);
        }

        $report->load(['booking.patient', 'generatedBy']);

        return view('reports.verify', [
            'valid' => true,
            'report' => $report,
        ]);
    }

    /**
     * Public PDF download via QR code (no auth required)
     */
    public function publicDownload(string $reportId)
    {
        $report = Report::where('report_id', $reportId)->first();

        if (!$report) {
            abort(404, 'Report not found');
        }

        $report->load([
            'booking.patient',
            'booking.lab',
            'booking.bookingTests.test.category',
            'booking.bookingTests.test.parameters',
            'booking.bookingTests.parameterResults',
            'booking.bookingTests.result.approvedBy',
            'booking.createdBy'
        ]);
        
        $booking = $report->booking;
        
        // Get the lab - try booking's lab first, then fallback
        $lab = $booking->lab ?? Lab::first() ?? new Lab(['name' => 'PathLAS Lab']);
        
        // Generate QR code for verification
        $qrCode = base64_encode(QrCode::format('svg')->size(100)->generate(route('reports.verify', $report->report_id)));
        
        // Generate PDF using the existing pdf view
        $pdf = Pdf::loadView('reports.pdf', [
            'booking' => $booking,
            'lab' => $lab,
            'qrCode' => $qrCode,
            'showHeader' => true,
        ]);

        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream('report-' . $report->report_id . '.pdf');
    }

    public function regenerate(Report $report)
    {
        return $this->generate($report->booking);
    }
}

