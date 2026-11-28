@php
    $reviewCount = $shop->reviews_count ?? 0;
    $averageRating = (float) ($shop->reviews_avg_rating ?? 0);
    $ratingLabel = $reviewCount > 0
        ? number_format($averageRating, 1) . ' out of 5 from ' . $reviewCount . ' ' . ($reviewCount === 1 ? 'review' : 'reviews')
        : 'No reviews yet';
@endphp
<span class="{{ $reviewCount > 0 ? 'text-primary' : 'text-muted' }}" aria-label="{{ $ratingLabel }}">
    <i class="fas fa-star {{ $reviewCount > 0 ? 'text-secondary' : '' }} me-1" aria-hidden="true"></i>
    @if ($reviewCount > 0)
        {{ number_format($averageRating, 1) }}
        <small class="text-muted">({{ $reviewCount }} {{ $reviewCount === 1 ? 'review' : 'reviews' }})</small>
    @else
        No reviews yet
    @endif
</span>
