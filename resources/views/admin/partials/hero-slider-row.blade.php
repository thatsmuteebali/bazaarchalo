@php
    $nameKey = 'sliders.' . $index . '.name';
    $imageKey = 'sliders.' . $index . '.image';
@endphp

<div class="slider-row border rounded p-3 mb-3 position-relative">
    <button type="button" class="btn-close position-absolute top-0 end-0 m-2 js-remove-slider"
        aria-label="Remove"></button>

    <input type="hidden" name="sliders[{{ $index }}][id]" value="{{ $id }}">

    <div class="row">
        <div class="col-md-6 mb-3 mb-md-0">
            <label class="form-label-custom">Name</label>
            <input type="text" name="sliders[{{ $index }}][name]" value="{{ $name }}" maxlength="255"
                placeholder="Enter slider name"
                class="form-control-custom @error($nameKey) is-invalid-custom @enderror">
            @error($nameKey)
                <div class="form-feedback-custom invalid-custom d-block">
                    <i class="bi bi-exclamation-circle-fill"></i> {{ $message }}
                </div>
            @enderror
        </div>

        <div class="col-md-6">
            <label class="form-label-custom">Slider Image</label>
            
            <input type="file" name="sliders[{{ $index }}][image]" accept="image/*"
                class="form-control-custom js-slider-image @error($imageKey) is-invalid-custom @enderror">
            <small class="text-muted">
                {{ $id ? 'Leave empty to keep the current image.' : 'Required.' }} Maximum file size: 4 MB.
            </small>
            <div class="mb-2">
                <img class="slider-preview" src="{{ $imageUrl }}" alt="Slider image"
                    style="width: 96px; height: 72px; object-fit: cover;">
            </div>
            @error($imageKey)
                <div class="form-feedback-custom invalid-custom d-block">
                    <i class="bi bi-exclamation-circle-fill"></i> {{ $message }}
                </div>
            @enderror
        </div>
    </div>
</div>