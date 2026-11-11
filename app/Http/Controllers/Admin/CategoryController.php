<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Services\MarketplaceNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class CategoryController extends Controller
{
    public function list(Request $request)
    {
        if ($request->ajax()) {
            $status = $request->input('status');

            // deleted_at must be selected so $category->trashed() works
            $query = Category::query()->select(['id', 'name', 'image', 'status', 'deleted_at']);

            if ($status === 'trashed') {
                $query->onlyTrashed();
            }

            return DataTables::eloquent($query)
                ->filter(function ($query) use ($status) {
                    if (in_array($status, ['active', 'inactive'], true)) {
                        $query->where('status', $status);
                    }
                }, true)
                ->addIndexColumn()
                ->editColumn('image', function (Category $category) {
                    $imageUrl = $category->image
                        ? asset('storage/' . $category->image)
                        : asset('assets/images/no_image.png');
                    $name = e($category->name);

                    return '<div class="table-user-cell"><img src="' . e($imageUrl) . '" alt="' . $name
                        . '" class="table-user-avatar" onerror="this.src=\''
                        . e(asset('assets/images/no_image.png')) . '\'"></div>';
                })
                ->editColumn('status', function (Category $category) {
                    $statusLabel = match ($category->status) {
                        'active' => 'success',
                        'inactive' => 'failed',
                        default => 'pending',
                    };

                    return '<span class="badge-table ' . $statusLabel . '">'
                        . e(ucfirst($category->status)) . '</span>';
                })
                ->addColumn('actions', function (Category $category) {
                    $name = e($category->name);

                    // Trashed rows: Restore + Delete permanently
                    if ($category->trashed()) {
                        $restoreUrl = route('admin.categories.restore', $category);
                        $forceUrl = route('admin.categories.force-delete', $category);

                        return '<div class="d-flex justify-content-center gap-1">'
                            . '<form method="POST" action="' . e($restoreUrl) . '">'
                            . csrf_field() . method_field('PATCH')
                            . '<button type="submit" class="table-btn-action restore" title="Restore category" aria-label="Restore '
                            . $name . '"><i class="bi bi-arrow-counterclockwise"></i></button></form>'
                            . '<form method="POST" action="' . e($forceUrl) . '">'
                            . csrf_field() . method_field('DELETE')
                            . '<button type="button" class="table-btn-action delete" title="Delete permanently" aria-label="Delete '
                            . $name . ' permanently" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal" data-confirm-delete'
                            . ' data-item-name="' . $name . '" data-item-type="category">'
                            . '<i class="bi bi-trash"></i></button></form></div>';
                    }

                    $showUrl = route('admin.categories.show', $category);
                    $editUrl = route('admin.categories.edit', $category);
                    $deleteUrl = route('admin.categories.destroy', $category);

                    return '<div class="d-flex justify-content-center gap-1">'
                        . '<a href="' . e($showUrl) . '" class="table-btn-action" title="View category" aria-label="View '
                        . $name . '"><i class="bi bi-eye"></i></a>'
                        . '<a href="' . e($editUrl) . '" class="table-btn-action" title="Edit category" aria-label="Edit '
                        . $name . '"><i class="bi bi-pencil"></i></a>'
                        . '<form method="POST" action="' . e($deleteUrl) . '">'
                        . csrf_field() . method_field('DELETE')
                        . '<button type="button" class="table-btn-action delete" title="Delete category" aria-label="Delete '
                        . $name . '" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal" data-confirm-delete'
                        . ' data-item-name="' . $name . '" data-item-type="category">'
                        . '<i class="bi bi-trash"></i></button></form></div>';
                })
                ->removeColumn('id', 'deleted_at')   // never send the real id to the browser
                ->rawColumns(['image', 'status', 'actions'])
                ->toJson();
        }

        return view('admin.categories.index');
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request, MarketplaceNotificationService $notifications)
    {
        $this->trimName($request);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:categories,name'],
            'image' => ['required', 'image', 'max:2048'],
            'status' => ['required', 'in:active,inactive'],
            'description' => ['nullable', 'string'],
        ], [
            'name.unique' => 'This category already exists.',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('categories', 'public');
        }

        $category = Category::create($validated);

        if ($category->status === 'active') {
            $notifications->notifyActiveSellersOfCategory($category);
        }

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

    public function update(
        Request $request,
        Category $category,
        MarketplaceNotificationService $notifications,
    )
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

        $wasActive = $category->status === 'active';
        $category->update($validated);

        if (! $wasActive && $category->status === 'active') {
            $notifications->notifyActiveSellersOfCategory($category);
        }

        return redirect()
            ->route('admin.categories.list', $request->only('search', 'page'))
            ->with('success', 'Category updated successfully');
    }

    /** Soft delete: the image is kept so the category can be restored. */
    public function destroy(Request $request, Category $category)
    {
        $category->delete();

        return redirect()
            ->route('admin.categories.list', $request->only('search', 'page'))
            ->with('success', 'Category deleted successfully');
    }

    public function restore(Category $category)
    {
        $category->restore();

        return redirect()
            ->route('admin.categories.list')
            ->with('success', 'Category restored successfully');
    }

    /** Permanent delete: removes the row and its image. */
    public function forceDelete(Category $category)
    {
        if ($category->image) {
            Storage::disk('public')->delete($category->image);
        }
        $category->forceDelete();

        return redirect()
            ->route('admin.categories.list')
            ->with('success', 'Category permanently deleted');
    }

    private function trimName(Request $request): void
    {
        $name = $request->input('name');

        if (is_string($name)) {
            $request->merge(['name' => trim($name)]);
        }
    }
}
