<div class="mb-3">
    <label for="name" class="form-label-custom">Collection Name</label>
    <input type="text" name="name" id="name" maxlength="255" required autofocus
        value="{{ old('name', $collection->name ?? '') }}"
        class="form-control-custom @error('name') is-invalid-custom @enderror" placeholder="Enter collection name">
    @error('name')
        <div class="form-feedback-custom invalid-custom"><i class="bi bi-exclamation-circle-fill"></i> {{ $message }}
        </div>
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

<div class="mb-3">
    <label for="image" class="form-label-custom">Collection Image</label>
    @if (!empty($collection?->image))
        <div class="mb-2">
            <img src="{{ asset('storage/' . $collection->image) }}" alt="{{ $collection->name }}"
                style="width: 180px; height: 90px; object-fit: cover;">
        </div>
    @endif
    <input type="file" name="image" id="image" accept="image/*"
        class="form-control-custom @error('image') is-invalid-custom @enderror">
    <small class="text-muted">Optional. Maximum file size: 4 MB.</small>
    @error('image')
        <div class="form-feedback-custom invalid-custom"><i class="bi bi-exclamation-circle-fill"></i> {{ $message }}
        </div>
    @enderror
</div>

<div class="mb-3">
    <label for="status" class="form-label-custom">Status</label>
    <select name="status" id="status" class="form-select-custom @error('status') is-invalid-custom @enderror">
        <option value="active" @selected(old('status', $collection->status ?? 'active') === 'active')>Active
        </option>
        <option value="inactive" @selected(old('status', $collection->status ?? 'active') === 'inactive')>Inactive
        </option>
    </select>
    @error('status')
        <div class="form-feedback-custom invalid-custom"><i class="bi bi-exclamation-circle-fill"></i>
            {{ $message }}</div>
    @enderror
</div>
