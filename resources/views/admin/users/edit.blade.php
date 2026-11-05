@extends('layouts.admin')

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">Edit {{ $title }} Status</h1>
            <p class="page-subtitle">Update the account status for {{ $user->name }}.</p>
        </div>
        <a href="{{ route($indexRoute) }}" class="btn-custom btn-custom-light">Back to {{ $title }}s</a>
    </div>

    <form method="POST" action="{{ route($updateRoute, ['userKey' => $userKey]) }}">
        @csrf
        @method('PUT')
        <div class="card border-light shadow-sm p-4">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label-custom">Name</label>
                    <input type="text" class="form-control-custom" value="{{ $user->name ?: 'N/A' }}" disabled>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label-custom">Email</label>
                    <input type="text" class="form-control-custom" value="{{ $user->email ?: 'N/A' }}" disabled>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="status" class="form-label-custom">Status</label>
                    <select name="status" id="status"
                        class="form-select-custom @error('status') is-invalid-custom @enderror" required>
                        <option value="Active" @selected(old('status', $user->status) === 'Active')>Active</option>
                        <option value="Inactive" @selected(old('status', $user->status) === 'Inactive')>Inactive</option>
                    </select>
                    @error('status')
                        <div class="form-feedback-custom invalid-custom">
                            <i class="bi bi-exclamation-circle-fill"></i> {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <button class="btn-custom btn-custom-primary" type="submit">Update Status</button>
                <a href="{{ route($indexRoute) }}" class="btn-custom btn-custom-light">Cancel</a>
            </div>
        </div>
    </form>
@endsection
