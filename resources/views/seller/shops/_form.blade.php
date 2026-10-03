<div class="mb-3">
    <label for="name" class="form-label-custom">Shop Name</label>
    <input type="text" name="name" id="name" maxlength="255" required autofocus
        value="{{ old('name', $shop->name ?? '') }}"
        class="form-control-custom @error('name') is-invalid-custom @enderror" placeholder="Enter shop name">
    @error('name')
        <div class="form-feedback-custom invalid-custom"><i class="bi bi-exclamation-circle-fill"></i> {{ $message }}
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
    <label for="banner" class="form-label-custom">Shop Banner</label>
    @if (!empty($shop?->banner))
        <div class="mb-2">
            <img src="{{ asset('storage/' . $shop->banner) }}" alt="{{ $shop->name }}"
                style="width: 180px; height: 90px; object-fit: cover;">
        </div>
    @endif
    <input type="file" name="banner" id="banner" accept="image/*"
        class="form-control-custom @error('banner') is-invalid-custom @enderror">
    <small class="text-muted">Optional. Maximum file size: 4 MB.</small>
    @error('banner')
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

<div class="row align-items-end">

    <div class="col-md-6 mb-3">
        <label for="status" class="form-label-custom">Status</label>
        <select name="status" id="status" class="form-select-custom @error('status') is-invalid-custom @enderror">
            <option value="active" @selected(old('status', $shop->status ?? 'active') === 'active')>Active
            </option>
            <option value="inactive" @selected(old('status', $shop->status ?? 'active') === 'inactive')>Inactive
            </option>
        </select>
        @error('status')
            <div class="form-feedback-custom invalid-custom"><i class="bi bi-exclamation-circle-fill"></i>
                {{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 mb-3">
        <input type="hidden" name="is_primary" value="0">
        <div class="form-switch-custom">
            <input name="is_primary" value="1" {{ old('is_primary', $shop->is_primary ?? false) ? 'checked' : '' }} class="form-switch-input-custom" type="checkbox" id="is_primary"
                >
            <label class="form-switch-label" for="is_primary">Is Primary</label>
        </div>
    </div>
</div>
