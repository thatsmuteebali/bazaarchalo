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
    <label for="image" class="form-label-custom">Collection Image</label>
    <div class="mb-2">
        <img src="{{ !empty($collection?->image) ? asset('storage/' . $collection->image) : asset('assets/images/no_image.png') }}"
            alt="{{ !empty($collection?->image) ? $collection->name : 'collection image' }}" class="image-preview"
            style="width: 180px; height: 90px; object-fit: contain;">
    </div>
    <input type="file" name="image" id="image" accept="image/*"
        class="form-control-custom image @error('image') is-invalid-custom @enderror">
    <small class="text-muted">Optional. Maximum file size: 4 MB.</small>
    @error('image')
        <div class="form-feedback-custom invalid-custom"><i class="bi bi-exclamation-circle-fill"></i> {{ $message }}</div>
    @enderror
</div>

<div class="col-md-6 mb-3">
    <label for="name" class="form-label-custom">Collection Name</label>
    <input type="text" name="name" id="name" maxlength="255" required
        value="{{ old('name', $collection->name ?? '') }}"
        class="form-control-custom @error('name') is-invalid-custom @enderror" placeholder="Enter collection name">
    @error('name')
        <div class="form-feedback-custom invalid-custom"><i class="bi bi-exclamation-circle-fill"></i> {{ $message }}
        </div>
    @enderror
</div>

<div class="col-md-6 mb-3">
    <label for="slug" class="form-label-custom">Slug</label>
    <input type="text" name="slug" id="slug" maxlength="255"
        value="{{ old('slug', $collection->slug ?? '') }}"
        class="form-control-custom @error('slug') is-invalid-custom @enderror" placeholder="eg: collection-name">
    @error('slug')
        <div class="form-feedback-custom invalid-custom"><i class="bi bi-exclamation-circle-fill"></i> {{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="description" class="form-label-custom">Description</label>
    <textarea name="description" id="description" rows="4" maxlength="10000"
        class="form-control-custom @error('description') is-invalid-custom @enderror"
        placeholder="Describe this collection">{{ old('description', $collection->description ?? '') }}</textarea>
    @error('description')
        <div class="form-feedback-custom invalid-custom"><i class="bi bi-exclamation-circle-fill"></i> {{ $message }}
        </div>
    @enderror
</div>

@section('customjs')
    <script>
        $(document).ready(function() {
            $("#collection-form").on("submit", function() {
                $('button[type="submit"]').prop('disabled', true);
            });
        });
    </script>
@endsection
