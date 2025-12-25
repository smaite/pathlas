<?php

namespace App\Http\Controllers;

use App\Models\Lab;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class LabController extends Controller
{
    public function index()
    {
        $labs = Lab::withCount(['users', 'patients', 'bookings'])->latest()->get();
        return view('labs.index', compact('labs'));
    }

    public function create()
    {
        return view('labs.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:labs',
            'tagline' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'pincode' => 'nullable|string|max:10',
            'phone' => 'nullable|string|max:20',
            'phone2' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|string|max:255',
            'header_color' => 'nullable|string|max:20',
            'footer_note' => 'nullable|string',
            'report_notes' => 'nullable|string',
        ]);

        $lab = Lab::create($validated);
        ActivityLog::log('lab_created', $lab);

        return redirect()->route('labs.index')
            ->with('success', 'Lab created successfully.');
    }

    public function edit(Lab $lab)
    {
        return view('labs.edit', compact('lab'));
    }

    public function update(Request $request, Lab $lab)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:labs,code,' . $lab->id,
            'tagline' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'pincode' => 'nullable|string|max:10',
            'phone' => 'nullable|string|max:20',
            'phone2' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|string|max:255',
            'header_color' => 'nullable|string|max:20',
            'footer_note' => 'nullable|string',
            'report_notes' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $lab->update($validated);
        ActivityLog::log('lab_updated', $lab);

        return redirect()->route('labs.index')
            ->with('success', 'Lab updated successfully.');
    }

    public function destroy(Lab $lab)
    {
        if ($lab->bookings()->count() > 0) {
            return back()->with('error', 'Cannot delete lab with existing bookings.');
        }

        ActivityLog::log('lab_deleted', $lab, $lab->toArray());
        $lab->delete();

        return redirect()->route('labs.index')
            ->with('success', 'Lab deleted successfully.');
    }

    public function toggleStatus(Lab $lab)
    {
        $lab->update(['is_active' => !$lab->is_active]);
        return back()->with('success', 'Lab status updated.');
    }

    // Lab admin settings for their own lab
    public function settings()
    {
        $lab = auth()->user()->lab;
        
        if (!$lab) {
            return redirect()->route('dashboard')->with('error', 'No lab associated with your account.');
        }

        return view('labs.settings', compact('lab'));
    }

    public function updateSettings(Request $request)
    {
        $lab = auth()->user()->lab;
        
        if (!$lab) {
            return back()->with('error', 'No lab associated with your account.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'tagline' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'pincode' => 'nullable|string|max:10',
            'phone' => 'nullable|string|max:20',
            'phone2' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|string|max:255',
            'header_color' => 'nullable|string|max:20',
            'report_notes' => 'nullable|string',
        ]);

        $validated['require_approval'] = $request->has('require_approval');

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('labs/' . $lab->id, 'public');
            $validated['logo'] = $logoPath;
        }

        $lab->update($validated);
        ActivityLog::log('lab_settings_updated', $lab);

        return back()->with('success', 'Lab settings updated successfully.');
    }
}
