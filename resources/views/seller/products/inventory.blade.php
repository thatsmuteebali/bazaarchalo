@extends('layouts.seller')

@section('customCss')
    <style>
        .inv-stat {
            display: flex;
            align-items: center;
            gap: 14px;
            height: 100%;
            padding: 16px;
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
        }

        .inv-stat-icon {
            width: 44px;
            height: 44px;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            font-size: 20px;
            background: #e8f5ee;
            color: #198754;
        }

        .inv-stat-icon.out {
            background: #fee2e2;
            color: #dc3545;
        }

        .inv-stat-icon.neutral {
            background: #f1f5f9;
            color: #475569;
        }

        .inv-stat-label {
            font-size: 12px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: .03em;
        }

        .inv-stat-value {
            font-size: 22px;
            font-weight: 700;
            line-height: 1.2;
        }

        .inv-plus {
            color: #198754;
            font-weight: 700;
        }

        .inv-minus {
            color: #dc3545;
            font-weight: 700;
        }

        .inv-filter label {
            display: block;
            margin-bottom: 4px;
            font-size: 12px;
            font-weight: 600;
            color: #64748b;
        }
    </style>
@endsection

@section('content')
    @php
        $reasonLabels = [
            'initial' => 'Initial stock',
            'restock' => 'Restock',
            'sale' => 'Sale',
            'return' => 'Customer return',
            'damaged' => 'Damaged / lost',
            'correction' => 'Correction',
        ];

        $reasonBadge = fn($r) => match ($r) {
            'initial', 'restock', 'return' => 'success',
            'damaged' => 'failed',
            default => 'pending',
        };

        $hasFilters = $variantId > 0 || $reason || $type || $from || $to;
        $net = (int) $totals->added - (int) $totals->removed;
    @endphp

    <div class="page-header">
        <div>
            <h1 class="page-title">Inventory History</h1>
            <p class="page-subtitle">
                {{ $product->name }} &middot; current stock <strong>{{ number_format($product->stock) }}</strong>
            </p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('seller.products.show', $product) }}" class="btn-custom btn-custom-light">
                <i class="bi bi-arrow-left" aria-hidden="true"></i> Back to Product
            </a>
            {{-- <a href="{{ route('seller.products.inventory', array_merge(request()->query(), ['product' => $product, 'export' => 1])) }}"
                class="btn-quick-action">
                <i class="bi bi-download" aria-hidden="true"></i>
                <span>Export CSV</span>
            </a> --}}
        </div>
    </div>

    {{-- TOTALS (for the current filters) --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="inv-stat">
                <div class="inv-stat-icon"><i class="bi bi-box-arrow-in-down"></i></div>
                <div>
                    <div class="inv-stat-label">Added</div>
                    <div class="inv-stat-value">+{{ number_format($totals->added) }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="inv-stat">
                <div class="inv-stat-icon out"><i class="bi bi-box-arrow-up"></i></div>
                <div>
                    <div class="inv-stat-label">Removed</div>
                    <div class="inv-stat-value">-{{ number_format($totals->removed) }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="inv-stat">
                <div class="inv-stat-icon neutral"><i class="bi bi-plus-slash-minus"></i></div>
                <div>
                    <div class="inv-stat-label">Net change</div>
                    <div class="inv-stat-value {{ $net > 0 ? 'inv-plus' : ($net < 0 ? 'inv-minus' : '') }}">
                        {{ $net > 0 ? '+' : '' }}{{ number_format($net) }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="inv-stat">
                <div class="inv-stat-icon neutral"><i class="bi bi-list-ul"></i></div>
                <div>
                    <div class="inv-stat-label">Entries</div>
                    <div class="inv-stat-value">{{ number_format($totals->entries) }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="table-card-custom">
        <div class="table-header-control">
            <form method="GET" action="{{ route('seller.products.inventory', $product) }}"
                class="row g-2 w-100 align-items-end inv-filter">
                @if ($product->has_variants)
                    <div class="col-md-3">
                        <label for="f_variant">Variant</label>
                        <select name="variant" id="f_variant" class="form-select-custom">
                            <option value="">All variants</option>
                            @foreach ($variants as $v)
                                <option value="{{ $v->id }}" @selected((int) $variantId === (int) $v->id)>{{ $v->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <div class="col-md-2">
                    <label for="f_reason">Reason</label>
                    <select name="reason" id="f_reason" class="form-select-custom">
                        <option value="">All reasons</option>
                        @foreach ($reasonLabels as $key => $label)
                            <option value="{{ $key }}" @selected($reason === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label for="f_type">Type</label>
                    <select name="type" id="f_type" class="form-select-custom">
                        <option value="">Added &amp; removed</option>
                        <option value="in" @selected($type === 'in')>Added only</option>
                        <option value="out" @selected($type === 'out')>Removed only</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label for="f_from">From</label>
                    <input type="date" name="from" id="f_from" class="form-control-custom"
                        value="{{ optional($from)->format('Y-m-d') }}">
                </div>

                <div class="col-md-2">
                    <label for="f_to">To</label>
                    <input type="date" name="to" id="f_to" class="form-control-custom"
                        value="{{ optional($to)->format('Y-m-d') }}">
                </div>

                <div class="col-md-1 d-flex gap-1">
                    <button type="submit" class="btn-quick-action" title="Apply filters" aria-label="Apply filters">
                        <i class="bi bi-funnel" aria-hidden="true"></i>
                    </button>
                    @if ($hasFilters)
                        <a href="{{ route('seller.products.inventory', $product) }}" class="table-btn-action"
                            title="Clear filters" aria-label="Clear filters">
                            <i class="bi bi-x-lg"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table-custom">
                <thead>
                    <tr>
                        <th>Sr</th>
                        <th>Date</th>
                        <th>Item</th>
                        <th>Reason</th>
                        <th>Change</th>
                        <th>Stock (before → after)</th>
                        <th>By</th>
                        <th>Note</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($movements as $m)
                        <tr>
                            <td>{{ $movements->firstItem() + $loop->index }}</td>
                            <td>
                                {{ $m->created_at->format('d M Y') }}
                                <div class="table-user-sub">{{ $m->created_at->format('H:i') }}</div>
                            </td>
                            <td>{{ $m->variant_title ?? ($product->has_variants ? '—' : 'Product') }}</td>
                            <td><span
                                    class="badge-table {{ $reasonBadge($m->reason) }}">{{ $reasonLabels[$m->reason] ?? ucfirst($m->reason) }}</span>
                            </td>
                            <td class="{{ $m->quantity_change > 0 ? 'inv-plus' : 'inv-minus' }}">
                                {{ $m->quantity_change > 0 ? '+' : '' }}{{ $m->quantity_change }}
                            </td>
                            <td>{{ $m->stock_before }} → <strong>{{ $m->stock_after }}</strong></td>
                            <td>{{ $m->user->name ?? '—' }}</td>
                            <td>{{ $m->note ?: '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                {{ $hasFilters ? 'No stock changes match your filters.' : 'No stock changes recorded yet.' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="table-footer-control">
            {{ $movements->links() }}
        </div>
    </div>
@endsection
