@extends('layouts.admin')

@section('customCss')
    <style>
        .color-field {
            display: flex;
            height: 44px;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            overflow: hidden;
            background: #fff;
        }

        .color-field.is-invalid {
            border-color: #dc3545;
        }

        /* Left half: color swatch */
        .color-field .color-picker {
            flex: 1 1 50%;
            width: 50%;
            height: 100%;
            padding: 0;
            border: 0;
            border-radius: 0;
            cursor: pointer;
            background: none;
            -webkit-appearance: none;
            appearance: none;
        }

        .color-field .color-picker::-webkit-color-swatch-wrapper {
            padding: 0;
        }

        .color-field .color-picker::-webkit-color-swatch {
            border: 0;
        }

        .color-field .color-picker::-moz-color-swatch {
            border: 0;
        }

        /* Right half: hex text */
        .color-field .color-text {
            flex: 1 1 50%;
            width: 50%;
            border: 0;
            border-left: 1px solid #dee2e6;
            padding: 0 12px;
            font-family: monospace;
            text-transform: lowercase;
            outline: none;
        }
    </style>
@endsection

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">{{ $pageTitle }}</h1>
            <p class="page-subtitle">Manage website content.</p>
        </div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"
                        class="text-decoration-none text-muted-green">Dashboard</a></li>
                <li class="breadcrumb-item">Website</li>
                <li class="breadcrumb-item active text-main" aria-current="page">{{ $pageTitle }}</li>
            </ol>
        </nav>
    </div>

    <form method="POST" action="{{ route('admin.website.store') }}" enctype="multipart/form-data" id="webcontent-form">
        @csrf
        <input type="hidden" name="page" value="{{ $page }}">

        <div class="card border-light shadow-sm p-4 h-100">
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

            <div class="row">
                @if ($page == 'home')
                    @php
                        $existingSliders = $sliders->keyBy('id');
                        $sliderRows =
                            old('sliders') ??
                            $sliders->map(fn($s) => ['id' => $s->id, 'name' => $s->name])->values()->all();
                        $nextSliderIndex = count($sliderRows)
                            ? max(array_map('intval', array_keys($sliderRows))) + 1
                            : 0;
                    @endphp

                    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">Hero Section Slider Images</h5>
                        <button type="button" id="add-slider" class="btn-custom btn-custom-primary">
                            <i class="bi bi-plus-lg"></i> Add Hero Slider Image
                        </button>
                    </div>

                    <input type="hidden" name="has_sliders" value="1">

                    <div id="slider-list">
                        @foreach ($sliderRows as $index => $row)
                            @include('admin.partials.hero-slider-row', [
                                'index' => $index,
                                'id' => $row['id'] ?? '',
                                'name' => $row['name'] ?? '',
                                'imageUrl' =>
                                    !empty($row['id']) && $existingSliders->has($row['id'])
                                        ? $existingSliders[$row['id']]->image_url
                                        : asset('assets/images/no_image.png'),
                            ])
                        @endforeach
                    </div>

                    <p id="slider-empty" class="text-muted">No slider images added yet.</p>

                    <template id="slider-row-template">
                        @include('admin.partials.hero-slider-row', [
                            'index' => '__INDEX__',
                            'id' => '',
                            'name' => '',
                            'imageUrl' => asset('assets/images/no_image.png'),
                        ])
                    </template>
                @endif
                
                @forelse ($webcontents as $content)
                    @php
                        $inputId = 'content_' . $content->id;
                        $errorKey = ($content->type === 'image' ? 'images.' : 'contents.') . $content->id;
                        $col = in_array($content->type, ['textarea', 'editor']) ? 'col-md-12' : 'col-md-6';
                    @endphp

                    <div class="{{ $col }} mb-3">
                        <label for="{{ $inputId }}" class="form-label-custom">{{ $content->field }}</label>

                        @switch($content->type)
                            @case('image')
                                <div class="mb-2">
                                    <img id="preview_{{ $content->id }}" class="image-preview" src="{{ $content->image_url }}"
                                        alt="{{ $content->field }}" style="width: 96px; height: 72px; object-fit: contain;">
                                </div>
                                <input type="file" name="images[{{ $content->id }}]" id="{{ $inputId }}" accept="image/*"
                                    data-preview="preview_{{ $content->id }}"
                                    class="form-control-custom js-image-input @error($errorKey) is-invalid-custom @enderror">
                                <small class="text-muted">Optional. Maximum file size: 2 MB.</small>
                            @break

                            @case('color')
                                @php $colorValue = old('contents.' . $content->id, $content->value); @endphp
                                <div class="color-field @error($errorKey) is-invalid @enderror">
                                    <input type="color" class="color-picker" value="{{ $colorValue }}" aria-label="Pick color">
                                    <input type="text" name="contents[{{ $content->id }}]" id="{{ $inputId }}"
                                        value="{{ $colorValue }}" maxlength="7" placeholder="#000000" autocomplete="off"
                                        class="color-text">
                                </div>
                            @break

                            @case('textarea')
                                <textarea name="contents[{{ $content->id }}]" id="{{ $inputId }}" rows="3" maxlength="1000"
                                    class="form-control-custom @error($errorKey) is-invalid-custom @enderror">{{ old('contents.' . $content->id, $content->value) }}</textarea>
                            @break

                            @case('editor')
                                <textarea name="contents[{{ $content->id }}]" id="{{ $inputId }}"
                                    class="summernote @error($errorKey) is-invalid-custom @enderror">{{ old('contents.' . $content->id, $content->value) }}</textarea>
                            @break

                            @default
                                <input type="{{ in_array($content->type, ['url', 'email']) ? $content->type : 'text' }}"
                                    name="contents[{{ $content->id }}]" id="{{ $inputId }}"
                                    value="{{ old('contents.' . $content->id, $content->value) }}"
                                    class="form-control-custom @error($errorKey) is-invalid-custom @enderror">
                        @endswitch

                        @error($errorKey)
                            <div class="form-feedback-custom invalid-custom d-block">
                                <i class="bi bi-exclamation-circle-fill"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>
                    @empty
                        <div class="col-12">
                            <p class="text-muted mb-0">No content found for this page.</p>
                        </div>
                    @endforelse
                </div>

                @if ($webcontents->isNotEmpty())
                    <div class="d-flex flex-wrap gap-2 mb-4">
                        <button class="btn-custom btn-custom-primary" type="submit">Save</button>
                        <a href="{{ route('admin.website.setting', ['page' => $page]) }}"
                            class="btn-custom btn-custom-light">Cancel</a>
                    </div>
                @endif
            </div>
        </form>
    @endsection

    @section('customjs')
        <script>
            $(document).ready(function() {

                // Live image preview
                $('.js-image-input').on('change', function() {
                    const preview = document.getElementById(this.dataset.preview);
                    if (this.files && this.files[0] && preview) {
                        preview.src = URL.createObjectURL(this.files[0]);
                    }
                });

                // Prevent double submit
                $('#webcontent-form').on('submit', function() {
                    $('button[type="submit"]').prop('disabled', true);
                });

                // Color field: picker <-> hex text sync
                function normalizeHex(value) {
                    value = value.trim().toLowerCase();
                    if (value && value[0] !== '#') value = '#' + value;
                    if (/^#[0-9a-f]{3}$/.test(value)) {
                        value = '#' + value.slice(1).split('').map(c => c + c).join('');
                    }
                    return value;
                }

                $('.color-field').each(function() {
                    const $picker = $(this).find('.color-picker');
                    const $text = $(this).find('.color-text');

                    // Choosing a color shows its hex in the text input
                    $picker.on('input', function() {
                        $text.val(this.value);
                    });

                    // Typing or pasting a valid hex updates the swatch
                    $text.on('input', function() {
                        const hex = normalizeHex(this.value);
                        if (/^#[0-9a-f]{6}$/.test(hex)) {
                            $picker.val(hex);
                        }
                    });

                    // On leaving the field, clean up the value or restore the last valid color
                    $text.on('blur', function() {
                        const hex = normalizeHex(this.value);
                        $text.val(/^#[0-9a-f]{6}$/.test(hex) ? hex : $picker.val());
                        $picker.val($text.val());
                    });
                });

                @if ($page == 'home')
                    // ---------- Hero slider rows ----------
                    let sliderIndex = {{ $nextSliderIndex }};

                    function toggleSliderEmpty() {
                        $('#slider-empty').toggle($('#slider-list .slider-row').length === 0);
                    }

                    $('#add-slider').on('click', function() {
                        const template = document.getElementById('slider-row-template').innerHTML;
                        $('#slider-list').append(template.replace(/__INDEX__/g, sliderIndex++));
                        toggleSliderEmpty();
                    });

                    $(document).on('click', '.js-remove-slider', function() {
                        $(this).closest('.slider-row').remove();
                        toggleSliderEmpty();
                    });

                    $(document).on('change', '.js-slider-image', function() {
                        const $preview = $(this).closest('.slider-row').find('.slider-preview');
                        if (this.files && this.files[0]) {
                            $preview.attr('src', URL.createObjectURL(this.files[0]));
                        }
                    });

                    toggleSliderEmpty();
                @endif
            });
        </script>
    @endsection
