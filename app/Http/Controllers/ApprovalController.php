<?php

namespace App\Http\Controllers;

use App\Models\Result;
use App\Models\Booking;
use App\Models\Report;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ApprovalController extends Controller
{
    public function index()
    {
        $pendingResults = Result::with([
            'bookingTest.test.category',
            'bookingTest.booking.patient',
            'enteredBy'
        ])
        ->pendingApproval()
        ->latest()
        ->paginate(20);

        return view('approvals.index', compact('pendingResults'));
    }

    public function show(Booking $booking)
    {
        $booking->load([
            'patient',
            'bookingTests.test.category',
            'bookingTests.result.enteredBy',
            'createdBy'
        ]);

        $hasEnteredResults = $booking->bookingTests->every(function($bt) {
            return $bt->result && in_array($bt->result->status, ['entered', 'verified', 'approved']);
        });

        return view('approvals.show', compact('booking', 'hasEnteredResults'));
    }

    public function approve(Request $request, Result $result)
    {
        $validated = $request->validate([
            'remarks' => 'nullable|string',
        ]);

        $result->update([
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'status' => 'approved',
            'remarks' => $validated['remarks'] ?? $result->remarks,
        ]);

        $result->bookingTest->update(['status' => 'completed']);

        ActivityLog::log('result_approved', $result);

        // Check if all results are approved
        $this->checkAllApproved($result->bookingTest->booking_id);

        return back()->with('success', 'Result approved successfully.');
    }

    public function reject(Request $request, Result $result)
    {
        $validated = $request->validate([
            'remarks' => 'required|string|min:10',
        ]);

        $result->update([
            'status' => 'rejected',
            'remarks' => $validated['remarks'],
        ]);

        $result->bookingTest->update(['status' => 'pending']);

        ActivityLog::log('result_rejected', $result, [], ['remarks' => $validated['remarks']]);

        return back()->with('success', 'Result rejected. Technician will re-enter.');
    }

    public function bulkApprove(Request $request)
    {
        $validated = $request->validate([
            'result_ids' => 'required|array',
            'result_ids.*' => 'exists:results,id',
        ]);

        $bookingIds = [];

        foreach ($validated['result_ids'] as $resultId) {
            $result = Result::findOrFail($resultId);
            
            $result->update([
                'approved_by' => auth()->id(),
                'approved_at' => now(),
                'status' => 'approved',
            ]);

            $result->bookingTest->update(['status' => 'completed']);
            $bookingIds[] = $result->bookingTest->booking_id;
        }

        // Check booking completion for each affected booking
        foreach (array_unique($bookingIds) as $bookingId) {
            $this->checkAllApproved($bookingId);
        }

        ActivityLog::log('bulk_results_approved', null, [], ['count' => count($validated['result_ids'])]);

        return back()->with('success', count($validated['result_ids']) . ' results approved.');
    }

    public function approveBooking(Booking $booking)
    {
        $results = Result::whereHas('bookingTest', function($q) use ($booking) {
            $q->where('booking_id', $booking->id);
        })->whereIn('status', ['entered', 'verified'])->get();

        foreach ($results as $result) {
            $result->update([
                'approved_by' => auth()->id(),
                'approved_at' => now(),
                'status' => 'approved',
            ]);
            $result->bookingTest->update(['status' => 'completed']);
        }

        $booking->update(['status' => 'completed']);
        ActivityLog::log('booking_approved', $booking);

        return redirect()->route('reports.generate', $booking)
            ->with('success', 'All results approved. Generating report...');
    }

    private function checkAllApproved(int $bookingId)
    {
        $booking = Booking::findOrFail($bookingId);
        
        $pendingCount = $booking->bookingTests()
            ->whereHas('result', function($q) {
                $q->where('status', '!=', 'approved');
            })
            ->count();

        if ($pendingCount === 0) {
            $booking->update(['status' => 'completed']);
        }
    }
}
