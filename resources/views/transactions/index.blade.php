@extends('layouts.app')

@section('title', 'Inventory Transactions')

@section('content')
<div class="container py-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <div>
            <h1 class="h3 mb-1 fw-bold text-primary">
                <i class="bi bi-arrow-left-right me-2"></i>Inventory Transactions
            </h1>
            <p class="text-muted mb-0">Audit trail of every stock movement</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('transactions.adjustment') }}" class="btn btn-outline-warning">
                <i class="bi bi-sliders me-1"></i> Stock Adjustment
            </a>
            <a href="{{ route('transactions.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i> Record Transaction
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('info'))
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            {{ session('info') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Filters -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('transactions.index') }}" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Product</label>
                    <select name="product_id" class="form-select form-select-sm">
                        <option value="">All products</option>
                        @foreach($products as $p)
                            <option value="{{ $p->id }}" @selected(request('product_id') == $p->id)>
                                {{ $p->name }} ({{ $p->sku }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-semibold">Type</label>
                    <select name="type" class="form-select form-select-sm">
                        <option value="">All types</option>
                        <option value="stock_in" @selected(request('type') === 'stock_in')>Stock In</option>
                        <option value="stock_out" @selected(request('type') === 'stock_out')>Stock Out</option>
                        <option value="adjustment" @selected(request('type') === 'adjustment')>Adjustment</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-semibold">From</label>
                    <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-semibold">To</label>
                    <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="bi bi-funnel me-1"></i> Filter
                    </button>
                    <a href="{{ route('transactions.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Table -->
    <div class="card shadow-sm border-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Date / Time</th>
                        <th>Product</th>
                        <th>Type</th>
                        <th class="text-end">Qty</th>
                        <th>Reference</th>
                        <th>User</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $t)
                        <tr>
                            <td class="text-nowrap small">
                                {{ $t->created_at->format('M d, Y') }}<br>
                                <span class="text-muted">{{ $t->created_at->format('H:i') }}</span>
                            </td>
                            <td>
                                <a href="{{ route('products.show', $t->product) }}" class="text-decoration-none fw-semibold">
                                    {{ $t->product->name ?? '—' }}
                                </a>
                                <br><small class="text-muted">{{ $t->product->sku ?? '' }}</small>
                            </td>
                            <td>
                                <span class="badge bg-{{ $t->type_badge }}">{{ $t->type_label }}</span>
                            </td>
                            <td class="text-end fw-bold {{ $t->type === 'stock_out' ? 'text-danger' : ($t->type === 'stock_in' ? 'text-success' : 'text-warning') }}">
                                {{ $t->type === 'stock_out' ? '−' : ($t->type === 'stock_in' ? '+' : '±') }}{{ $t->quantity }}
                            </td>
                            <td class="small">{{ $t->reference_number ?? '—' }}</td>
                            <td class="small">{{ $t->user->name ?? '—' }}</td>
                            <td class="text-center">
                                <a href="{{ route('transactions.show', $t) }}" class="btn btn-sm btn-outline-primary" title="View">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <form action="{{ route('transactions.destroy', $t) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Delete this transaction record? Stock will NOT be reversed.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-5">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                No transactions found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($transactions->hasPages())
            <div class="card-footer bg-white">
                {{ $transactions->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
