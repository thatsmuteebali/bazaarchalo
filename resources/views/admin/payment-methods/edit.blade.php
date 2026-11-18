@extends('layouts.admin')

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">Edit Payment Method</h1>
            <p class="page-subtitle">Update this customer checkout option.</p>
        </div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-muted-green">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.payment-methods.list', array_filter(['search' => $search, 'page' => $page], fn ($value) => $value !== null && $value !== '')) }}" class="text-decoration-none text-muted-green">Payment Methods</a></li>
                <li class="breadcrumb-item active text-main" aria-current="page">Edit</li>
            </ol>
        </nav>
    </div>

    <form method="POST"
        action="{{ route('admin.payment-methods.update', array_merge(['paymentMethod' => $paymentMethod], array_filter(['search' => $search, 'page' => $page], fn ($value) => $value !== null && $value !== ''))) }}"
        id="payment-method-form">
        @csrf
        @method('PUT')
        <div class="card border-light shadow-sm p-4 h-100">
            <div class="row">
                @if ($errors->any())
                    <div class="alert alert-danger py-2 mt-3">
                        <strong>There are errors with some fields. Please fix them and try again.</strong>
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li class="small">{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="col-md-6 mb-3">
                    <label for="name" class="form-label-custom">Payment Method Name</label>
                    <input type="text" name="name" id="name" maxlength="100" required autofocus
                        value="{{ old('name', $paymentMethod->name) }}"
                        class="form-control-custom @error('name') is-invalid-custom @enderror">
                    @error('name')
                        <div class="form-feedback-custom invalid-custom"><i class="bi bi-exclamation-circle-fill"></i> {{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="slug" class="form-label-custom">Slug</label>
                    <input type="text" name="slug" id="slug" maxlength="30" required
                        value="{{ old('slug', $paymentMethod->slug) }}"
                        class="form-control-custom @error('slug') is-invalid-custom @enderror">
                    <small class="text-muted">Use <code>cod</code> for Cash on Delivery; other slugs are treated as online payments.</small>
                    @error('slug')
                        <div class="form-feedback-custom invalid-custom"><i class="bi bi-exclamation-circle-fill"></i> {{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="status" class="form-label-custom">Status</label>
                    <select name="status" id="status" class="form-select-custom @error('status') is-invalid-custom @enderror">
                        <option value="active" @selected(old('status', $paymentMethod->status) === 'active')>Active</option>
                        <option value="inactive" @selected(old('status', $paymentMethod->status) === 'inactive')>Inactive</option>
                    </select>
                    @error('status')
                        <div class="form-feedback-custom invalid-custom"><i class="bi bi-exclamation-circle-fill"></i> {{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="d-flex flex-wrap gap-2 mb-4">
                <button class="btn-custom btn-custom-primary" type="submit">Update</button>
                <a href="{{ route('admin.payment-methods.list', array_filter(['search' => $search, 'page' => $page], fn ($value) => $value !== null && $value !== '')) }}" class="btn-custom btn-custom-light">Cancel</a>
            </div>
        </div>
    </form>
@endsection

@section('customjs')
    <script>
        const methodNameInput = document.getElementById('name');
        const methodSlugInput = document.getElementById('slug');
        let methodSlugManuallyEdited = false;

        methodNameInput.addEventListener('input', function () {
            if (!methodSlugManuallyEdited) {
                methodSlugInput.value = methodNameInput.value
                    .normalize('NFD')
                    .replace(/[\u0300-\u036f]/g, '')
                    .toLowerCase()
                    .replace(/[^a-z0-9]+/g, '-')
                    .replace(/^-|-$/g, '')
                    .slice(0, 30);
            }
        });
        methodSlugInput.addEventListener('input', function () {
            methodSlugManuallyEdited = true;
        });

        $('#payment-method-form').on('submit', function () {
            $(this).find('button[type="submit"]').prop('disabled', true);
        });
    </script>
@endsection
