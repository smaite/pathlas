<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Patient;
use App\Models\Result;
use App\Models\Payment;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Super admin goes to super admin dashboard
        if ($user->isSuperAdmin()) {
            return redirect()->route('superadmin.dashboard');
        }
        
        if ($user->isAdmin()) {
            return $this->adminDashboard();
        } elseif ($user->isReceptionist()) {
            return $this->receptionDashboard();
        } elseif ($user->isTechnician()) {
            return $this->labDashboard();
        } elseif ($user->isPathologist()) {
            return $this->pathologistDashboard();
        }
        
        return $this->receptionDashboard();
    }

    // Get lab-scoped patient query
    private function patientQuery()
    {
        $user = auth()->user();
        if ($user->isSuperAdmin()) {
            return Patient::query();
        }
        return Patient::where('lab_id', $user->lab_id);
    }

    // Get lab-scoped booking query
    private function bookingQuery()
    {
        $user = auth()->user();
        if ($user->isSuperAdmin()) {
            return Booking::query();
        }
        return Booking::where('lab_id', $user->lab_id);
    }

    // Get lab-scoped payment query (via bookings)
    private function paymentQuery()
    {
        $user = auth()->user();
        if ($user->isSuperAdmin()) {
            return Payment::query();
        }
        return Payment::whereHas('booking', function($q) use ($user) {
            $q->where('lab_id', $user->lab_id);
        });
    }

    // Get lab-scoped result query (via booking_tests)
    private function resultQuery()
    {
        $user = auth()->user();
        if ($user->isSuperAdmin()) {
            return Result::query();
        }
        return Result::whereHas('bookingTest.booking', function($q) use ($user) {
            $q->where('lab_id', $user->lab_id);
        });
    }

    private function adminDashboard()
    {
        $stats = [
            'total_patients' => $this->patientQuery()->count(),
            'total_bookings' => $this->bookingQuery()->count(),
            'pending_bookings' => $this->bookingQuery()->pending()->count(),
            'completed_today' => $this->bookingQuery()->completed()->whereDate('updated_at', today())->count(),
            'revenue_today' => $this->paymentQuery()->whereDate('created_at', today())->sum('amount'),
            'revenue_month' => $this->paymentQuery()->whereMonth('created_at', now()->month)->sum('amount'),
        ];

        $recentBookings = $this->bookingQuery()
            ->with('patient')
            ->latest()
            ->take(10)
            ->get();

        $recentPayments = $this->paymentQuery()
            ->with('booking.patient')
            ->latest()
            ->take(10)
            ->get();

        return view('dashboard.admin', compact('stats', 'recentBookings', 'recentPayments'));
    }

    private function receptionDashboard()
    {
        $stats = [
            'patients_today' => $this->patientQuery()->whereDate('created_at', today())->count(),
            'bookings_today' => $this->bookingQuery()->whereDate('created_at', today())->count(),
            'pending_bookings' => $this->bookingQuery()->pending()->count(),
            'unpaid_bookings' => $this->bookingQuery()->where('payment_status', '!=', 'paid')->count(),
        ];

        $recentPatients = $this->patientQuery()->latest()->take(5)->get();
        $pendingPayments = $this->bookingQuery()
            ->with('patient')
            ->where('payment_status', '!=', 'paid')
            ->latest()
            ->take(10)
            ->get();

        return view('dashboard.reception', compact('stats', 'recentPatients', 'pendingPayments'));
    }

    private function labDashboard()
    {
        $stats = [
            'pending_tests' => $this->resultQuery()->where('status', 'pending')->count(),
            'in_progress' => $this->resultQuery()->where('status', 'entered')->count(),
            'completed_today' => $this->resultQuery()->whereDate('entered_at', today())->count(),
        ];

        $pendingTests = $this->resultQuery()
            ->with(['bookingTest.test', 'bookingTest.booking.patient'])
            ->where('status', 'pending')
            ->latest()
            ->take(20)
            ->get();

        return view('dashboard.lab', compact('stats', 'pendingTests'));
    }

    private function pathologistDashboard()
    {
        $stats = [
            'pending_approval' => $this->resultQuery()->pendingApproval()->count(),
            'approved_today' => $this->resultQuery()->approved()->whereDate('approved_at', today())->count(),
        ];

        $pendingApprovals = $this->resultQuery()
            ->with(['bookingTest.test', 'bookingTest.booking.patient', 'enteredBy'])
            ->pendingApproval()
            ->latest()
            ->take(20)
            ->get();

        return view('dashboard.pathologist', compact('stats', 'pendingApprovals'));
    }
}
