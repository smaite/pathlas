<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BookingTest;
use App\Models\Patient;
use App\Models\Test;
use App\Models\Payment;
use App\Models\Result;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class BookingController extends Controller
{
    // Get lab-scoped booking query
    private function labQuery()
    {
        $user = auth()->user();
        
        if ($user->isSuperAdmin()) {
            return Booking::query();
        }
        
        // Include bookings for user's lab OR bookings without a lab (legacy data)
        return Booking::where(function($q) use ($user) {
            $q->where('lab_id', $user->lab_id)
              ->orWhereNull('lab_id');
        });
    }

    public function index(Request $request)
    {
        $query = $this->labQuery()->with(['patient', 'createdBy', 'bookingTests'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('booking_id', 'like', "%{$search}%")
                  ->orWhereHas('patient', function($pq) use ($search) {
                      $pq->where('name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                  });
            });
        }

        $bookings = $query->paginate(15);
        return view('bookings.index', compact('bookings'));
    }

    public function create(Request $request)
    {
        $patient = null;
        if ($request->filled('patient_id')) {
            $patient = Patient::findOrFail($request->patient_id);
        }

        $tests = Test::active()->with('category')->get()->groupBy('category.name');
        $packages = \App\Models\TestPackage::where('lab_id', auth()->user()->lab_id)
            ->active()
            ->with('tests')
            ->ordered()
            ->get();
        return view('bookings.create', compact('patient', 'tests', 'packages'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'tests' => 'required|array|min:1',
            'tests.*' => 'exists:tests,id',
            'discount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'is_urgent' => 'boolean',
            'referring_doctor' => 'nullable|string|max:255',
            'patient_type' => 'nullable|string|max:50',
            'collection_centre' => 'nullable|string|max:255',
            'collection_date' => 'nullable|date',
            'received_date' => 'nullable|date',
            'reporting_date' => 'nullable|date',
            'sample_collected_by' => 'nullable|string|max:255',
            'sample_collected_at' => 'nullable|string|max:500',
            'paid_amount' => 'nullable|numeric|min:0',
            'payment_method' => 'nullable|in:cash,card,upi,bank_transfer,other',
        ]);

        $booking = Booking::create([
            'patient_id' => $validated['patient_id'],
            'lab_id' => auth()->user()->lab_id,
            'created_by' => auth()->id(),
            'discount' => $validated['discount'] ?? 0,
            'notes' => $validated['notes'] ?? null,
            'is_urgent' => $validated['is_urgent'] ?? false,
            'referring_doctor_name' => $validated['referring_doctor'] ?? null,
            'patient_type' => $validated['patient_type'] ?? 'other',
            'collection_centre' => $validated['collection_centre'] ?? 'Main Branch',
            'collection_date' => $validated['collection_date'] ?? now(),
            'received_date' => $validated['received_date'] ?? now(),
            'reporting_date' => $validated['reporting_date'] ?? null,
            'sample_collected_by' => $validated['sample_collected_by'] ?? null,
            'sample_collected_at_address' => $validated['sample_collected_at'] ?? null,
        ]);

        // Add tests to booking
        $firstTestWithParams = null;
        foreach ($validated['tests'] as $testId) {
            $test = Test::findOrFail($testId);
            $bookingTest = BookingTest::create([
                'booking_id' => $booking->id,
                'test_id' => $testId,
                'price' => $test->price,
            ]);

            // Create pending result entry
            Result::create([
                'booking_test_id' => $bookingTest->id,
                'status' => 'pending',
            ]);

            // Track first test with parameters for redirect
            if ($firstTestWithParams === null && $test->hasParameters()) {
                $firstTestWithParams = $bookingTest;
            }
        }

        $booking->calculateTotal();

        // Record payment if paid
        if (!empty($validated['paid_amount']) && $validated['paid_amount'] > 0) {
            Payment::create([
                'booking_id' => $booking->id,
                'amount' => $validated['paid_amount'],
                'method' => $validated['payment_method'] ?? 'cash',
                'received_by' => auth()->id(),
            ]);
        }

        ActivityLog::log('booking_created', $booking, [], $booking->toArray());

        // Redirect to result entry if test has parameters (like CBC)
        if ($firstTestWithParams) {
            return redirect()->route('results.parameters', $firstTestWithParams)
                ->with('success', 'Booking created! Enter test results below.');
        }

        return redirect()->route('bookings.show', $booking)
            ->with('success', 'Booking created successfully.');
    }

    public function show(Booking $booking)
    {
        $booking->load([
            'patient',
            'bookingTests.test.category',
            'bookingTests.test.parameters',
            'bookingTests.result',
            'bookingTests.parameterResults',
            'payments.receivedBy',
            'createdBy',
            'report'
        ]);

        return view('bookings.show', compact('booking'));
    }

    public function invoice(Booking $booking)
    {
        $booking->load(['patient', 'bookingTests.test', 'payments']);
        return view('bookings.invoice', compact('booking'));
    }

    public function invoicePdf(Booking $booking)
    {
        $booking->load(['patient', 'bookingTests.test', 'payments']);
        
        $pdf = Pdf::loadView('bookings.invoice-pdf', compact('booking'));
        return $pdf->download("invoice-{$booking->booking_id}.pdf");
    }

    public function updateStatus(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,sample_collected,in_progress,completed,cancelled',
        ]);

        $oldStatus = $booking->status;
        $booking->update($validated);

        ActivityLog::log('booking_status_updated', $booking, 
            ['status' => $oldStatus], 
            ['status' => $validated['status']]
        );

        return back()->with('success', 'Status updated successfully.');
    }

    public function addPayment(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01|max:' . $booking->due_amount,
            'method' => 'required|in:cash,card,upi,bank_transfer,other',
            'transaction_id' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $payment = Payment::create([
            'booking_id' => $booking->id,
            'amount' => $validated['amount'],
            'method' => $validated['method'],
            'transaction_id' => $validated['transaction_id'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'received_by' => auth()->id(),
        ]);

        ActivityLog::log('payment_received', $payment, [], $validated);

        return back()->with('success', 'Payment recorded successfully.');
    }

    public function destroy(Booking $booking)
    {
        if ($booking->status !== 'pending') {
            return back()->with('error', 'Only pending bookings can be deleted.');
        }

        ActivityLog::log('booking_deleted', $booking, $booking->toArray());
        $booking->delete();

        return redirect()->route('bookings.index')
            ->with('success', 'Booking deleted successfully.');
    }

    /**
     * Generate receipt PDF
     */
    public function receipt(Booking $booking)
    {
        $booking->load(['patient', 'bookingTests.test', 'payments', 'report']);
        $lab = auth()->user()->lab ?? $booking->lab ?? new \App\Models\Lab(['name' => 'PathLAS Lab']);

        $pdf = Pdf::loadView('receipts.pdf', compact('booking', 'lab'));
        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream('receipt-' . $booking->booking_id . '.pdf');
    }
}

