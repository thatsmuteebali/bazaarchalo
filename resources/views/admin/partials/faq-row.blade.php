@php
    $questionKey = 'faqs.' . $index . '.question';
    $answerKey = 'faqs.' . $index . '.answer';
@endphp

<div class="faq-row border rounded p-3 mb-3 position-relative">
    <button type="button" class="btn-close position-absolute top-0 end-0 m-2 js-remove-faq" aria-label="Remove"></button>

    <input type="hidden" name="faqs[{{ $index }}][id]" value="{{ $id }}">

    <div class="mb-3">
        <label class="form-label-custom">Question</label>
        <input type="text" name="faqs[{{ $index }}][question]" value="{{ $question }}" maxlength="500"
            placeholder="Enter question" class="form-control-custom @error($questionKey) is-invalid-custom @enderror">
        @error($questionKey)
            <div class="form-feedback-custom invalid-custom d-block">
                <i class="bi bi-exclamation-circle-fill"></i> {{ $message }}
            </div>
        @enderror
    </div>

    <div>
        <label class="form-label-custom">Answer</label>
        <textarea name="faqs[{{ $index }}][answer]" rows="3" maxlength="5000" placeholder="Enter answer"
            class="form-control-custom @error($answerKey) is-invalid-custom @enderror">{{ $answer }}</textarea>
        @error($answerKey)
            <div class="form-feedback-custom invalid-custom d-block">
                <i class="bi bi-exclamation-circle-fill"></i> {{ $message }}
            </div>
        @enderror
    </div>
</div>
