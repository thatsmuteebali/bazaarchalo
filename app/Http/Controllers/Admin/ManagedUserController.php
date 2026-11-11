<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\AccountStatusMail;
use App\Models\User;
use App\Notifications\AccountStatusNotification;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class ManagedUserController extends Controller
{
    public function sellers(Request $request)
    {
        return $this->index($request, 'seller', 'admin.sellers.index', 'Sellers');
    }

    public function customers(Request $request)
    {
        return $this->index($request, 'customer', 'admin.customers.index', 'Customers');
    }

    public function showSeller(string $userKey)
    {
        return $this->show($userKey, 'seller', 'admin.sellers.edit', 'admin.sellers.index', 'Seller');
    }

    public function editSeller(string $userKey)
    {
        return $this->edit($userKey, 'seller', 'admin.sellers.update', 'admin.sellers.index', 'Seller');
    }

    public function updateSeller(Request $request, string $userKey)
    {
        return $this->update($request, $userKey, 'seller', 'admin.sellers.index', 'Seller');
    }

    public function showCustomer(string $userKey)
    {
        return $this->show($userKey, 'customer', 'admin.customers.edit', 'admin.customers.index', 'Customer');
    }

    public function editCustomer(string $userKey)
    {
        return $this->edit($userKey, 'customer', 'admin.customers.update', 'admin.customers.index', 'Customer');
    }

    public function updateCustomer(Request $request, string $userKey)
    {
        return $this->update($request, $userKey, 'customer', 'admin.customers.index', 'Customer');
    }

    private function index(Request $request, string $role, string $routeName, string $title)
    {
        if ($request->ajax()) {
            $status = $request->input('status');
            $query = User::query()
                ->where('role', $role)
                ->select(['id', 'name', 'username', 'email', 'phone', 'image', 'status']);

            return DataTables::eloquent($query)
                ->filter(function ($query) use ($status) {
                    if (in_array($status, ['Active', 'Inactive'], true)) {
                        $query->where('status', $status);
                    }
                }, true)
                ->addIndexColumn()
                ->editColumn('image', function (User $user) {
                    $imageUrl = $user->image
                        ? asset('storage/'.$user->image)
                        : asset('assets/images/no_image.png');

                    return '<div class="table-user-cell"><img src="'.e($imageUrl).'" alt="'.e($user->name)
                        .'" class="table-user-avatar" onerror="this.src=\''
                        .e(asset('assets/images/no_image.png')).'\'"></div>';
                })
                ->editColumn('username', fn (User $user) => $user->username ?: 'N/A')
                ->editColumn('email', fn (User $user) => $user->email ?: 'N/A')
                ->editColumn('phone', fn (User $user) => $user->phone ?: 'N/A')
                ->editColumn('status', function (User $user) {
                    $statusClass = $user->status === 'Active' ? 'success' : 'failed';

                    return '<span class="badge-table '.$statusClass.'">'.e($user->status).'</span>';
                })
                ->addColumn('actions', function (User $user) use ($role) {
                    $key = $this->encryptedKey($user);
                    $showUrl = route("admin.{$role}s.show", ['userKey' => $key]);
                    $editUrl = route("admin.{$role}s.edit", ['userKey' => $key]);
                    $name = e($user->name);

                    return '<div class="d-flex justify-content-center gap-1">'
                        .'<a href="'.e($showUrl).'" class="table-btn-action" title="View user" aria-label="View '.$name
                        .'"><i class="bi bi-eye"></i></a>'
                        .'<a href="'.e($editUrl).'" class="table-btn-action" title="Edit status" aria-label="Edit status for '.$name
                        .'"><i class="bi bi-pencil"></i></a></div>';
                })
                ->removeColumn('id')
                ->rawColumns(['image', 'status', 'actions'])
                ->toJson();
        }

        return view('admin.users.index', compact('routeName', 'title'));
    }

    private function show(string $userKey, string $role, string $editRoute, string $indexRoute, string $title)
    {
        $user = $this->findUser($userKey, $role);

        return view('admin.users.show', compact('user', 'userKey', 'editRoute', 'indexRoute', 'title'));
    }

    private function edit(string $userKey, string $role, string $updateRoute, string $indexRoute, string $title)
    {
        $user = $this->findUser($userKey, $role);

        return view('admin.users.edit', compact('user', 'userKey', 'updateRoute', 'indexRoute', 'title'));
    }

    private function update(Request $request, string $userKey, string $role, string $indexRoute, string $title)
    {
        $user = $this->findUser($userKey, $role);
        $validated = $request->validate([
            'status' => ['required', Rule::in(['Active', 'Inactive'])],
        ]);

        $previousStatus = $user->status;
        $user->update($validated);

        if ($previousStatus !== $user->status) {
            $user->notify(new AccountStatusNotification($user->status));
            Mail::to($user->email)->queue((new AccountStatusMail($user, $user->status))->afterCommit());
        }

        return redirect()->route($indexRoute)->with('success', "{$title} status updated successfully.");
    }

    private function findUser(string $userKey, string $role): User
    {
        try {
            $id = Crypt::decryptString(strtr($userKey, '-_', '+/'));
        } catch (DecryptException) {
            abort(404);
        }

        return User::query()->where('role', $role)->findOrFail($id);
    }

    private function encryptedKey(User $user): string
    {
        return rtrim(strtr(Crypt::encryptString((string) $user->getKey()), '+/', '-_'), '=');
    }
}
