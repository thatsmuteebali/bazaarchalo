<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class RiderController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $status = $request->input('status');
            $query = User::query()
                ->where('role', 'rider')
                ->select(['id', 'name', 'username', 'email', 'phone', 'image', 'status']);

            return DataTables::eloquent($query)
                ->filter(function ($query) use ($status) {
                    if (in_array($status, ['Active', 'Inactive'], true)) {
                        $query->where('status', $status);
                    }
                }, true)
                ->addIndexColumn()
                ->editColumn('image', function (User $rider) {
                    $imageUrl = $rider->image
                        ? asset('storage/'.$rider->image)
                        : asset('assets/images/no_image.png');

                    return '<div class="table-user-cell"><img src="'.e($imageUrl).'" alt="'.e($rider->name)
                        .'" class="table-user-avatar" onerror="this.src=\''
                        .e(asset('assets/images/no_image.png')).'\'"></div>';
                })
                ->editColumn('status', function (User $rider) {
                    $statusClass = $rider->status === 'Active' ? 'success' : 'failed';

                    return '<span class="badge-table '.$statusClass.'">'.e($rider->status).'</span>';
                })
                ->addColumn('actions', function (User $rider) {
                    $key = $this->encryptedKey($rider);
                    $showUrl = route('admin.riders.show', ['riderKey' => $key]);
                    $editUrl = route('admin.riders.edit', ['riderKey' => $key]);
                    $deleteUrl = route('admin.riders.destroy', ['riderKey' => $key]);
                    $name = e($rider->name);

                    return '<div class="d-flex justify-content-center gap-1">'
                        .'<a href="'.e($showUrl).'" class="table-btn-action" title="View rider" aria-label="View '.$name
                        .'"><i class="bi bi-eye"></i></a>'
                        .'<a href="'.e($editUrl).'" class="table-btn-action" title="Edit rider" aria-label="Edit '.$name
                        .'"><i class="bi bi-pencil"></i></a>'
                        .'<form method="POST" action="'.e($deleteUrl).'">'
                        .csrf_field().method_field('DELETE')
                        .'<button type="button" class="table-btn-action delete" title="Delete rider" aria-label="Delete '
                        .$name.'" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal" data-confirm-delete'
                        .' data-item-name="'.$name.'" data-item-type="rider">'
                        .'<i class="bi bi-trash"></i></button></form></div>';
                })
                ->removeColumn('id')
                ->rawColumns(['image', 'status', 'actions'])
                ->toJson();
        }

        return view('admin.riders.index');
    }

    public function create()
    {
        return view('admin.riders.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'image' => ['nullable', 'image', 'max:2048'],
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:50', 'alpha_dash', 'unique:users,username'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['required', 'string', 'unique:users,phone', 'regex:/^03\d{9}$/'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'status' => ['required', Rule::in(['Active', 'Inactive'])],
        ], [
            'phone.regex' => 'Enter a Pakistani mobile number in the format 03000000000.',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('riders', 'public');
        }

        $validated['role'] = 'rider';
        User::create($validated);

        return redirect()->route('admin.riders.index')->with('success', 'Rider added successfully.');
    }

    public function show(string $riderKey)
    {
        $rider = $this->findRider($riderKey);

        return view('admin.riders.show', compact('rider', 'riderKey'));
    }

    public function edit(string $riderKey)
    {
        $rider = $this->findRider($riderKey);

        return view('admin.riders.edit', compact('rider', 'riderKey'));
    }

    public function update(Request $request, string $riderKey)
    {
        $rider = $this->findRider($riderKey);
        $validated = $request->validate([
            'image' => ['nullable', 'image', 'max:2048'],
            'name' => ['required', 'string', 'max:255'],
            'username' => [
                'required', 'string', 'max:50', 'alpha_dash',
                Rule::unique('users', 'username')->ignore($rider->id),
            ],
            'email' => [
                'required', 'string', 'email', 'max:255',
                Rule::unique('users', 'email')->ignore($rider->id),
            ],
            'phone' => [
                'required', 'string', 'regex:/^03\d{9}$/',
                Rule::unique('users', 'phone')->ignore($rider->id),
            ],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'status' => ['required', Rule::in(['Active', 'Inactive'])],
            'remove_image' => ['nullable', 'boolean'],
        ], [
            'phone.regex' => 'Enter a Pakistani mobile number in the format 03000000000.',
        ]);

        $oldImage = $rider->image;
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('riders', 'public');
        } elseif ($request->boolean('remove_image')) {
            $validated['image'] = null;
        }

        if (empty($validated['password'])) {
            unset($validated['password']);
        }

        unset($validated['remove_image']);
        $rider->update($validated);

        if ($oldImage && $oldImage !== $rider->image) {
            Storage::disk('public')->delete($oldImage);
        }

        return redirect()->route('admin.riders.index')->with('success', 'Rider updated successfully.');
    }

    public function destroy(string $riderKey)
    {
        $rider = $this->findRider($riderKey);

        if ($rider->image) {
            Storage::disk('public')->delete($rider->image);
        }
        $rider->delete();

        return redirect()->route('admin.riders.index')->with('success', 'Rider deleted successfully.');
    }

    private function findRider(string $riderKey): User
    {
        try {
            $id = Crypt::decryptString(strtr($riderKey, '-_', '+/'));
        } catch (DecryptException) {
            abort(404);
        }

        return User::query()->where('role', 'rider')->findOrFail($id);
    }

    private function encryptedKey(User $rider): string
    {
        return rtrim(strtr(Crypt::encryptString((string) $rider->getKey()), '+/', '-_'), '=');
    }
}
