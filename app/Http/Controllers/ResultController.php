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
                // Filter by lab for non-super-admin users
                if (!$user->isSuperAdmin()) {
                    $q->where(fn($b) => $b->where('lab_id', $labId)->orWhereNull('lab_id'));
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

        if ($nextTest) {
            return redirect()->route('results.parameters', $nextTest)
                ->with('success', 'Results saved! Enter next test results.');
        }

        // All tests completed - redirect to booking with option to generate report
        return redirect()->route('bookings.show', $bookingTest->booking_id)
            ->with('success', 'All results completed! You can now generate the report.');
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
}
