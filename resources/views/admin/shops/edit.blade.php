@extends('layouts.admin')

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">Edit Shop Settings</h1>
            <p class="page-subtitle">Review {{ $shop->name }} and change its status or featured visibility.</p>
        </div>
        <a href="{{ route('admin.shops.index') }}" class="btn-custom btn-custom-light">Back to Shops</a>
    </div>

    <form method="POST" action="{{ route('admin.shops.update', $shop) }}">
        @csrf
        @method('PUT')
        <div class="card border-light shadow-sm p-4">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label-custom">Shop Name</label>
                    <input type="text" class="form-control-custom" value="{{ $shop->name ?: 'N/A' }}" readonly>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label-custom">Seller</label>
                    <input type="text" class="form-control-custom" value="{{ $shop->seller?->name ?: 'N/A' }}" readonly>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label-custom">Slug</label>
                    <input type="text" class="form-control-custom" value="{{ $shop->slug ?: 'N/A' }}" readonly>
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label-custom">Banner</label>
                    <div class="mb-2">
                        <img src="{{ $shop->banner ? asset('storage/' . $shop->banner) : asset('assets/images/no_image.png') }}"
                            alt="{{ $shop->name }}" style="max-width: 220px; max-height: 120px; object-fit: contain;"
                            onerror="this.src='{{ asset('assets/images/no_image.png') }}'">
                    </div>
                    <input type="text" class="form-control-custom"
                        value="{{ $shop->banner ?: 'N/A' }}" readonly>
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label-custom">Address</label>
                    <textarea class="form-control-custom" rows="2" readonly>{{ $shop->address ?: 'N/A' }}</textarea>
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label-custom">Description</label>
                    <textarea class="form-control-custom" rows="4" readonly>{{ $shop->description ?: 'N/A' }}</textarea>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="status" class="form-label-custom">Status</label>
                    <select name="status" id="status"
                        class="form-select-custom @error('status') is-invalid-custom @enderror" required>
                        @foreach (['pending' => 'Pending', 'active' => 'Active', 'inactive' => 'Inactive'] as $value => $label)
                            <option value="{{ $value }}" @selected(old('status', $shop->status) === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('status')
                        <div class="form-feedback-custom invalid-custom">
                            <i class="bi bi-exclamation-circle-fill"></i> {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label-custom d-block">Featured Shop</label>
                    <input type="hidden" name="is_feature_shop" value="0">
                    <div class="form-switch-custom">
                        <input name="is_feature_shop" value="1"
                            @checked((bool) old('is_feature_shop', $shop->is_feature_shop))
                            class="form-switch-input-custom" type="checkbox" id="is_feature_shop">
                        <label class="form-switch-label" for="is_feature_shop">Show as featured shop</label>
                    </div>
                    @error('is_feature_shop')
                        <div class="form-feedback-custom invalid-custom">
                            <i class="bi bi-exclamation-circle-fill"></i> {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label-custom">Primary Shop</label>
                    <input type="text" class="form-control-custom" value="{{ $shop->is_primary ? 'Yes' : 'No' }}" readonly>
                </div>
                
            </div>
            <div class="d-flex flex-wrap gap-2">
                <button class="btn-custom btn-custom-primary" type="submit">Update Shop Settings</button>
                <a href="{{ route('admin.shops.index') }}" class="btn-custom btn-custom-light">Cancel</a>
            </div>
        </div>
    </form>
@endsection
