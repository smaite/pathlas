<?php

namespace App\Http\Controllers;

use App\Http\Requests\TestPackages\StoreTestPackageRequest;
use App\Http\Requests\TestPackages\UpdateTestPackageRequest;
use App\Models\TestPackage;
use App\Models\Test;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class TestPackageController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Strict lab isolation
        $query = TestPackage::with('tests');
        if (!$user->isSuperAdmin()) {
            $query->where('lab_id', $user->lab_id);
        }
        
        $packages = $query->ordered()->get();
            
        return view('packages.index', compact('packages'));
    }

    public function create()
    {
        $tests = Test::active()->orderBy('name')->get();
        return view('packages.create', compact('tests'));
    }

    public function store(StoreTestPackageRequest $request)
    {
        $validated = $request->validated();

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

    public function update(UpdateTestPackageRequest $request, TestPackage $package)
    {
        $validated = $request->validated();

        $package->update([
            'name' => $validated['name'],
            'code' => strtoupper($validated['code']),
            'description' => $validated['description'] ?? null,
            'price' => $validated['price'],
            'mrp' => $validated['mrp'] ?? null,
            'is_active' => $request->boolean('is_active', true), // Safe to keep using helper or validated value if present
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
