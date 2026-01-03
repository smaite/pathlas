<?php

namespace App\Http\Controllers;

use App\Models\Result;
use App\Models\BookingTest;
use App\Models\ParameterResult;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ResultController extends Controller
{
    public function pending()
    {
        $user = auth()->user();
        $labId = $user->lab_id;
        
        // Show booking tests that don't have approved results yet
        $bookingTests = BookingTest::with(['test.category', 'booking.patient', 'result'])
            ->whereHas('booking', function($q) use ($labId, $user) {
                $q->where('status', '!=', 'cancelled');
                // Strict lab isolation for non-super-admin users
                if (!$user->isSuperAdmin()) {
                    $q->where('lab_id', $labId);
                }
            })
            ->where(function($q) {
                $q->whereDoesntHave('result')  // No result at all
                  ->orWhereHas('result', fn($r) => $r->where('status', '!=', 'approved'));  // OR has non-approved result
            })
            ->latest()
            ->paginate(20);

        return view('results.pending', compact('bookingTests'));
    }

    public function entry(Result $result)
    {
        $result->load(['bookingTest.test.parameters', 'bookingTest.booking.patient', 'bookingTest.parameterResults']);
        return view('results.entry', compact('result'));
    }

    // New: Show parameter entry form for tests with parameters
    public function parameters(BookingTest $bookingTest)
    {
        $bookingTest->load([
            'test.parameters', 
            'test.category',
            'booking.patient',
            'parameterResults',
            'result'
        ]);
        
        return view('results.parameters', compact('bookingTest'));
    }

    // New: Store parameter results
    public function storeParameters(Request $request, BookingTest $bookingTest)
    {
        $validated = $request->validate([
            'parameters' => 'array',
            'parameters.*.value' => 'nullable|string',
            'parameters.*.numeric_value' => 'nullable|numeric',
            'remarks' => 'nullable|string',
            // For simple tests
            'value' => 'nullable|string',
            'numeric_value' => 'nullable|numeric',
        ]);

        $bookingTest->load('test.parameters', 'booking.patient');
        $gender = $bookingTest->booking->patient->gender;

        if ($bookingTest->test->hasParameters()) {
            // Handle parameter-based results
            foreach ($validated['parameters'] ?? [] as $paramId => $data) {
                if (empty($data['value'])) continue;

                $param = $bookingTest->test->parameters->find($paramId);
                if (!$param) continue;

                $numericValue = is_numeric($data['value']) ? (float)$data['value'] : null;
                $flag = $numericValue !== null ? $param->checkFlag($numericValue, $gender) : null;

                ParameterResult::updateOrCreate(
                    [
                        'booking_test_id' => $bookingTest->id,
                        'test_parameter_id' => $paramId,
                    ],
                    [
                        'value' => $data['value'],
                        'numeric_value' => $numericValue,
                        'flag' => $flag,
                    ]
                );
            }
        }

        // Update or create the main result entry
        $result = $bookingTest->result ?? Result::create([
            'booking_test_id' => $bookingTest->id,
        ]);

        $result->update([
            'value' => $validated['value'] ?? ($bookingTest->test->hasParameters() ? 'See parameters' : null),
            'numeric_value' => $validated['numeric_value'] ?? null,
            'remarks' => $validated['remarks'] ?? null,
            'entered_by' => auth()->id(),
            'entered_at' => now(),
            'status' => 'approved',  // Auto-approve - skip approval step
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        if (!$bookingTest->test->hasParameters() && $validated['numeric_value']) {
            $result->calculateFlag();
            $result->save();
        }

        $bookingTest->update(['status' => 'completed']);  // Mark as completed
        ActivityLog::log('result_entered', $result, [], $validated);

        // Check if all tests in booking are completed
        $booking = $bookingTest->booking;
        $allCompleted = $booking->bookingTests()
            ->whereDoesntHave('result', fn($q) => $q->where('status', 'approved'))
            ->doesntExist();
        
        if ($allCompleted) {
            $booking->update(['status' => 'completed']);
        }

        // Check for next test to enter
        $nextTest = BookingTest::where('booking_id', $bookingTest->booking_id)
            ->where('id', '>', $bookingTest->id)
            ->whereDoesntHave('result', fn($q) => $q->where('status', 'approved'))
            ->first();

        // Auto-generate/regenerate report
        $this->autoRegenerateReport($booking);

        if ($nextTest) {
            return redirect()->route('results.parameters', $nextTest)
                ->with('success', 'Results saved! Enter next test results.');
        }

        // All tests completed - redirect to booking with option to generate report
        return redirect()->route('bookings.show', $bookingTest->booking_id)
            ->with('success', 'All results completed! Report has been auto-generated.');
    }

    public function store(Request $request, Result $result)
    {
        $validated = $request->validate([
            'value' => 'required|string',
            'numeric_value' => 'nullable|numeric',
            'remarks' => 'nullable|string',
        ]);

        $result->update([
            'value' => $validated['value'],
            'numeric_value' => $validated['numeric_value'] ?? null,
            'remarks' => $validated['remarks'] ?? null,
            'entered_by' => auth()->id(),
            'entered_at' => now(),
            'status' => 'entered',
        ]);

        $result->calculateFlag();
        $result->save();
        $result->bookingTest->update(['status' => 'in_progress']);

        ActivityLog::log('result_entered', $result, [], $validated);
        $this->checkBookingCompletion($result->bookingTest->booking_id);

        return redirect()->route('results.pending')
            ->with('success', 'Result entered successfully.');
    }

    public function bulkEntry(Request $request)
    {
        $validated = $request->validate([
            'results' => 'required|array',
            'results.*.id' => 'required|exists:results,id',
            'results.*.value' => 'required|string',
            'results.*.numeric_value' => 'nullable|numeric',
        ]);

        foreach ($validated['results'] as $data) {
            $result = Result::findOrFail($data['id']);
            
            $result->update([
                'value' => $data['value'],
                'numeric_value' => $data['numeric_value'] ?? null,
                'entered_by' => auth()->id(),
                'entered_at' => now(),
                'status' => 'entered',
            ]);

            $result->calculateFlag();
            $result->save();

            $result->bookingTest->update(['status' => 'in_progress']);
            $this->checkBookingCompletion($result->bookingTest->booking_id);
        }

        ActivityLog::log('bulk_results_entered', null, [], ['count' => count($validated['results'])]);

        return redirect()->route('results.pending')
            ->with('success', count($validated['results']) . ' results entered successfully.');
    }

    public function checkFlag(Request $request)
    {
        $validated = $request->validate([
            'test_id' => 'required|exists:tests,id',
            'value' => 'required|numeric',
            'gender' => 'required|in:male,female,other',
        ]);

        $test = \App\Models\Test::findOrFail($validated['test_id']);
        $flag = $test->checkValueInRange($validated['value'], $validated['gender']);

        return response()->json([
            'flag' => $flag,
            'normal_range' => $test->normal_range,
            'is_abnormal' => $flag !== 'normal',
        ]);
    }

    private function checkBookingCompletion(int $bookingId)
    {
        $booking = \App\Models\Booking::findOrFail($bookingId);
        
        $pendingCount = $booking->bookingTests()
            ->whereHas('result', function($q) {
                $q->where('status', 'pending');
            })
            ->count();

        if ($pendingCount === 0) {
            $booking->update(['status' => 'in_progress']);
        }
    }

    /**
     * Show edit form for a completed result
     */
    public function edit(BookingTest $bookingTest)
    {
        $bookingTest->load([
            'test.parameters',
            'test.category',
            'booking.patient',
            'parameterResults',
            'result.editedBy'
        ]);

        return view('results.edit', compact('bookingTest'));
    }

    /**
     * Update an existing result with edit tracking
     */
    public function updateEdit(Request $request, BookingTest $bookingTest)
    {
        $result = $bookingTest->result;
        
        if (!$result) {
            return back()->with('error', 'No result found to edit.');
        }

        $validated = $request->validate([
            'value' => 'nullable|string',
            'edit_reason' => 'required|string|max:255',
            'parameters' => 'nullable|array',
        ]);

        // Track previous value
        $previousValue = $result->value;

        // Update main result if simple test
        if (!$bookingTest->test->hasParameters()) {
            $result->update([
                'previous_value' => $previousValue,
                'value' => $validated['value'],
                'edited_at' => now(),
                'edited_by' => auth()->id(),
                'edit_reason' => $validated['edit_reason'],
            ]);
        }

        // Update parameter results if test has parameters
        if (!empty($validated['parameters'])) {
            foreach ($validated['parameters'] as $paramId => $data) {
                $paramResult = ParameterResult::where('booking_test_id', $bookingTest->id)
                    ->where('test_parameter_id', $paramId)
                    ->first();
                
                if ($paramResult && isset($data['value'])) {
                    $paramResult->update([
                        'value' => $data['value'],
                        'numeric_value' => is_numeric($data['value']) ? (float)$data['value'] : null,
                    ]);
                }
            }
            
            // Track edit on main result
            $result->update([
                'previous_value' => 'Parameters edited',
                'edited_at' => now(),
                'edited_by' => auth()->id(),
                'edit_reason' => $validated['edit_reason'],
            ]);
        }

        ActivityLog::log('result_edited', $result, [
            'edit_reason' => $validated['edit_reason'],
            'previous_value' => $previousValue,
        ]);

        // Auto-regenerate report
        $this->autoRegenerateReport($bookingTest->booking);

        return redirect()->route('bookings.show', $bookingTest->booking)
            ->with('success', 'Result updated and report regenerated.');
    }

    /**
     * Auto-generate or regenerate report when results are entered/edited
     */
    private function autoRegenerateReport($booking)
    {
        // Check if booking has any results
        $hasAnyResults = $booking->bookingTests()->whereHas('result')->exists();
        
        if (!$hasAnyResults) {
            return;
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

        $lab = $booking->lab ?? \App\Models\Lab::first();

        // Generate QR code
        $qrCode = base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(100)->generate(
            route('reports.verify', ['report' => $booking->report?->report_id ?? 'temp'])
        ));

        // Generate PDF
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.pdf', [
            'booking' => $booking,
            'lab' => $lab,
            'qrCode' => $qrCode,
        ]);

        $filename = "reports/report-{$booking->booking_id}-" . now()->timestamp . ".pdf";
        \Illuminate\Support\Facades\Storage::disk('public')->put($filename, $pdf->output());

        // Create or update report
        $report = \App\Models\Report::updateOrCreate(
            ['booking_id' => $booking->id],
            [
                'pdf_path' => $filename,
                'qr_code' => route('reports.verify', ['report' => $booking->report?->report_id ?? 'new']),
                'generated_by' => auth()->id(),
                'generated_at' => now(),
                'is_final' => true,
            ]
        );

        \App\Models\ActivityLog::log('report_auto_generated', $report);
    }
}
