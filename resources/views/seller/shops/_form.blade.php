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
<div class="mb-3">
    <label for="banner" class="form-label-custom">Shop Banner</label>

        <div class="mb-2">
            <img src="{{ !empty($shop?->banner) ? asset('storage/' . $shop->banner) : asset('assets/images/no_image.png') }}" alt="{{ !empty($shop?->banner) ? $shop->name : 'shop image' }}" class="image-preview"
                style="width: 180px; height: 90px; object-fit: contain;">
        </div>
    <input type="file" name="banner" id="banner" accept="image/*"
        class="form-control-custom image @error('banner') is-invalid-custom @enderror">
    <small class="text-muted">Optional. Maximum file size: 4 MB.</small>
    @error('banner')
        <div class="form-feedback-custom invalid-custom"><i class="bi bi-exclamation-circle-fill"></i> {{ $message }}
        </div>
    @enderror
</div>

<div class="col-md-6 mb-3">
    <label for="name" class="form-label-custom">Shop Name</label>
    <input type="text" name="name" id="name" maxlength="255" required
        value="{{ old('name', $shop->name ?? '') }}"
        class="form-control-custom @error('name') is-invalid-custom @enderror" placeholder="Enter shop name">
    @error('name')
        <div class="form-feedback-custom invalid-custom"><i class="bi bi-exclamation-circle-fill"></i> {{ $message }}
        </div>
    @enderror
</div>

<div class="col-md-6 mb-3">
    <label for="slug" class="form-label-custom">Slug</label>
    <input type="text" name="slug"
        class="form-control-custom @error('slug') is-invalid-custom @enderror" id="slug"
        value="{{ old('slug', $shop->slug ?? '') }}" placeholder="eg: shop-name" maxlength="255">
    @error('slug')
        <div class="form-feedback-custom invalid-custom">
            <i class="bi bi-exclamation-circle-fill"></i> {{ $message }}
        </div>
    @enderror
</div>

<div class="mb-3">
    <label for="description" class="form-label-custom">Description</label>
    <textarea name="description" id="description" rows="4" maxlength="5000"
        class="form-control-custom @error('description') is-invalid-custom @enderror" placeholder="Describe your shop">{{ old('description', $shop->description ?? '') }}</textarea>
    @error('description')
        <div class="form-feedback-custom invalid-custom"><i class="bi bi-exclamation-circle-fill"></i> {{ $message }}
        </div>
    @enderror
</div>

<div class="mb-3">
    <label for="address" class="form-label-custom">Address</label>
    <textarea name="address" id="address" rows="3" maxlength="5000"
        class="form-control-custom @error('address') is-invalid-custom @enderror" placeholder="Enter shop address">{{ old('address', $shop->address ?? '') }}</textarea>
    @error('address')
        <div class="form-feedback-custom invalid-custom"><i class="bi bi-exclamation-circle-fill"></i> {{ $message }}
        </div>
    @enderror
</div>

<div class="col-md-6 mb-3">
    <input type="hidden" name="is_primary" value="0">
    <label for=""></label>
    <div class="form-switch-custom">
        <input name="is_primary" value="1" {{ old('is_primary', $shop->is_primary ?? false) ? 'checked' : '' }}
            class="form-switch-input-custom" type="checkbox" id="is_primary">
        <label class="form-switch-label" for="is_primary">Is Primary</label>
    </div>
</div>

@section('customjs')
    <script>
        $(document).ready(function() {
            $("#shop-form").on("submit", function() {
                $('button[type="submit"]').prop('disabled', true);
            });
        });
    </script>
@endsection
