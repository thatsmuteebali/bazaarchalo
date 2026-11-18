<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class PaymentMethodController extends Controller
{
    public function list(Request $request)
    {
        if ($request->ajax()) {
            $status = $request->input('status');
            $query = PaymentMethod::query()->select(['id', 'name', 'slug', 'status', 'deleted_at']);

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
                ->editColumn('status', function (PaymentMethod $paymentMethod) {
                    $statusLabel = match ($paymentMethod->status) {
                        'active' => 'success',
                        'inactive' => 'failed',
                        default => 'pending',
                    };

                    return '<span class="badge-table '.$statusLabel.'">'
                        .e(ucfirst($paymentMethod->status)).'</span>';
                })
                ->addColumn('actions', function (PaymentMethod $paymentMethod) {
                    $name = e($paymentMethod->name);

                    if ($paymentMethod->trashed()) {
                        $restoreUrl = route('admin.payment-methods.restore', $paymentMethod);
                        $forceUrl = route('admin.payment-methods.force-delete', $paymentMethod);

                        return '<div class="d-flex justify-content-center gap-1">'
                            .'<form method="POST" action="'.e($restoreUrl).'">'
                            .csrf_field().method_field('PATCH')
                            .'<button type="submit" class="table-btn-action restore" title="Restore payment method" aria-label="Restore '
                            .$name.'"><i class="bi bi-arrow-counterclockwise"></i></button></form>'
                            .'<form method="POST" action="'.e($forceUrl).'">'
                            .csrf_field().method_field('DELETE')
                            .'<button type="button" class="table-btn-action delete" title="Delete permanently" aria-label="Delete '
                            .$name.' permanently" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal" data-confirm-delete'
                            .' data-item-name="'.$name.'" data-item-type="payment method">'
                            .'<i class="bi bi-trash"></i></button></form></div>';
                    }

                    $showUrl = route('admin.payment-methods.show', $paymentMethod);
                    $editUrl = route('admin.payment-methods.edit', $paymentMethod);
                    $deleteUrl = route('admin.payment-methods.destroy', $paymentMethod);

                    return '<div class="d-flex justify-content-center gap-1">'
                        .'<a href="'.e($showUrl).'" class="table-btn-action" title="View payment method" aria-label="View '
                        .$name.'"><i class="bi bi-eye"></i></a>'
                        .'<a href="'.e($editUrl).'" class="table-btn-action" title="Edit payment method" aria-label="Edit '
                        .$name.'"><i class="bi bi-pencil"></i></a>'
                        .'<form method="POST" action="'.e($deleteUrl).'">'
                        .csrf_field().method_field('DELETE')
                        .'<button type="button" class="table-btn-action delete" title="Delete payment method" aria-label="Delete '
                        .$name.'" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal" data-confirm-delete'
                        .' data-item-name="'.$name.'" data-item-type="payment method">'
                        .'<i class="bi bi-trash"></i></button></form></div>';
                })
                ->removeColumn('id', 'deleted_at')
                ->rawColumns(['status', 'actions'])
                ->toJson();
        }

        return view('admin.payment-methods.index');
    }

    public function create()
    {
        return view('admin.payment-methods.create');
    }

    public function store(Request $request)
    {
        $this->normalize($request);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'slug' => ['required', 'string', 'max:30', 'alpha_dash', 'unique:payment_methods,slug'],
            'status' => ['required', 'in:active,inactive'],
        ], [
            'slug.unique' => 'This payment method slug already exists.',
        ]);

        PaymentMethod::create($validated);

        return redirect()
            ->route('admin.payment-methods.list')
            ->with('success', 'Payment method added successfully.');
    }

    public function show(PaymentMethod $paymentMethod)
    {
        return view('admin.payment-methods.show', compact('paymentMethod'));
    }

    public function edit(Request $request, PaymentMethod $paymentMethod)
    {
        return view('admin.payment-methods.edit', [
            'paymentMethod' => $paymentMethod,
            'search' => $request->query('search'),
            'page' => $request->query('page'),
        ]);
    }

    public function update(Request $request, PaymentMethod $paymentMethod)
    {
        $this->normalize($request);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'slug' => [
                'required',
                'string',
                'max:30',
                'alpha_dash',
                Rule::unique('payment_methods', 'slug')->ignore($paymentMethod->id),
            ],
            'status' => ['required', 'in:active,inactive'],
        ], [
            'slug.unique' => 'This payment method slug already exists.',
        ]);

        $paymentMethod->update($validated);

        return redirect()
            ->route('admin.payment-methods.list', $request->only('search', 'page'))
            ->with('success', 'Payment method updated successfully.');
    }

    public function destroy(Request $request, PaymentMethod $paymentMethod)
    {
        $paymentMethod->delete();

        return redirect()
            ->route('admin.payment-methods.list', $request->only('search', 'page'))
            ->with('success', 'Payment method moved to trash.');
    }

    public function restore(PaymentMethod $paymentMethod)
    {
        $paymentMethod->restore();

        return redirect()
            ->route('admin.payment-methods.list')
            ->with('success', 'Payment method restored successfully.');
    }

    public function forceDelete(PaymentMethod $paymentMethod)
    {
        $paymentMethod->forceDelete();

        return redirect()
            ->route('admin.payment-methods.list')
            ->with('success', 'Payment method permanently deleted.');
    }

    private function normalize(Request $request): void
    {
        foreach (['name', 'slug'] as $field) {
            $value = $request->input($field);
            if (is_string($value)) {
                $request->merge([$field => $field === 'slug' ? Str::lower(trim($value)) : trim($value)]);
            }
        }
    }
}
