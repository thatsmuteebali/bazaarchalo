@extends('layouts.admin')

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">Edit Collection Settings</h1>
            <p class="page-subtitle">Review {{ $collection->name }} and change its status or featured visibility.</p>
        </div>
        <a href="{{ route('admin.collections.index') }}" class="btn-custom btn-custom-light">Back to Collections</a>
    </div>

    <form method="POST" action="{{ route('admin.collections.update', $collection) }}">
        @csrf
        @method('PUT')
        <div class="card border-light shadow-sm p-4">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label-custom">Collection Name</label>
                    <input type="text" class="form-control-custom" value="{{ $collection->name ?: 'N/A' }}" readonly>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label-custom">Seller</label>
                    <input type="text" class="form-control-custom" value="{{ $collection->seller?->name ?: 'N/A' }}" readonly>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="form-label-custom">Slug</label>
                    <input type="text" class="form-control-custom" value="{{ $collection->slug ?: 'N/A' }}" readonly>
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label-custom">Image</label>
                    <div class="mb-2">
                        <img src="{{ $collection->image ? asset('storage/' . $collection->image) : asset('assets/images/no_image.png') }}"
                            alt="{{ $collection->name }}" style="max-width: 220px; max-height: 120px; object-fit: contain;"
                            onerror="this.src='{{ asset('assets/images/no_image.png') }}'">
                    </div>
                    <input type="text" class="form-control-custom" value="{{ $collection->image ?: 'N/A' }}" readonly>
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label-custom">Description</label>
                    <textarea class="form-control-custom" rows="4" readonly>{{ $collection->description ?: 'N/A' }}</textarea>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="status" class="form-label-custom">Status</label>
                    <select name="status" id="status"
                        class="form-select-custom @error('status') is-invalid-custom @enderror" required>
                        @foreach (['pending' => 'Pending', 'active' => 'Active', 'inactive' => 'Inactive'] as $value => $label)
                            <option value="{{ $value }}" @selected(old('status', $collection->status) === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('status')
                        <div class="form-feedback-custom invalid-custom">
                            <i class="bi bi-exclamation-circle-fill"></i> {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label-custom d-block">Featured Collection</label>
                    <input type="hidden" name="is_featured" value="0">
                    <div class="form-switch-custom">
                        <input name="is_featured" value="1"
                            @checked((bool) old('is_featured', $collection->is_featured))
                            class="form-switch-input-custom" type="checkbox" id="is_featured">
                        <label class="form-switch-label" for="is_featured">Show as featured collection</label>
                    </div>
                    @error('is_featured')
                        <div class="form-feedback-custom invalid-custom">
                            <i class="bi bi-exclamation-circle-fill"></i> {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <button class="btn-custom btn-custom-primary" type="submit">Update Collection Settings</button>
                <a href="{{ route('admin.collections.index') }}" class="btn-custom btn-custom-light">Cancel</a>
            </div>
        </div>
    </form>
@endsection
