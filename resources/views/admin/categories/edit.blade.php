@extends('layouts.admin')
@section('customCss')
@endsection

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">Edit Category</h1>
            <p class="page-subtitle">Update the category name.</p>
        </div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"
                        class="text-decoration-none text-muted-green">Dashboard</a></li>
                <li class="breadcrumb-item"><a
                        href="{{ route('admin.categories.list', array_filter(['search' => $search, 'page' => $page], fn($value) => $value !== null && $value !== '')) }}"
                        class="text-decoration-none text-muted-green">Categories</a></li>
                <li class="breadcrumb-item active text-main" aria-current="page">Edit</li>
            </ol>
        </nav>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-12">
            <form method="POST"
                action="{{ route('admin.categories.update', array_merge(['category' => $category], array_filter(['search' => $search, 'page' => $page], fn($value) => $value !== null && $value !== ''))) }}"
                enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="card border-light shadow-sm p-4 h-100">
                    <div class="mb-3">
                        <label for="name" class="form-label-custom">Category Name</label>
                        <input type="text" name="name"
                            class="form-control-custom @error('name') is-invalid-custom @enderror" id="name"
                            value="{{ old('name', $category->name) }}" placeholder="Enter Category Name" maxlength="255"
                            required autofocus>
                        @error('name')
                            <div class="form-feedback-custom invalid-custom">
                                <i class="bi bi-exclamation-circle-fill"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class=" mb-3">
                        <label for="description" class="form-label-custom">Description</label>
                        <textarea name="description" id="description" rows="2" maxlength="255"
                            class="form-control-custom @error('description') is-invalid-custom @enderror">{{ old('description', $category->description) }}</textarea>
                        @error('description')
                            <div class="form-feedback-custom invalid-custom"><i class="bi bi-exclamation-circle-fill"></i>
                                {{ $message }}</div>
                        @enderror
                    </div>


                    <div class="mb-3">
                        <label for="image" class="form-label-custom">Category Image</label>
                        @if ($category->image)
                            <div class="mb-2">
                                <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }}"
                                    style="width: 96px; height: 72px; object-fit: cover;">
                            </div>
                        @endif
                        <input type="file" name="image" id="image" accept="image/*"
                            class="form-control-custom @error('image') is-invalid-custom @enderror">
                        <small class="text-muted">Leave empty to keep the current image. Maximum file size: 2 MB.</small>
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
                            <option value="active" @selected(old('status', $category->status ?? 'active') === 'active')>Active
                            </option>
                            <option value="inactive" @selected(old('status', $category->status ?? 'active') === 'inactive')>Inactive
                            </option>
                        </select>
                        @error('status')
                            <div class="form-feedback-custom invalid-custom"><i class="bi bi-exclamation-circle-fill"></i>
                                {{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex flex-wrap gap-2 mb-4">
                        <button class="btn-custom btn-custom-primary" type="submit">Update</button>
                        <a href="{{ route('admin.categories.list', array_filter(['search' => $search, 'page' => $page], fn($value) => $value !== null && $value !== '')) }}"
                            class="btn-custom btn-custom-light">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('customjs')
@endsection
