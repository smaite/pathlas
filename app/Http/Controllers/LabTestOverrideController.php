<?php

namespace App\Http\Controllers;

use App\Http\Requests\LabTestOverrides\UpdateLabTestOverrideRequest;
use App\Http\Requests\LabTestOverrides\BulkUpdateTestPricesRequest;
use App\Models\Test;
use App\Models\LabTestOverride;
use Illuminate\Http\Request;

class LabTestOverrideController extends Controller
{
    /**
     * Show all tests with lab-specific overrides for the current lab
     */
    public function index()
    {
        $labId = auth()->user()->lab_id;
        
        $tests = Test::with(['category', 'labOverrides' => fn($q) => $q->where('lab_id', $labId)])
            ->active()
            ->orderBy('name')
            ->get()
            ->map(fn($test) => array_merge($test->toArray(), [
                'lab_price' => $test->getPriceForLab($labId),
                'has_override' => $test->getLabOverride($labId) !== null,
            ]));

        return view('lab-tests.index', compact('tests'));
    }

    /**
     * Show form to customize a test for the current lab
     */
    public function edit(Test $test)
    {
        $labId = auth()->user()->lab_id;
        $override = LabTestOverride::where('lab_id', $labId)
            ->where('test_id', $test->id)
            ->first();

        return view('lab-tests.edit', compact('test', 'override'));
    }

    /**
     * Save lab-specific override for a test
     */
    public function update(UpdateLabTestOverrideRequest $request, Test $test)
    {
        $labId = auth()->user()->lab_id;

        $validated = $request->validated();

        // Build overrides JSON - only include non-null values
        $overrides = [];
        foreach (['price', 'name', 'short_name', 'unit', 'normal_range', 'sample_type', 'method', 'turnaround_time'] as $field) {
            if (isset($validated[$field]) && $validated[$field] !== null && $validated[$field] !== '') {
                $overrides[$field] = $validated[$field];
            }
        }

        // If no overrides and is_active is true (default), delete the record
        if (empty($overrides) && ($validated['is_active'] ?? true)) {
            LabTestOverride::where('lab_id', $labId)
                ->where('test_id', $test->id)
                ->delete();
                
            return back()->with('success', 'Test reset to default settings.');
        }

        LabTestOverride::updateOrCreate(
            ['lab_id' => $labId, 'test_id' => $test->id],
            [
                'overrides' => empty($overrides) ? null : $overrides,
                'is_active' => $validated['is_active'] ?? true,
            ]
        );

        return back()->with('success', 'Test customization saved.');
    }

    /**
     * Reset test to default (remove override)
     */
    public function reset(Test $test)
    {
        $labId = auth()->user()->lab_id;
        
        LabTestOverride::where('lab_id', $labId)
            ->where('test_id', $test->id)
            ->delete();

        return back()->with('success', 'Test reset to default.');
    }

    /**
     * Bulk update prices for multiple tests
     */
    public function bulkUpdatePrices(BulkUpdateTestPricesRequest $request)
    {
        $labId = auth()->user()->lab_id;

        $validated = $request->validated();

        foreach ($validated['prices'] as $item) {
            $test = Test::find($item['test_id']);
            
            // Only create override if price differs from master
            if ((float)$test->price !== (float)$item['price']) {
                $override = LabTestOverride::firstOrNew([
                    'lab_id' => $labId,
                    'test_id' => $item['test_id']
                ]);
                
                $overrides = $override->overrides ?? [];
                $overrides['price'] = $item['price'];
                $override->overrides = $overrides;
                $override->save();
            }
        }

        return back()->with('success', 'Prices updated successfully.');
    }
}
