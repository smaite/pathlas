<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    // Get lab-scoped patient query
    private function labQuery()
    {
        $user = auth()->user();
        
        // Super admin sees all
        if ($user->isSuperAdmin()) {
            return Patient::query();
        }
        
        // Others see their lab's patients OR patients without a lab (legacy data)
        return Patient::where(function($q) use ($user) {
            $q->where('lab_id', $user->lab_id)
              ->orWhereNull('lab_id');
        });
    }

    public function index(Request $request)
    {
        $query = $this->labQuery()->with('createdBy')->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('patient_id', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $patients = $query->paginate(15);
        return view('patients.index', compact('patients'));
    }

    public function create()
    {
        return view('patients.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'age' => 'nullable|integer|min:0|max:150',
            'gender' => 'nullable|in:male,female,other',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'date_of_birth' => 'nullable|date',
            'blood_group' => 'nullable|string|max:5',
            'medical_history' => 'nullable|string',
        ]);

        // Set default name if not provided
        if (empty($validated['name'])) {
            $validated['name'] = 'Walk-in Patient';
        }

        $validated['created_by'] = auth()->id();
        $validated['lab_id'] = auth()->user()->lab_id;
        
        $patient = Patient::create($validated);

        ActivityLog::log('patient_created', $patient, [], $validated);

        return redirect()->route('patients.show', $patient)
            ->with('success', 'Patient registered successfully.');
    }

    public function show(Patient $patient)
    {
        // Check lab access
        $this->authorizeLabAccess($patient);
        
        $patient->load(['bookings.bookingTests.test', 'bookings.payments']);
        return view('patients.show', compact('patient'));
    }

    public function edit(Patient $patient)
    {
        $this->authorizeLabAccess($patient);
        return view('patients.edit', compact('patient'));
    }

    public function update(Request $request, Patient $patient)
    {
        $this->authorizeLabAccess($patient);
        
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'age' => 'nullable|integer|min:0|max:150',
            'gender' => 'nullable|in:male,female,other',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'date_of_birth' => 'nullable|date',
            'blood_group' => 'nullable|string|max:5',
            'medical_history' => 'nullable|string',
        ]);

        $oldValues = $patient->toArray();
        $patient->update($validated);

        ActivityLog::log('patient_updated', $patient, $oldValues, $validated);

        return redirect()->route('patients.show', $patient)
            ->with('success', 'Patient updated successfully.');
    }

    public function destroy(Patient $patient)
    {
        $this->authorizeLabAccess($patient);
        
        ActivityLog::log('patient_deleted', $patient, $patient->toArray());
        $patient->delete();

        return redirect()->route('patients.index')
            ->with('success', 'Patient deleted successfully.');
    }

    public function search(Request $request)
    {
        $search = $request->get('q', '');
        $user = auth()->user();
        
        $query = Patient::query();
        
        // Lab-scoped search
        if (!$user->isSuperAdmin() && $user->lab_id) {
            $query->where('lab_id', $user->lab_id);
        }
        
        $patients = $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('patient_id', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            })
            ->take(10)
            ->get(['id', 'patient_id', 'name', 'age', 'gender', 'phone']);

        return response()->json($patients);
    }

    private function authorizeLabAccess($patient)
    {
        $user = auth()->user();
        
        // Super admin can access all
        if ($user->isSuperAdmin()) {
            return;
        }
        
        // If patient has no lab (legacy data), allow access
        if ($patient->lab_id === null) {
            return;
        }
        
        // Others can only access their lab's patients
        if ($patient->lab_id !== $user->lab_id) {
            abort(403, 'Unauthorized access to patient.');
        }
    }
}
