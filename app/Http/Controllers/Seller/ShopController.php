<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search', ''));
        $status = $request->query('status', '');

        $shops = auth()->user()->shops()
            ->when($search !== '', fn ($query) => $query->where('name', 'like', "%{$search}%"))
            ->when(in_array($status, ['active', 'inactive'], true), fn ($query) => $query->where('status', $status))
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('seller.shops.index', compact('shops', 'search', 'status'));
    }

    public function create()
    {
        return view('seller.shops.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->rules());

        if ($request->hasFile('banner')) {
            $validated['banner'] = $request->file('banner')->store('shops', 'public');
        }

        auth()->user()->shops()->create($validated);

        return redirect()->route('seller.shops.index')->with('success', 'Shop created successfully.');
    }

    public function show(Shop $shop)
    {
        $this->ensureOwnership($shop);

        return view('seller.shops.show', compact('shop'));
    }

    public function edit(Shop $shop)
    {
        $this->ensureOwnership($shop);

        return view('seller.shops.edit', compact('shop'));
    }

    public function update(Request $request, Shop $shop)
    {
        $this->ensureOwnership($shop);
        $validated = $request->validate($this->rules());

        if ($request->hasFile('banner')) {
            if ($shop->banner) {
                Storage::disk('public')->delete($shop->banner);
            }
            $validated['banner'] = $request->file('banner')->store('shops', 'public');
        }

        $shop->update($validated);

        return redirect()->route('seller.shops.index')->with('success', 'Shop updated successfully.');
    }

    public function destroy(Request $request, Shop $shop)
    {
        $this->ensureOwnership($shop);
        if ($shop->banner) {
            Storage::disk('public')->delete($shop->banner);
        }
        $shop->delete();

        return redirect()
            ->route('seller.shops.index', $request->only('search', 'status', 'page'))
            ->with('success', 'Shop deleted successfully.');
    }

    private function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'banner' => ['nullable', 'image', 'max:4096'],
            'address' => ['nullable', 'string', 'max:5000'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'is_primary' => ['nullable', 'boolean'],
        ];
    }

    private function ensureOwnership(Shop $shop): void
    {
        abort_unless((int) $shop->seller_id === (int) auth()->id(), 404);
    }
}
