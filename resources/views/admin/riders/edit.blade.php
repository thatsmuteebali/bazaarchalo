@extends('layouts.admin')

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">Edit Rider</h1>
            <p class="page-subtitle">Update {{ $rider->name }}'s account details.</p>
        </div>
        <a href="{{ route('admin.riders.index') }}" class="btn-custom btn-custom-light">Back to Riders</a>
    </div>

    @include('admin.riders.form', ['isEdit' => true])
@endsection
