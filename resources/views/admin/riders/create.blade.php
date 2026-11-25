@extends('layouts.admin')

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">Add Rider</h1>
            <p class="page-subtitle">Create a rider account.</p>
        </div>
        <a href="{{ route('admin.riders.index') }}" class="btn-custom btn-custom-light">Back to Riders</a>
    </div>

    @include('admin.riders.form', ['isEdit' => false])
@endsection
