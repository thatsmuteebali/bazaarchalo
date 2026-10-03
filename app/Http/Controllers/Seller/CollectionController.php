<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class CollectionController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search', ''));

        $collections = auth()->user()->collections()
            ->when($search !== '', fn ($query) => $query->where('name', 'like', "%{$search}%"))
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('seller.collections.index', compact('collections', 'search'));
    }

    public function create()
    {
        return view('seller.collections.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->rules($request));

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('collections', 'public');
        }

        auth()->user()->collections()->create($validated);

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
        $validated = $request->validate($this->rules($request));
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

        if ($collection->image) {
            Storage::disk('public')->delete($collection->image);
        }

        $collection->delete();

        return redirect()
            ->route('seller.collections.index', $request->only('search', 'page'))
            ->with('success', 'Collection deleted successfully.');
    }

    private function rules(Request $request): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:10000'],
            'image' => ['nullable', 'image', 'max:4096'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ];
    }

    private function ensureOwnership(Collection $collection): void
    {
        abort_unless((int) $collection->seller_id === (int) auth()->id(), 404);
    }
}
