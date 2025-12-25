<?php

namespace App\Http\Controllers;

use App\Models\Test;
use App\Models\TestParameter;
use Illuminate\Http\Request;

class TestParameterController extends Controller
{
    public function store(Request $request, Test $test)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'unit' => 'nullable|string|max:50',
            'normal_min' => 'nullable|numeric',
            'normal_max' => 'nullable|numeric',
            'normal_min_male' => 'nullable|numeric',
            'normal_max_male' => 'nullable|numeric',
            'normal_min_female' => 'nullable|numeric',
            'normal_max_female' => 'nullable|numeric',
            'critical_low' => 'nullable|numeric',
            'critical_high' => 'nullable|numeric',
            'group_name' => 'nullable|string|max:100',
            'sort_order' => 'nullable|integer',
        ]);

        $validated['test_id'] = $test->id;
        $validated['is_active'] = true;
        
        if (!isset($validated['sort_order'])) {
            $validated['sort_order'] = $test->parameters()->max('sort_order') + 1;
        }

        TestParameter::create($validated);

        return redirect()->route('tests.show', $test)
            ->with('success', 'Parameter added successfully.');
    }

    public function update(Request $request, Test $test, TestParameter $parameter)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'unit' => 'nullable|string|max:50',
            'normal_min' => 'nullable|numeric',
            'normal_max' => 'nullable|numeric',
            'normal_min_male' => 'nullable|numeric',
            'normal_max_male' => 'nullable|numeric',
            'normal_min_female' => 'nullable|numeric',
            'normal_max_female' => 'nullable|numeric',
            'critical_low' => 'nullable|numeric',
            'critical_high' => 'nullable|numeric',
            'group_name' => 'nullable|string|max:100',
            'sort_order' => 'nullable|integer',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $parameter->update($validated);

        return redirect()->route('tests.show', $test)
            ->with('success', 'Parameter updated successfully.');
    }

    public function destroy(Test $test, TestParameter $parameter)
    {
        $parameter->delete();

        return redirect()->route('tests.show', $test)
            ->with('success', 'Parameter deleted successfully.');
    }

    public function reorder(Request $request, Test $test)
    {
        $parameters = $request->input('parameters', []);
        
        foreach ($parameters as $param) {
            TestParameter::where('id', $param['id'])
                ->where('test_id', $test->id)
                ->update([
                    'sort_order' => $param['sort_order'],
                    'group_name' => $param['group_name'] ?? null,
                ]);
        }

        return response()->json(['success' => true]);
    }
}
