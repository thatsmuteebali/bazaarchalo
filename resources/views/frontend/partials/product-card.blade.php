@php
    $coverImage = $product->coverImage;
    $secondaryImage =
        $product->images->first(fn($image) => !$coverImage || $image->id !== $coverImage->id) ?? $coverImage;
    $imageUrl = $coverImage?->url ?? asset('img/fruite-item-1.jpg');
    $secondaryImageUrl = $secondaryImage?->url ?? $imageUrl;
    $displayPrice = $product->has_variants
        ? $product->variants_min_price ?? ($product->variants->min('price') ?? $product->price)
        : $product->price;
    $hasDiscount =
        !$product->has_variants && $product->compare_price && (float) $product->compare_price > (float) $product->price;
    $productUrl = route('frontend.product-detail', ['product' => $product->slug]);
    $reviewCount = $product->reviews_count ?? 0;
    $averageRating = (float) ($product->reviews_avg_rating ?? 0);
@endphp
<div class="product-card">
    @if ($hasDiscount)
        <span class="product-badge product-badge-discount">
            {{ round((1 - (float) $product->price / (float) $product->compare_price) * 100) }}% off
        </span>
    @endif

    @if ($product->has_variants)
        <span class="product-badge product-badge-discount">
            {{ $product->variants_count }} variants
        </span>
    @endif
    <button type="button" class="wishlist-button" aria-label="Add to wishlist">
        <i class="far fa-heart" aria-hidden="true"></i>
    </button>
    <div class="product-media">
        <a href="{{ $productUrl }}" class="product-image-link" aria-label="View {{ $product->name }}">
            <img src="{{ $imageUrl }}" class="product-image product-image-primary" alt="{{ $product->name }}" />
            <img src="{{ $secondaryImageUrl }}" class="product-image product-image-secondary" alt=""
                aria-hidden="true" />
        </a>
        @if ($product->has_variants)
            <a href="{{ $productUrl }}" class="product-cart-cta">
                <i class="fa fa-sliders-h" aria-hidden="true"></i> Choose options
            </a>
        @elseif ($product->stock > 0)
            <a href="{{ route('frontend.cart') }}" class="product-cart-cta" data-cart-add
                data-product-id="{{ $product->id }}" data-product-name="{{ $product->name }}"
                data-product-price="{{ $product->price }}" data-product-stock="{{ $product->stock }}"
                data-product-image="{{ $imageUrl }}">
                <i class="fa fa-shopping-bag" aria-hidden="true"></i> Add to Cart
            </a>
        @else
            <span class="product-cart-cta" aria-disabled="true">Sold out</span>
        @endif
    </div>
    <div class="product-body">
        <a href="{{ $productUrl }}" class="stretched-link product-title">{{ $product->name }}</a>
        @if ($product->category)
            <small class="text-muted">{{ $product->category->name }}</small>
        @endif
        <div class="product-meta">
            <div class="product-rating"
                aria-label="{{ $reviewCount > 0 ? number_format($averageRating, 1) . ' out of 5 stars from ' . $reviewCount . ' reviews' : 'No reviews yet' }}">
                <span aria-hidden="true">
                    @for ($star = 1; $star <= 5; $star++)
                        <i class="{{ $reviewCount > 0 && $star <= round($averageRating) ? 'fas' : 'far' }} fa-star"></i>
                    @endfor
                </span>
                @if ($reviewCount > 0)
                    <span class="text-muted ms-1" aria-hidden="true">
                        {{ number_format($averageRating, 1) }} ({{ $reviewCount }})
                    </span>
                @else
                    <span class="text-muted ms-1">No reviews</span>
                @endif
            </div>
            <div class="product-price">
                @if ($hasDiscount)
                    <span class="product-price-old">Rs.{{ number_format((float) $product->compare_price, 0) }}</span>
                @endif
                <span class="product-price-current">Rs.{{ number_format((float) $displayPrice, 0) }} @if ($product->has_variants && $product->variants_max_price !== $displayPrice)
                        - Rs.{{ number_format((float) $product->variants_max_price, 0) }}
                    @endif
                </span>
            </div>
        </div>
    </div>
</div>
