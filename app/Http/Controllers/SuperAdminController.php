<?php

namespace App\Http\Controllers;

use App\Models\Lab;
use App\Models\User;
use App\Models\Booking;
use App\Models\Patient;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class SuperAdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_labs' => Lab::count(),
            'pending_labs' => Lab::pending()->count(),
            'active_subscriptions' => Lab::where('subscription_expires_at', '>', now())->orWhere('subscription_plan', 'lifetime')->count(),
            'expired_subscriptions' => Lab::where('subscription_expires_at', '<', now())->where('subscription_plan', '!=', 'lifetime')->count(),
            'total_users' => User::whereNotNull('lab_id')->count(),
            'total_patients' => Patient::count(),
            'total_bookings' => Booking::count(),
        ];

        $pendingLabs = Lab::pending()->latest()->take(5)->get();
        $expiringLabs = Lab::where('subscription_expires_at', '>', now())
            ->where('subscription_expires_at', '<', now()->addDays(7))
            ->where('subscription_plan', '!=', 'lifetime')
            ->get();
        $recentLabs = Lab::latest()->take(10)->get();

        return view('superadmin.dashboard', compact('stats', 'pendingLabs', 'expiringLabs', 'recentLabs'));
    }

    public function labs(Request $request)
    {
        $query = Lab::withCount(['users', 'patients', 'bookings']);

        if ($request->filled('status')) {
            match($request->status) {
                'pending' => $query->pending(),
                'verified' => $query->verified(),
                'expired' => $query->where('subscription_expires_at', '<', now())->where('subscription_plan', '!=', 'lifetime'),
                'active' => $query->where(function($q) {
                    $q->where('subscription_expires_at', '>', now())->orWhere('subscription_plan', 'lifetime');
                }),
                default => null,
            };
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%");
            });
        }

        $labs = $query->latest()->paginate(20);
        return view('superadmin.labs', compact('labs'));
    }

    public function verifyLab(Lab $lab)
    {
        $lab->update([
            'is_verified' => true,
            'verified_at' => now(),
            'verified_by' => auth()->id(),
            'rejection_reason' => null,
        ]);

        ActivityLog::log('lab_verified', $lab);

        return back()->with('success', "Lab '{$lab->name}' has been verified.");
    }

    public function rejectLab(Request $request, Lab $lab)
    {
        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $lab->update([
            'is_verified' => false,
            'rejection_reason' => $validated['rejection_reason'],
        ]);

        ActivityLog::log('lab_rejected', $lab);

        return back()->with('success', "Lab '{$lab->name}' has been rejected.");
    }

    public function extendSubscription(Request $request, Lab $lab)
    {
        $validated = $request->validate([
            'subscription_plan' => 'required|in:free_trial,monthly,yearly,lifetime,custom',
            'expires_at' => 'required_unless:subscription_plan,lifetime|date|after:today',
            'amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:500',
        ]);

        $lab->update([
            'subscription_plan' => $validated['subscription_plan'],
            'subscription_expires_at' => $validated['subscription_plan'] === 'lifetime' ? null : $validated['expires_at'],
            'subscription_amount' => $validated['amount'] ?? 0,
            'subscription_notes' => $validated['notes'] ?? null,
        ]);

        if (!$lab->subscription_starts_at) {
            $lab->update(['subscription_starts_at' => now()]);
        }

        ActivityLog::log('subscription_extended', $lab);

        return back()->with('success', "Subscription updated for '{$lab->name}'.");
    }

    public function revokeSubscription(Lab $lab)
    {
        $lab->update([
            'subscription_expires_at' => now()->subDay(),
        ]);

        ActivityLog::log('subscription_revoked', $lab);

        return back()->with('success', "Subscription revoked for '{$lab->name}'.");
    }

    public function showLab(Lab $lab)
    {
        $lab->load(['users', 'verifiedBy']);
        $stats = [
            'users' => $lab->users()->count(),
            'patients' => $lab->patients()->count(),
            'bookings' => $lab->bookings()->count(),
            'reports' => $lab->bookings()->whereHas('report')->count(),
        ];
        $recentBookings = $lab->bookings()->with('patient')->latest()->take(10)->get();

        return view('superadmin.lab-details', compact('lab', 'stats', 'recentBookings'));
    }

    public function toggleLabStatus(Lab $lab)
    {
        $lab->update(['is_active' => !$lab->is_active]);
        return back()->with('success', 'Lab status updated.');
    }
}
