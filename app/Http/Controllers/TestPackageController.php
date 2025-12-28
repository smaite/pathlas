<?php

namespace App\Http\Controllers;

use App\Models\TestPackage;
use App\Models\Test;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class TestPackageController extends Controller
{
    public function index()
    {
        $packages = TestPackage::where('lab_id', auth()->user()->lab_id)
            ->with('tests')
            ->ordered()
            ->get();
            
        return view('packages.index', compact('packages'));
    }

    public function create()
    {
        $tests = Test::active()->orderBy('name')->get();
        return view('packages.create', compact('tests'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:test_packages',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'mrp' => 'nullable|numeric|min:0',
            'tests' => 'required|array|min:1',
            'tests.*' => 'exists:tests,id',
        ]);

        $package = TestPackage::create([
            'lab_id' => auth()->user()->lab_id,
            'name' => $validated['name'],
            'code' => strtoupper($validated['code']),
            'description' => $validated['description'] ?? null,
            'price' => $validated['price'],
            'mrp' => $validated['mrp'] ?? null,
            'is_active' => true,
        ]);

        $package->tests()->attach($validated['tests']);
        ActivityLog::log('package_created', $package);

        return redirect()->route('packages.index')
            ->with('success', 'Test package created successfully.');
    }

    public function show(TestPackage $package)
    {
        $package->load('tests.category');
        return view('packages.show', compact('package'));
    }

    public function edit(TestPackage $package)
    {
        $tests = Test::active()->orderBy('name')->get();
        $package->load('tests');
        return view('packages.edit', compact('package', 'tests'));
    }

    public function update(Request $request, TestPackage $package)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:test_packages,code,' . $package->id,
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'mrp' => 'nullable|numeric|min:0',
            'tests' => 'required|array|min:1',
            'tests.*' => 'exists:tests,id',
            'is_active' => 'boolean',
        ]);

        $package->update([
            'name' => $validated['name'],
            'code' => strtoupper($validated['code']),
            'description' => $validated['description'] ?? null,
            'price' => $validated['price'],
            'mrp' => $validated['mrp'] ?? null,
            'is_active' => $request->boolean('is_active', true),
        ]);

        $package->tests()->sync($validated['tests']);
        ActivityLog::log('package_updated', $package);

        return redirect()->route('packages.index')
            ->with('success', 'Test package updated successfully.');
    }

    public function destroy(TestPackage $package)
    {
        ActivityLog::log('package_deleted', $package, $package->toArray());
        $package->delete();

        return redirect()->route('packages.index')
            ->with('success', 'Test package deleted successfully.');
    }

    public function toggleStatus(TestPackage $package)
    {
        $package->update(['is_active' => !$package->is_active]);
        return back()->with('success', 'Package status updated.');
    }
}
