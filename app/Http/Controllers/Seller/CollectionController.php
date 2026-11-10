<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class CollectionController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $status = $request->input('status');
            $query = auth()->user()->collections()
                ->select(['id', 'name', 'image', 'slug', 'status', 'deleted_at']);

            if ($status === 'trashed') {
                $query->onlyTrashed();
            }

            return DataTables::eloquent($query)
                ->filter(function ($query) use ($status) {
                    if (in_array($status, ['pending', 'active', 'inactive'], true)) {
                        $query->where('status', $status);
                    }
                }, true)
                ->addIndexColumn()
                ->editColumn('image', function (Collection $collection) {
                    $imageUrl = $collection->image
                        ? asset('storage/'.$collection->image)
                        : asset('assets/images/no_image.png');

                    return '<div class="table-user-cell"><img src="'.e($imageUrl).'" alt="'.e($collection->name)
                        .'" class="table-user-avatar" onerror="this.src=\''
                        .e(asset('assets/images/no_image.png')).'\'"></div>';
                })
                ->editColumn('status', function (Collection $collection) {
                    $statusLabel = match ($collection->status) {
                        'active' => 'success',
                        'inactive' => 'failed',
                        default => 'pending',
                    };

                    return '<span class="badge-table '.$statusLabel.'">'
                        .e(ucfirst($collection->status)).'</span>';
                })
                ->addColumn('actions', function (Collection $collection) {
                    $name = e($collection->name);

                    if ($collection->trashed()) {
                        $restoreUrl = route('seller.collections.restore', $collection);
                        $forceUrl = route('seller.collections.force-delete', $collection);

                        return '<div class="d-flex justify-content-center gap-1">'
                            .'<form method="POST" action="'.e($restoreUrl).'">'
                            .csrf_field().method_field('PATCH')
                            .'<button type="submit" class="table-btn-action restore" title="Restore collection" aria-label="Restore '
                            .$name.'"><i class="bi bi-arrow-counterclockwise"></i></button></form>'
                            .'<form method="POST" action="'.e($forceUrl).'">'
                            .csrf_field().method_field('DELETE')
                            .'<button type="button" class="table-btn-action delete" title="Delete permanently" aria-label="Delete '
                            .$name.' permanently" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal" data-confirm-delete'
                            .' data-item-name="'.$name.'" data-item-type="collection">'
                            .'<i class="bi bi-trash"></i></button></form></div>';
                    }

                    $showUrl = route('seller.collections.show', $collection);
                    $editUrl = route('seller.collections.edit', $collection);
                    $deleteUrl = route('seller.collections.destroy', $collection);

                    return '<div class="d-flex justify-content-center gap-1">'
                        .'<a href="'.e($showUrl).'" class="table-btn-action" title="View collection" aria-label="View '
                        .$name.'"><i class="bi bi-eye"></i></a>'
                        .'<a href="'.e($editUrl).'" class="table-btn-action" title="Edit collection" aria-label="Edit '
                        .$name.'"><i class="bi bi-pencil"></i></a>'
                        .'<form method="POST" action="'.e($deleteUrl).'">'
                        .csrf_field().method_field('DELETE')
                        .'<button type="button" class="table-btn-action delete" title="Delete collection" aria-label="Delete '
                        .$name.'" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal" data-confirm-delete'
                        .' data-item-name="'.$name.'" data-item-type="collection">'
                        .'<i class="bi bi-trash"></i></button></form></div>';
                })
                ->removeColumn('id', 'deleted_at')
                ->rawColumns(['image', 'status', 'actions'])
                ->toJson();
        }

        return view('seller.collections.index');
    }

    public function create()
    {
        return view('seller.collections.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:10000'],
            'image' => ['required', 'image', 'max:4096'],
            'slug' => ['required', 'string', 'max:255', 'unique:collections,slug','regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/'],
        ],[
            'slug.regex' => 'Slug must contain only lowercase letters, numbers, and single hyphens (e.g. apple-iphone-15).',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('collections', 'public');
        }

        auth()->user()->collections()->create($validated + [
            'status' => 'pending',
            'is_featured' => false,
        ]);

        return redirect()->route('seller.collections.index')->with('success', 'Collection created successfully.');
    }

    public function show(Request $request, Collection $collection)
    {
        $this->ensureOwnership($collection);

        return view('seller.collections.show', [
            'collection' => $collection,
            'search' => $request->query('search', ''),
            'page' => $request->query('page', 1),
        ]);
    }

    public function edit(Request $request, Collection $collection)
    {
        $this->ensureOwnership($collection);

        return view('seller.collections.edit', [
            'collection' => $collection,
            'search' => $request->query('search', ''),
            'page' => $request->query('page', 1),
        ]);
    }

    public function update(Request $request, Collection $collection)
    {
        $this->ensureOwnership($collection);
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:10000'],
            'image' => ['nullable', 'image', 'max:4096'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('collections', 'slug')->ignore($collection->id),'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/'],
        ],[
            'slug.regex' => 'Slug must contain only lowercase letters, numbers, and single hyphens (e.g. apple-iphone-15).',
        ]);
        $oldImage = $collection->image;

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('collections', 'public');
        }

        $collection->update($validated);

        if ($oldImage && isset($validated['image'])) {
            Storage::disk('public')->delete($oldImage);
        }

        return redirect()
            ->route('seller.collections.index', $request->only('search', 'page'))
            ->with('success', 'Collection updated successfully.');
    }

    public function destroy(Request $request, Collection $collection)
    {
        $this->ensureOwnership($collection);
        $collection->delete();

        return redirect()
            ->route('seller.collections.index', $request->only('search', 'page'))
            ->with('success', 'Collection deleted successfully.');
    }

    public function restore(Collection $collection)
    {
        $this->ensureOwnership($collection);
        $collection->restore();

        return redirect()
            ->route('seller.collections.index')
            ->with('success', 'Collection restored successfully.');
    }

    public function forceDelete(Collection $collection)
    {
        $this->ensureOwnership($collection);
        $image = $collection->image;
        $collection->forceDelete();

        if ($image) {
            Storage::disk('public')->delete($image);
        }

        return redirect()
            ->route('seller.collections.index')
            ->with('success', 'Collection permanently deleted.');
    }

    private function ensureOwnership(Collection $collection): void
    {
        abort_unless((int) $collection->seller_id === (int) auth()->id(), 404);
    }
}
