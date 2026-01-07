<?php

namespace App\Http\Controllers;

use App\Http\Requests\Tests\StoreTestRequest;
use App\Http\Requests\Tests\UpdateTestRequest;
use App\Http\Requests\Tests\StoreCategoryRequest;
use App\Http\Requests\Tests\UpdateCategoryRequest;
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

    public function store(StoreTestRequest $request)
    {
        $validated = $request->validated();

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

    public function update(UpdateTestRequest $request, Test $test)
    {
        $validated = $request->validated();

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

    public function storeCategory(StoreCategoryRequest $request)
    {
        $validated = $request->validated();

        TestCategory::create($validated);

        return redirect()->route('tests.categories')
            ->with('success', 'Category created successfully.');
    }

    public function updateCategory(UpdateCategoryRequest $request, TestCategory $category)
    {
        $validated = $request->validated();

        $category->update($validated);

        return redirect()->route('tests.categories')
            ->with('success', 'Category updated successfully.');
    }
}
