@extends('layouts.seller')

@section('customCss')
    <style>
        .pv-title {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
            font-size: 15px;
            font-weight: 600;
            margin-bottom: 14px;
            padding-bottom: 8px;
            border-bottom: 1px solid #e2e8f0;
        }

        .pv-main-img {
            width: 100%;
            aspect-ratio: 1 / 1;
            object-fit: cover;
            border-radius: 12px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
        }

        .pv-no-img {
            width: 100%;
            aspect-ratio: 1 / 1;
            border-radius: 12px;
            background: #f8fafc;
            border: 2px dashed #cbd5e1;
            color: #94a3b8;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }

        .pv-no-img i {
            font-size: 40px;
        }

        .pv-thumbs {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 12px;
        }

        .pv-thumb {
            width: 64px;
            height: 64px;
            object-fit: cover;
            border-radius: 8px;
            border: 2px solid transparent;
            opacity: .7;
            cursor: pointer;
            transition: opacity .2s, border-color .2s;
        }

        .pv-thumb:hover {
            opacity: 1;
        }

        .pv-thumb.active {
            border-color: #198754;
            opacity: 1;
        }

        .pv-price {
            font-size: 28px;
            font-weight: 700;
            line-height: 1.2;
        }

        .pv-compare {
            margin-left: 8px;
            font-size: 16px;
            color: #94a3b8;
            text-decoration: line-through;
            font-weight: 400;
        }

        .pv-discount {
            margin-left: 8px;
            padding: 2px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            background: #e8f5ee;
            color: #146c43;
            vertical-align: middle;
        }

        .pv-from {
            font-size: 13px;
            color: #64748b;
            font-weight: 400;
        }

        .pv-dl {
            display: grid;
            grid-template-columns: 130px 1fr;
            gap: 10px 16px;
            margin: 0;
            font-size: 14px;
        }

        .pv-dl dt {
            color: #64748b;
            font-weight: 500;
        }

        .pv-dl dd {
            margin: 0;
            font-weight: 500;
        }

        .pv-stat {
            display: flex;
            align-items: center;
            gap: 14px;
            height: 100%;
            padding: 16px;
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
        }

        .pv-stat-icon {
            width: 44px;
            height: 44px;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            background: #e8f5ee;
            color: #198754;
            font-size: 20px;
        }

        .pv-stat-icon.danger {
            background: #fee2e2;
            color: #dc3545;
        }

        .pv-stat-label {
            font-size: 12px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: .03em;
        }

        .pv-stat-value {
            font-size: 22px;
            font-weight: 700;
            line-height: 1.2;
        }

        .pv-stat-value.small {
            font-size: 15px;
        }

        .pv-option {
            margin-bottom: 14px;
        }

        .pv-option:last-child {
            margin-bottom: 0;
        }

        .pv-option-name {
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 6px;
        }

        .pv-chip {
            display: inline-block;
            margin: 0 6px 6px 0;
            padding: 3px 12px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 500;
            background: #e8f5ee;
            color: #146c43;
        }

        .pv-activity {
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .pv-activity li {
            display: flex;
            gap: 12px;
            padding: 10px 0;
            border-bottom: 1px solid #f1f5f9;
        }

        .pv-activity li:last-child {
            border-bottom: 0;
        }

        .pv-dot {
            width: 32px;
            height: 32px;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            font-size: 14px;
        }

        .pv-dot.in {
            background: #e8f5ee;
            color: #198754;
        }

        .pv-dot.out {
            background: #fee2e2;
            color: #dc3545;
        }

        .pv-activity-title {
            font-weight: 600;
            font-size: 14px;
        }

        .pv-activity-sub {
            font-size: 12px;
            color: #94a3b8;
        }

        .pv-desc {
            font-size: 14px;
            line-height: 1.7;
            overflow-wrap: anywhere;
        }

        .pv-desc img {
            max-width: 100%;
            height: auto;
        }

        .pv-muted {
            color: #94a3b8;
            font-size: 14px;
        }

        .pv-stock-num {
            display: inline-block;
            min-width: 36px;
            padding: 4px 12px;
            background: #f1f5f9;
            border-radius: 8px;
            font-weight: 600;
            text-align: center;
        }
    </style>
@endsection

@section('content')
    @php
        $lowStockAt = 5;

        $hasDiscount = $product->compare_price && (float) $product->compare_price > (float) $product->price;
        $discount = $hasDiscount ? round((1 - (float) $product->price / (float) $product->compare_price) * 100) : 0;

        $minPrice =
            $product->has_variants && $product->variants->isNotEmpty()
                ? (float) $product->variants->min('price')
                : (float) $product->price;
        $maxPrice =
            $product->has_variants && $product->variants->isNotEmpty()
                ? (float) $product->variants->max('price')
                : (float) $product->price;

        $outOfStockCount = $product->variants->where('stock', '<', 1)->count();
        $lastMove = $movements->first();

        $stockBadge = fn($n) => $n < 1 ? 'failed' : ($n <= $lowStockAt ? 'pending' : 'success');
        $stockLabel = fn($n) => $n < 1 ? 'Out of stock' : ($n <= $lowStockAt ? 'Low stock' : 'In stock');

        $statusBadge = match ($product->status) {
            'active' => 'success',
            'inactive' => 'failed',
            default => 'pending',
        };

        $reasonLabels = [
            'initial' => 'Initial stock',
            'restock' => 'Restock',
            'sale' => 'Sale',
            'return' => 'Customer return',
            'damaged' => 'Damaged / lost',
            'correction' => 'Correction',
        ];

        $allowedTags =
            '<p><br><ul><ol><li><strong><b><em><i><u><h2><h3><h4><blockquote><span><table><thead><tbody><tr><th><td><img>';
    @endphp

    <div class="page-header">
        <div>
            <h1 class="page-title">{{ $product->name }}</h1>
            <p class="page-subtitle">
                {{ $product->shop->name ?? '—' }} &middot; {{ $product->category->name ?? '—' }}
            </p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('seller.products.index') }}" class="btn-custom btn-custom-light">Back to Products</a>
            <a href="{{ route('seller.products.inventory', $product) }}" class="btn-custom btn-custom-light">
                <i class="bi bi-clock-history" aria-hidden="true"></i> View inventory history
            </a>
            <a href="{{ route('seller.products.edit', $product) }}" class="btn-quick-action">
                <i class="bi bi-pencil" aria-hidden="true"></i>
                <span>Edit Product</span>
            </a>
        </div>
    </div>

    {{-- TOP: gallery + overview --}}
    <div class="row g-4 mb-4">
        <div class="col-lg-5">
            <div class="card border-light shadow-sm p-4 h-100">
                @if ($product->images->isNotEmpty())
                    <img id="pvMainImage" src="{{ $product->images->first()->url }}" alt="{{ $product->name }}"
                        class="pv-main-img">

                    @if ($product->images->count() > 1)
                        <div class="pv-thumbs">
                            @foreach ($product->images as $img)
                                <img src="{{ $img->url }}" data-src="{{ $img->url }}" alt=""
                                    class="pv-thumb {{ $loop->first ? 'active' : '' }}">
                            @endforeach
                        </div>
                    @endif
                @else
                    <div class="pv-no-img">
                        <i class="bi bi-image" aria-hidden="true"></i>
                        <span>No images added</span>
                    </div>
                @endif
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card border-light shadow-sm p-4 h-100">
                <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                    <span class="badge-table {{ $statusBadge }}">{{ ucfirst($product->status) }}</span>
                    @if ($product->is_featured)
                        <span class="badge-table success">Featured</span>
                    @endif
                    <span class="badge-table {{ $stockBadge($product->stock) }}">{{ $stockLabel($product->stock) }}</span>
                </div>

                <div class="pv-price mb-3">
                    @if ($product->has_variants && $minPrice !== $maxPrice)
                        {{ number_format($minPrice, 2) }} – {{ number_format($maxPrice, 2) }}
                    @else
                        {{ number_format($minPrice, 2) }}
                    @endif

                    @if (!$product->has_variants && $hasDiscount)
                        <span class="pv-compare">{{ number_format((float) $product->compare_price, 2) }}</span>
                        <span class="pv-discount">{{ $discount }}% off</span>
                    @endif

                    @if ($product->has_variants)
                        <div class="pv-from">Price range across {{ $product->variants->count() }} variants</div>
                    @endif
                </div>

                @if ($product->short_description)
                    <p class="mb-4" style="font-size:14px;color:#475569;">{{ $product->short_description }}</p>
                @endif

                <dl class="pv-dl">
                    <dt>Shop</dt>
                    <dd>{{ $product->shop->name ?? '—' }}</dd>
                    <dt>Category</dt>
                    <dd>{{ $product->category->name ?? '—' }}</dd>
                    <dt>Collection</dt>
                    <dd>{{ $product->collection->name ?? '—' }}</dd>
                    <dt>Variants</dt>
                    <dd>{{ $product->has_variants ? $product->variants->count() . ' variants' : 'No variants' }}</dd>
                    <dt>Added</dt>
                    <dd>{{ $product->created_at->format('d M Y, H:i') }}</dd>
                    <dt>Last updated</dt>
                    <dd>{{ $product->updated_at->diffForHumans() }}</dd>
                </dl>
            </div>
        </div>
    </div>

    {{-- STAT TILES --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="pv-stat">
                <div class="pv-stat-icon"><i class="bi bi-box-seam"></i></div>
                <div>
                    <div class="pv-stat-label">Total stock</div>
                    <div class="pv-stat-value">{{ number_format($product->stock) }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="pv-stat">
                <div class="pv-stat-icon"><i class="bi bi-diagram-3"></i></div>
                <div>
                    <div class="pv-stat-label">Variants</div>
                    <div class="pv-stat-value">{{ $product->has_variants ? $product->variants->count() : 0 }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="pv-stat">
                <div
                    class="pv-stat-icon {{ $outOfStockCount > 0 || (!$product->has_variants && $product->stock < 1) ? 'danger' : '' }}">
                    <i class="bi bi-exclamation-triangle"></i>
                </div>
                <div>
                    <div class="pv-stat-label">Out of stock</div>
                    <div class="pv-stat-value">
                        @if ($product->has_variants)
                            {{ $outOfStockCount }} <span class="pv-from">of {{ $product->variants->count() }}</span>
                        @else
                            {{ $product->stock < 1 ? 'Yes' : 'No' }}
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="pv-stat">
                <div class="pv-stat-icon"><i class="bi bi-clock-history"></i></div>
                <div>
                    <div class="pv-stat-label">Last stock change</div>
                    <div class="pv-stat-value small">
                        {{ $lastMove ? $lastMove->created_at->diffForHumans() : 'No changes yet' }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        {{-- LEFT: inventory + description --}}
        <div class="col-lg-8">
            <div class="card border-light shadow-sm p-4 mb-4" id="inventory">
                <div class="pv-title">
                    <span>Inventory</span>
                    <a href="{{ route('seller.products.inventory', $product) }}" class="btn-custom btn-custom-light">
                        <i class="bi bi-clock-history" aria-hidden="true"></i> View inventory history
                    </a>
                </div>

                @if ($errors->hasAny(['quantity', 'action', 'reason', 'note', 'product_variant_id']))
                    <div class="alert alert-danger">
                        @foreach ($errors->only(['quantity', 'action', 'reason', 'note', 'product_variant_id']) as $messages)
                            @foreach ((array) $messages as $msg)
                                <div>{{ $msg }}</div>
                            @endforeach
                        @endforeach
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table-custom">
                        <thead>
                            <tr>
                                <th>{{ $product->has_variants ? 'Variant' : 'Item' }}</th>
                                <th>SKU</th>
                                <th>Price</th>
                                <th>Stock</th>
                                <th>Status</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($product->has_variants)
                                @foreach ($product->variants as $variant)
                                    <tr>
                                        <td><strong>{{ $variant->title }}</strong></td>
                                        <td>{{ $variant->sku ?: '—' }}</td>
                                        <td>
                                            {{ number_format((float) $variant->price, 2) }}
                                            @if ($variant->compare_price && (float) $variant->compare_price > (float) $variant->price)
                                                <small
                                                    class="pv-compare">{{ number_format((float) $variant->compare_price, 2) }}</small>
                                            @endif
                                        </td>
                                        <td><span class="pv-stock-num">{{ $variant->stock }}</span></td>
                                        <td><span
                                                class="badge-table {{ $stockBadge($variant->stock) }}">{{ $stockLabel($variant->stock) }}</span>
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn-custom btn-custom-light"
                                                data-bs-toggle="modal" data-bs-target="#stockModal"
                                                data-variant-id="{{ $variant->id }}" data-label="{{ $variant->title }}"
                                                data-stock="{{ $variant->stock }}">
                                                <i class="bi bi-plus-slash-minus" aria-hidden="true"></i> Adjust stock
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td><strong>{{ $product->name }}</strong></td>
                                    <td>—</td>
                                    <td>{{ number_format((float) $product->price, 2) }}</td>
                                    <td><span class="pv-stock-num">{{ $product->stock }}</span></td>
                                    <td><span
                                            class="badge-table {{ $stockBadge($product->stock) }}">{{ $stockLabel($product->stock) }}</span>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn-custom btn-custom-light" data-bs-toggle="modal"
                                            data-bs-target="#stockModal" data-variant-id=""
                                            data-label="{{ $product->name }}" data-stock="{{ $product->stock }}">
                                            <i class="bi bi-plus-slash-minus" aria-hidden="true"></i> Adjust stock
                                        </button>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card border-light shadow-sm p-4">
                <div class="pv-title"><span>Description</span></div>
                @if (filled(strip_tags((string) $product->description)))
                    <div class="pv-desc">{!! strip_tags($product->description, $allowedTags) !!}</div>
                @else
                    <div class="pv-muted">No description added.</div>
                @endif
            </div>
        </div>

        {{-- RIGHT: options + recent activity --}}
        <div class="col-lg-4">
            <div class="card border-light shadow-sm p-4 mb-4">
                <div class="pv-title"><span>Options</span></div>
                @forelse ($product->options as $option)
                    <div class="pv-option">
                        <div class="pv-option-name">{{ $option->name }}</div>
                        @foreach ($option->values as $value)
                            <span class="pv-chip">{{ $value }}</span>
                        @endforeach
                    </div>
                @empty
                    <div class="pv-muted">This product has no options.</div>
                @endforelse
            </div>

            <div class="card border-light shadow-sm p-4">
                <div class="pv-title"><span>Recent stock activity</span></div>

                @if ($movements->isEmpty())
                    <div class="pv-muted">No stock changes yet.</div>
                @else
                    <ul class="pv-activity">
                        @foreach ($movements as $m)
                            <li>
                                <div class="pv-dot {{ $m->quantity_change > 0 ? 'in' : 'out' }}">
                                    <i
                                        class="bi {{ $m->quantity_change > 0 ? 'bi-arrow-down-short' : 'bi-arrow-up-short' }}"></i>
                                </div>
                                <div>
                                    <div class="pv-activity-title">
                                        {{ $m->quantity_change > 0 ? '+' : '' }}{{ $m->quantity_change }}
                                        &middot; {{ $reasonLabels[$m->reason] ?? ucfirst($m->reason) }}
                                    </div>
                                    <div class="pv-activity-sub">
                                        @if ($product->has_variants)
                                            {{ $m->variant_title ?? 'Product' }} &middot;
                                        @endif
                                        now {{ $m->stock_after }} &middot; {{ $m->created_at->diffForHumans() }}
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif

                <a href="{{ route('seller.products.inventory', $product) }}"
                    class="btn-custom btn-custom-light w-100 mt-3 text-center">View all history</a>
            </div>
        </div>
    </div>

    @include('seller.products._stock_modal', ['product' => $product])
@endsection

@section('customjs')
    <script>
        // gallery: click a thumbnail to show it in the big picture
        (function() {
            const main = document.getElementById('pvMainImage');
            if (!main) return;

            document.querySelectorAll('.pv-thumb').forEach(thumb => {
                thumb.addEventListener('click', () => {
                    main.src = thumb.dataset.src;
                    document.querySelectorAll('.pv-thumb').forEach(t => t.classList.remove('active'));
                    thumb.classList.add('active');
                });
            });
        })();
    </script>
@endsection
