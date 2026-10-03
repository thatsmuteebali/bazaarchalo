<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    public function list(Request $request)
    {
        $search = trim((string) $request->query('search', ''));

        $categories = Category::query()
            ->when($search !== '', fn ($query) => $query->where('name', 'like', "%{$search}%"))
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('admin.categories.index', compact('categories', 'search'));
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        $this->trimName($request);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:categories,name'],
            'image' => ['nullable', 'image', 'max:2048'],
            'status' => ['required', 'in:active,inactive'],
            'description' => ['nullable', 'string'],
        ], [
            'name.unique' => 'This category already exists.',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('categories', 'public');
        }

        Category::create($validated);

        return redirect()
            ->route('admin.categories.list')
            ->with('success', 'Category added successfully');
    }

    public function show(Category $category)
    {
        return view('admin.categories.show', [
            'category' => $category
        ]);
    }


    public function edit(Request $request, Category $category)
    {
        return view('admin.categories.edit', [
            'category' => $category,
            'search' => $request->query('search'),
            'page' => $request->query('page'),
        ]);
    }

    public function update(Request $request, Category $category)
    {
        $this->trimName($request);

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('categories', 'name')->ignore($category->id),
            ],
            'image' => ['nullable', 'image', 'max:2048'],
            'status' => ['required', 'in:active,inactive'],
            'description' => ['nullable', 'string'],
        ], [
            'name.unique' => 'This category already exists.',
        ]);

        if ($request->hasFile('image')) {
            if ($category->image) {
                Storage::disk('public')->delete($category->image);
            }
            $validated['image'] = $request->file('image')->store('categories', 'public');
        }

        $category->update($validated);

        return redirect()
            ->route('admin.categories.list', $request->only('search', 'page'))
            ->with('success', 'Category updated successfully');
    }

    public function destroy(Request $request, Category $category)
    {
        if ($category->image) {
            Storage::disk('public')->delete($category->image);
        }
        $category->delete();

        return redirect()
            ->route('admin.categories.list', $request->only('search', 'page'))
            ->with('success', 'Category deleted successfully');
    }

    private function trimName(Request $request): void
    {
        $name = $request->input('name');

        if (is_string($name)) {
            $request->merge(['name' => trim($name)]);
        }
    }
}
