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
            'pan_number' => 'nullable|string|max:50',
            'website' => 'nullable|string|max:255',
            'header_color' => 'nullable|string|max:20',
            'report_notes' => 'nullable|string',
            'signature_name' => 'nullable|string|max:100',
            'signature_designation' => 'nullable|string|max:100',
        ]);

        $validated['require_approval'] = $request->has('require_approval');

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('labs/' . $lab->id, 'public');
            $validated['logo'] = $logoPath;
        }

        // Handle signature image upload
        if ($request->hasFile('signature_image')) {
            $signaturePath = $request->file('signature_image')->store('labs/' . $lab->id . '/signatures', 'public');
            $validated['signature_image'] = $signaturePath;
        }

        $lab->update($validated);
        ActivityLog::log('lab_settings_updated', $lab);

        return back()->with('success', 'Lab settings updated successfully.');
    }

    // Report Customization Page
    public function reportCustomization()
    {
        $lab = auth()->user()->lab;
        
        if (!$lab) {
            return redirect()->route('dashboard')->with('error', 'No lab associated with your account.');
        }

        return view('labs.report-customization', compact('lab'));
    }

    public function updateReportCustomization(Request $request)
    {
        $lab = auth()->user()->lab;
        
        if (!$lab) {
            return back()->with('error', 'No lab associated with your account.');
        }

        $validated = $request->validate([
            'header_color' => 'nullable|string|max:20',
            'logo_width' => 'nullable|integer|min:30|max:200',
            'logo_height' => 'nullable|integer|min:30|max:150',
            'signature_name' => 'nullable|string|max:100',
            'signature_designation' => 'nullable|string|max:100',
            'signature_width' => 'nullable|integer|min:50|max:200',
            'signature_height' => 'nullable|integer|min:20|max:80',
            'signature_name_2' => 'nullable|string|max:100',
            'signature_designation_2' => 'nullable|string|max:100',
            'signature_width_2' => 'nullable|integer|min:50|max:200',
            'signature_height_2' => 'nullable|integer|min:20|max:80',
            'report_notes' => 'nullable|string',
            'headerless_margin_top' => 'nullable|integer|min:10|max:100',
            'headerless_margin_bottom' => 'nullable|integer|min:10|max:80',
        ]);

        // Handle file uploads
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('labs/' . $lab->id, 'public');
            $validated['logo'] = $logoPath;
        }

        if ($request->hasFile('signature_image')) {
            $sigPath = $request->file('signature_image')->store('labs/' . $lab->id . '/signatures', 'public');
            $validated['signature_image'] = $sigPath;
        }

        if ($request->hasFile('signature_image_2')) {
            $sig2Path = $request->file('signature_image_2')->store('labs/' . $lab->id . '/signatures', 'public');
            $validated['signature_image_2'] = $sig2Path;
        }

        $lab->update($validated);
        ActivityLog::log('report_customization_updated', $lab);

        return back()->with('success', 'Report customization saved successfully.');
    }

    // Live preview endpoint for report customization
    public function previewReport(Request $request)
    {
        $lab = auth()->user()->lab;
        
        if (!$lab) {
            return response('No lab found', 404);
        }

        // Apply temporary settings from request for preview
        $previewLab = clone $lab;
        if ($request->has('header_color')) $previewLab->header_color = $request->header_color;
        if ($request->has('logo_width')) $previewLab->logo_width = $request->logo_width;
        if ($request->has('logo_height')) $previewLab->logo_height = $request->logo_height;
        if ($request->has('signature_name')) $previewLab->signature_name = $request->signature_name;
        if ($request->has('signature_designation')) $previewLab->signature_designation = $request->signature_designation;
        if ($request->has('signature_name_2')) $previewLab->signature_name_2 = $request->signature_name_2;
        if ($request->has('signature_designation_2')) $previewLab->signature_designation_2 = $request->signature_designation_2;
        if ($request->has('report_notes')) $previewLab->report_notes = $request->report_notes;

        return view('labs.report-preview', [
            'lab' => $previewLab,
        ]);
    }
}

