@extends('layouts.admin')
@section('customCss')
@endsection

@section('content')
    <!-- START: Page Header Banner -->
    <div class="page-header">
        <div>
            <h1 class="page-title">Add Category</h1>
            <p class="page-subtitle">Product Categories.</p>
        </div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"
                        class="text-decoration-none text-muted-green">Dashboard</a>
                </li>
                <li class="breadcrumb-item"><a href="{{ route('admin.categories.list') }}"
                        class="text-decoration-none text-muted-green">Categories</a>
                </li>
                <li class="breadcrumb-item active text-main" aria-current="page">Add</li>
            </ol>
        </nav>
    </div>

    <div class="row g-4 mb-4">

        <!-- Column 1: Basic controls -->
        <div class="col-12 col-lg-12">
            <form method="POST" action="{{ route('admin.categories.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="card border-light shadow-sm p-4 h-100">
                    <!-- Text input -->
                    <div class="mb-3">
                        <label for="name" class="form-label-custom">Category Name</label>
                        <input type="text" name="name"
                            class="form-control-custom @error('name') is-invalid-custom @enderror" id="name"
                            value="{{ old('name') }}" placeholder="Enter Category Name" maxlength="255" required
                            autofocus>
                        @error('name')
                            <div class="form-feedback-custom invalid-custom">
                                <i class="bi bi-exclamation-circle-fill"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class=" mb-3">
                        <label for="description" class="form-label-custom">Description</label>
                        <textarea name="description" id="description" rows="2" maxlength="255"
                            class="form-control-custom @error('description') is-invalid-custom @enderror">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="form-feedback-custom invalid-custom"><i class="bi bi-exclamation-circle-fill"></i>
                                {{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="image" class="form-label-custom">Category Image</label>
                        <input type="file" name="image" id="image" accept="image/*"
                            class="form-control-custom @error('image') is-invalid-custom @enderror">
                        <small class="text-muted">Optional. Maximum file size: 2 MB.</small>
                        @error('image')
                            <div class="form-feedback-custom invalid-custom">
                                <i class="bi bi-exclamation-circle-fill"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class=" mb-3">
                        <label for="status" class="form-label-custom">Status</label>
                        <select name="status" id="status"
                            class="form-select-custom @error('status') is-invalid-custom @enderror">
                            <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Active
                            </option>
                            <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive
                            </option>
                        </select>
                        @error('status')
                            <div class="form-feedback-custom invalid-custom"><i class="bi bi-exclamation-circle-fill"></i>
                                {{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex flex-wrap gap-2 mb-4">
                        <button class="btn-custom btn-custom-primary" type="submit">Save</button>
                        <a href="{{ route('admin.categories.list') }}" class="btn-custom btn-custom-light">Cancel</a>
                    </div>
                </div>
            </form>
        </div>

    </div>
@endsection

@section('customjs')
@endsection
