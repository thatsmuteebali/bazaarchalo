<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Collection;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class CollectionController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $status = $request->input('status');
            $query = Collection::query()
                ->with('seller:id,name')
                ->select(['id', 'seller_id', 'name', 'image', 'slug', 'status', 'is_featured']);

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
                ->addColumn('seller_name', fn (Collection $collection) => $collection->seller?->name ?: 'N/A')
                ->editColumn('status', function (Collection $collection) {
                    $statusClass = match ($collection->status) {
                        'active' => 'success',
                        'inactive' => 'failed',
                        default => 'pending',
                    };

                    return '<span class="badge-table '.$statusClass.'">'
                        .e(ucfirst($collection->status)).'</span>';
                })
                ->editColumn('is_featured', fn (Collection $collection) => $collection->is_featured ? 'Yes' : 'No')
                ->addColumn('actions', function (Collection $collection) {
                    $showUrl = route('admin.collections.show', $collection);
                    $editUrl = route('admin.collections.edit', $collection);
                    $name = e($collection->name);

                    return '<div class="d-flex justify-content-center gap-1">'
                        .'<a href="'.e($showUrl).'" class="table-btn-action" title="View collection" aria-label="View '
                        .$name.'"><i class="bi bi-eye"></i></a>'
                        .'<a href="'.e($editUrl).'" class="table-btn-action" title="Edit collection settings" aria-label="Edit settings for '
                        .$name.'"><i class="bi bi-pencil"></i></a></div>';
                })
                ->removeColumn('id')
                ->rawColumns(['image', 'status', 'actions'])
                ->toJson();
        }

        return view('admin.collections.index');
    }

    public function show(Collection $collection)
    {
        $collection->load('seller:id,name,email');

        return view('admin.collections.show', compact('collection'));
    }

    public function edit(Collection $collection)
    {
        $collection->load('seller:id,name,email');

        return view('admin.collections.edit', compact('collection'));
    }

    public function update(Request $request, Collection $collection)
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['pending', 'active', 'inactive'])],
            'is_featured' => ['required', 'boolean'],
        ]);

        $collection->update($validated);

        return redirect()->route('admin.collections.index')->with('success', 'Collection settings updated successfully.');
    }
}
