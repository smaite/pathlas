<?php

namespace App\Http\Controllers;

use App\Models\Test;
use App\Models\TestCategory;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class TestController extends Controller
{
    public function index(Request $request)
    {
        $query = Test::with('category')->withCount('parameters')->latest();

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $tests = $query->paginate(20);
        $categories = TestCategory::active()->get();

        return view('tests.index', compact('tests', 'categories'));
    }

    public function create()
    {
        $categories = TestCategory::active()->get();
        return view('tests.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:test_categories,id',
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:tests,code',
            'short_name' => 'nullable|string|max:50',
            'unit' => 'nullable|string|max:50',
            'normal_range_male' => 'nullable|string',
            'normal_range_female' => 'nullable|string',
            'normal_min' => 'nullable|numeric',
            'normal_max' => 'nullable|numeric',
            'price' => 'required|numeric|min:0',
            'sample_type' => 'required|in:blood,urine,stool,swab,other',
            'method' => 'nullable|string',
            'instructions' => 'nullable|string',
            'turnaround_time' => 'integer|min:1',
        ]);

        $test = Test::create($validated);
        ActivityLog::log('test_created', $test, [], $validated);

        return redirect()->route('tests.index')
            ->with('success', 'Test created successfully.');
    }

    public function edit(Test $test)
    {
        $test->load(['parameters' => fn($q) => $q->ordered()]);
        $categories = TestCategory::active()->get();
        return view('tests.edit', compact('test', 'categories'));
    }

    public function show(Test $test)
    {
        $test->load(['category', 'parameters' => fn($q) => $q->ordered()]);
        return view('tests.show', compact('test'));
    }

    public function update(Request $request, Test $test)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:test_categories,id',
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:tests,code,' . $test->id,
            'short_name' => 'nullable|string|max:50',
            'unit' => 'nullable|string|max:50',
            'normal_range_male' => 'nullable|string',
            'normal_range_female' => 'nullable|string',
            'normal_min' => 'nullable|numeric',
            'normal_max' => 'nullable|numeric',
            'price' => 'required|numeric|min:0',
            'sample_type' => 'nullable|in:blood,urine,stool,swab,other',
            'method' => 'nullable|string',
            'instructions' => 'nullable|string',
            'interpretation' => 'nullable|string',
            'turnaround_time' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
        ]);

        $oldValues = $test->toArray();
        $test->update($validated);
        ActivityLog::log('test_updated', $test, $oldValues, $validated);

        // Return JSON for AJAX requests
        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Test updated']);
        }

        return redirect()->route('tests.index')
            ->with('success', 'Test updated successfully.');
    }

    public function destroy(Test $test)
    {
        ActivityLog::log('test_deleted', $test, $test->toArray());
        $test->delete();

        return redirect()->route('tests.index')
            ->with('success', 'Test deleted successfully.');
    }

    public function search(Request $request)
    {
        $search = $request->get('q', '');
        
        $tests = Test::where('is_active', true)
            ->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            })
            ->with('category:id,name')
            ->take(20)
            ->get(['id', 'category_id', 'name', 'code', 'price', 'unit']);

        return response()->json($tests);
    }

    // Category management
    public function categories()
    {
        $categories = TestCategory::withCount('tests')->get();
        return view('tests.categories', compact('categories'));
    }

    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:test_categories,code',
            'description' => 'nullable|string',
        ]);

        TestCategory::create($validated);

        return redirect()->route('tests.categories')
            ->with('success', 'Category created successfully.');
    }

    public function updateCategory(Request $request, TestCategory $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:test_categories,code,' . $category->id,
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $category->update($validated);

        return redirect()->route('tests.categories')
            ->with('success', 'Category updated successfully.');
    }
}
