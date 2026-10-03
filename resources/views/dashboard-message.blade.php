@if (session('success'))
<!-- Alert Success -->
<div class="alert-custom alert-custom-success">
    <i class="bi bi-check-circle-fill alert-custom-icon"></i>
    <div class="alert-custom-content">
        <strong>Success:</strong> {{ session('success') }}
    </div>
    <button class="alert-custom-close" type="button" aria-label="Close" onclick="this.parentElement.remove();">
        <i class="bi bi-x-lg"></i>
    </button>
</div>
@endif

@if (session('error'))
<!-- Alert Danger -->
<div class="alert-custom alert-custom-danger">
    <i class="bi bi-exclamation-triangle-fill alert-custom-icon"></i>
    <div class="alert-custom-content">
        <strong>Error:</strong> {{ session('error') }}
    </div>
    <button class="alert-custom-close" type="button" aria-label="Close" onclick="this.parentElement.remove();">
        <i class="bi bi-x-lg"></i>
    </button>
</div>
@endif
