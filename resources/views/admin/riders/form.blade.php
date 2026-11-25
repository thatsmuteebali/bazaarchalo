<form method="POST"
    action="{{ $isEdit ? route('admin.riders.update', ['riderKey' => $riderKey]) : route('admin.riders.store') }}"
    enctype="multipart/form-data" id="rider-form">
    @csrf
    @if ($isEdit)
        @method('PUT')
    @endif
    <div class="card border-light shadow-sm p-4">
        @if ($errors->any())
            <div class="alert alert-danger py-2">
                <strong>There are errors with some fields. Please fix them and try again.</strong>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li class="small">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="image" class="form-label-custom">Image <span class="text-muted">(optional)</span></label>

                <input type="file" name="image" id="image" accept="image/*"
                    class="form-control-custom image @error('image') is-invalid-custom @enderror">
                <div class="mb-2">
                    <img class="image-preview"
                        src="{{ $isEdit && $rider->image ? asset('storage/'.$rider->image) : asset('assets/images/no_image.png') }}"
                        alt="{{ $isEdit ? $rider->name : 'No image selected' }}"
                        style="width: 96px; height: 96px; object-fit: cover; border-radius: 50%;">
                </div>
                <small class="text-muted">Maximum file size: 2 MB. Leave empty to keep the current image.</small>
                {{-- @if ($isEdit && $rider->image)
                    <div class="form-switch-custom mt-2">
                        <input type="checkbox" name="remove_image" id="remove_image" value="1"
                            class="form-switch-input-custom" @checked(old('remove_image'))>
                        <label class="form-switch-label" for="remove_image">Remove current image</label>
                    </div>
                @endif --}}
                @error('image')
                    <div class="form-feedback-custom invalid-custom"><i class="bi bi-exclamation-circle-fill"></i> {{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-6 mb-3">
                <label for="name" class="form-label-custom">Name</label>
                <input type="text" name="name" id="name"
                    class="form-control-custom @error('name') is-invalid-custom @enderror"
                    value="{{ old('name', $isEdit ? $rider->name : '') }}" maxlength="255" required autofocus>
                @error('name')
                    <div class="form-feedback-custom invalid-custom"><i class="bi bi-exclamation-circle-fill"></i> {{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6 mb-3">
                <label for="username" class="form-label-custom">Username</label>
                <input type="text" name="username" id="username"
                    class="form-control-custom @error('username') is-invalid-custom @enderror"
                    value="{{ old('username', $isEdit ? $rider->username : '') }}" maxlength="50" required>
                @error('username')
                    <div class="form-feedback-custom invalid-custom"><i class="bi bi-exclamation-circle-fill"></i> {{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6 mb-3">
                <label for="email" class="form-label-custom">Email</label>
                <input type="email" name="email" id="email"
                    class="form-control-custom @error('email') is-invalid-custom @enderror"
                    value="{{ old('email', $isEdit ? $rider->email : '') }}" maxlength="255" required>
                @error('email')
                    <div class="form-feedback-custom invalid-custom"><i class="bi bi-exclamation-circle-fill"></i> {{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6 mb-3">
                <label for="phone" class="form-label-custom">Phone</label>
                <input type="tel" name="phone" id="phone"
                    class="form-control-custom @error('phone') is-invalid-custom @enderror"
                    value="{{ old('phone', $isEdit ? $rider->phone : '') }}" placeholder="03000000000"
                    pattern="03[0-9]{9}" maxlength="11" required>
                <small class="text-muted">Note: phone no should be format (03000000000).</small>
                @error('phone')
                    <div class="form-feedback-custom invalid-custom"><i class="bi bi-exclamation-circle-fill"></i> {{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6 mb-3">
                <label for="password" class="form-label-custom">Password{{ $isEdit ? ' (leave blank to keep current)' : '' }}</label>
                <input type="password" name="password" id="password"
                    class="form-control-custom @error('password') is-invalid-custom @enderror"
                    autocomplete="new-password"
                    {{ $isEdit ? '' : 'required' }}>
                @error('password')
                    <div class="form-feedback-custom invalid-custom"><i class="bi bi-exclamation-circle-fill"></i> {{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6 mb-3">
                <label for="password_confirmation" class="form-label-custom">Confirm Password{{ $isEdit ? ' (if changing)' : '' }}</label>
                <input type="password" name="password_confirmation" id="password_confirmation"
                    class="form-control-custom" autocomplete="new-password" {{ $isEdit ? '' : 'required' }}>
            </div>

            <div class="col-md-6 mb-3">
                <label for="status" class="form-label-custom">Status</label>
                <select name="status" id="status"
                    class="form-select-custom @error('status') is-invalid-custom @enderror" required>
                    <option value="Active" @selected(old('status', $isEdit ? $rider->status : 'Active') === 'Active')>Active</option>
                    <option value="Inactive" @selected(old('status', $isEdit ? $rider->status : 'Active') === 'Inactive')>Inactive</option>
                </select>
                @error('status')
                    <div class="form-feedback-custom invalid-custom"><i class="bi bi-exclamation-circle-fill"></i> {{ $message }}</div>
                @enderror
            </div>

        </div>

        <div class="d-flex flex-wrap gap-2">
            <button class="btn-custom btn-custom-primary" type="submit">{{ $isEdit ? 'Update Rider' : 'Save Rider' }}</button>
            <a href="{{ route('admin.riders.index') }}" class="btn-custom btn-custom-light">Cancel</a>
        </div>
    </div>
</form>

@section('customjs')
    <script>
        $(function () {
            $('#rider-form').on('submit', function () {
                $(this).find('button[type="submit"]').prop('disabled', true);
            });
        });
    </script>
@endsection
