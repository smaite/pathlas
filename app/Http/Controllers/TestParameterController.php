<?php

namespace App\Http\Controllers;

use App\Http\Requests\TestParameters\StoreTestParameterRequest;
use App\Http\Requests\TestParameters\UpdateTestParameterRequest;
use App\Http\Requests\TestParameters\ReorderTestParametersRequest;
use App\Models\Test;
use App\Models\TestParameter;
use Illuminate\Http\Request;

class TestParameterController extends Controller
{
    public function store(StoreTestParameterRequest $request, Test $test)
    {
        $validated = $request->validated();

        // If formula is provided, mark as calculated
        $validated['is_calculated'] = !empty($validated['formula']);

        $validated['test_id'] = $test->id;
        $validated['is_active'] = true;
        
        if (!isset($validated['sort_order'])) {
            $validated['sort_order'] = $test->parameters()->max('sort_order') + 1;
        }

        TestParameter::create($validated);

        return redirect()->route('tests.show', $test)
            ->with('success', 'Parameter added successfully.');
    }

    public function update(UpdateTestParameterRequest $request, Test $test, TestParameter $parameter)
    {
        $validated = $request->validated();

        // If formula is provided, mark as calculated
        $validated['is_calculated'] = !empty($validated['formula']);

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

    public function reorder(ReorderTestParametersRequest $request, Test $test)
    {
        $validated = $request->validated();
        $parameters = $validated['parameters'];

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
