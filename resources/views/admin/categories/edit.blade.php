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

    <form method="POST"
        action="{{ route('admin.categories.update', array_merge(['category' => $category], array_filter(['search' => $search, 'page' => $page], fn($value) => $value !== null && $value !== ''))) }}"
        enctype="multipart/form-data" id="category-form">
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

                <div class="col-md-6 mb-3">
                    <label for="slug" class="form-label-custom">Slug</label>
                    <input type="text" name="slug" id="slug"
                        class="form-control-custom @error('slug') is-invalid-custom @enderror" value="{{ $category->slug }}"
                        maxlength="255" readonly>
                    <small class="text-muted">Generated automatically from the category name.</small>
                    @error('slug')
                        <div class="form-feedback-custom invalid-custom">
                            <i class="bi bi-exclamation-circle-fill"></i> {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="col-md-12 mb-3">
                    <label for="description" class="form-label-custom">Description</label>
                    <textarea name="description" id="description" rows="2" maxlength="255"
                        class="form-control-custom @error('description') is-invalid-custom @enderror">{{ old('description', $category->description) }}</textarea>
                    @error('description')
                        <div class="form-feedback-custom invalid-custom"><i class="bi bi-exclamation-circle-fill"></i>
                            {{ $message }}</div>
                    @enderror
                </div>


                <div class="col-md-12 mb-3">
                    <label for="image" class="form-label-custom">Category Image</label>
                    @if ($category->image)
                        <div class="mb-2">
                            <img class="image-preview" src="{{ asset('storage/' . $category->image) }}"
                                alt="{{ $category->name }}" style="width: 96px; height: 72px; object-fit: cover;">
                        </div>
                    @endif
                    <input type="file" name="image" id="image" accept="image/*"
                        class="form-control-custom image @error('image') is-invalid-custom @enderror">
                    <small class="text-muted">Leave empty to keep the current image. Maximum file size: 2 MB.</small>
                    @error('image')
                        <div class="form-feedback-custom invalid-custom">
                            <i class="bi bi-exclamation-circle-fill"></i> {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
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
            </div>

            <div class="d-flex flex-wrap gap-2 mb-4">
                <button class="btn-custom btn-custom-primary" type="submit">Update</button>
                <a href="{{ route('admin.categories.list', array_filter(['search' => $search, 'page' => $page], fn($value) => $value !== null && $value !== '')) }}"
                    class="btn-custom btn-custom-light">Cancel</a>
            </div>
        </div>
    </form>
@endsection

@section('customjs')
    <script>
        const categoryNameInput = document.getElementById('name');
        const categorySlugInput = document.getElementById('slug');

        function updateCategorySlug() {
            categorySlugInput.value = categoryNameInput.value
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '')
                .toLowerCase()
                .replace(/[^a-z0-9]+/g, '-')
                .replace(/^-|-$/g, '')
                .slice(0, 255);
        }

        categoryNameInput.addEventListener('input', updateCategorySlug);
        updateCategorySlug();
    </script>

    <script>
        $(document).ready(function() {
            $("#category-form").on("submit", function() {
                $('button[type="submit"]').prop('disabled', true);
            });
        });
    </script>
@endsection
