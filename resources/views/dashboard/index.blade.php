@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <h1 class="h3 mb-0 text-primary">
            <i class="bi bi-speedometer2 me-2"></i>Dashboard
        </h1>
        <div class="text-muted small">
            <i class="bi bi-arrow-repeat me-1"></i>
            Last updated: <span id="lastUpdated">{{ now()->format('H:i:s') }}</span>
            <span class="badge bg-success-subtle text-success ms-2">Live (30s poll)</span>
        </div>
    </div>

    <!-- Statistics Cards (IDs for AJAX targeting) -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card stat-card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-primary text-white rounded-3 me-3">
                            <i class="bi bi-boxes"></i>
                        </div>
                        <div>
                            <p class="text-muted small mb-0">Total Products</p>
                            <h3 class="mb-0 fw-bold text-primary" id="statTotalProducts">{{ $totalProducts }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card stat-card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-warning text-dark rounded-3 me-3">
                            <i class="bi bi-exclamation-triangle"></i>
                        </div>
                        <div>
                            <p class="text-muted small mb-0">Low Stock</p>
                            <h3 class="mb-0 fw-bold text-warning" id="statLowStock">{{ $lowStockCount }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card stat-card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-danger text-white rounded-3 me-3">
                            <i class="bi bi-x-circle"></i>
                        </div>
                        <div>
                            <p class="text-muted small mb-0">Out of Stock</p>
                            <h3 class="mb-0 fw-bold text-danger" id="statOutOfStock">{{ $outOfStockCount }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card stat-card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-accent text-dark rounded-3 me-3">
                            <i class="bi bi-currency-dollar"></i>
                        </div>
                        <div>
                            <p class="text-muted small mb-0">Inventory Value</p>
                            <h3 class="mb-0 fw-bold text-accent" id="statInventoryValue">${{ number_format($totalInventoryValue, 0) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Recent Transactions -->
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-arrow-left-right me-2"></i>Recent Transactions</h5>
                    <a href="{{ route('transactions.index') }}" class="btn btn-sm btn-outline-light">View all</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Product</th>
                                    <th>Type</th>
                                    <th class="text-end">Qty</th>
                                    <th>When</th>
                                </tr>
                            </thead>
                            <tbody id="recentTransactionsBody">
                                @forelse($recentTransactions as $t)
                                    <tr>
                                        <td>
                                            <a href="{{ route('products.show', $t->product) }}" class="text-decoration-none small fw-semibold">
                                                {{ Str::limit($t->product->name ?? '—', 25) }}
                                            </a>
                                        </td>
                                        <td><span class="badge bg-{{ $t->type_badge }}">{{ $t->type_label }}</span></td>
                                        <td class="text-end small fw-bold">
                                            {{ $t->type === 'stock_out' ? '−' : ($t->type === 'stock_in' ? '+' : '±') }}{{ $t->quantity }}
                                        </td>
                                        <td class="small text-muted">{{ $t->created_at->diffForHumans() }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">No transactions yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Low Stock Alerts -->
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-exclamation-triangle me-2"></i>Low Stock Alerts</h5>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush" id="lowStockList">
                        @forelse ($lowStockProducts as $product)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <a href="{{ route('products.show', $product) }}" class="text-decoration-none fw-medium text-dark">
                                        {{ $product->name }}
                                    </a>
                                    <br>
                                    <small class="text-muted">Reorder at {{ $product->reorder_level }}</small>
                                </div>
                                @if ($product->isOutOfStock())
                                    <span class="badge bg-danger">0 left</span>
                                @else
                                    <span class="badge bg-warning text-dark">{{ $product->quantity }} left</span>
                                @endif
                            </li>
                        @empty
                            <li class="list-group-item text-muted text-center py-4">
                                <i class="bi bi-check-circle text-success me-1"></i> All stock levels healthy.
                            </li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Category Summary -->
    @if ($categoryCounts->isNotEmpty())
    <div class="card shadow-sm border-0 mt-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="bi bi-pie-chart me-2"></i>Products by Category</h5>
        </div>
        <div class="card-body">
            <div class="row g-2">
                @foreach ($categoryCounts as $category => $count)
                    <div class="col-6 col-md-4 col-lg-2">
                        <div class="text-center p-3 rounded bg-light-accent">
                            <div class="fw-bold text-primary fs-4">{{ $count }}</div>
                            <div class="small text-muted">{{ $category }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/dashboard-poll.js') }}"></script>
@endpush
