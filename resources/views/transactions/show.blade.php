@extends('layouts.app')

@section('title', 'Transaction Details')

@section('content')
<div class="container py-4">
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('transactions.index') }}" class="btn btn-outline-secondary btn-sm me-3">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h1 class="h3 mb-0 fw-bold text-primary">Transaction #{{ $transaction->id }}</h1>
            <p class="text-muted mb-0 small">{{ $transaction->created_at->format('F d, Y \a\t H:i') }}</p>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-receipt me-2"></i>Transaction Details</h5>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-4 text-muted">Type</dt>
                        <dd class="col-sm-8">
                            <span class="badge bg-{{ $transaction->type_badge }} fs-6">{{ $transaction->type_label }}</span>
                        </dd>
                        <dt class="col-sm-4 text-muted">Quantity</dt>
                        <dd class="col-sm-8 fw-bold">{{ $transaction->quantity }}</dd>
                        <dt class="col-sm-4 text-muted">Reference</dt>
                        <dd class="col-sm-8">{{ $transaction->reference_number ?? '—' }}</dd>
                        <dt class="col-sm-4 text-muted">Notes</dt>
                        <dd class="col-sm-8">{{ $transaction->notes ?? '—' }}</dd>
                        <dt class="col-sm-4 text-muted">Recorded</dt>
                        <dd class="col-sm-8">{{ $transaction->created_at->format('M d, Y H:i:s') }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="bi bi-box-seam me-2"></i>Product</h5>
                </div>
                <div class="card-body">
                    @if($transaction->product)
                        <h5 class="mb-1">
                            <a href="{{ route('products.show', $transaction->product) }}" class="text-decoration-none">
                                {{ $transaction->product->name }}
                            </a>
                        </h5>
                        <p class="text-muted small mb-2">SKU: {{ $transaction->product->sku }}</p>
                        <p class="mb-0">
                            Current stock:
                            <span class="badge bg-{{ $transaction->product->stock_status_color }}">
                                {{ $transaction->product->quantity }} — {{ $transaction->product->stock_status }}
                            </span>
                        </p>
                    @else
                        <p class="text-muted mb-0">Product no longer exists.</p>
                    @endif
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="bi bi-person me-2"></i>Performed By</h5>
                </div>
                <div class="card-body">
                    <p class="mb-0 fw-semibold">{{ $transaction->user->name ?? 'Unknown' }}</p>
                    <p class="text-muted small mb-0">{{ $transaction->user->email ?? '' }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <a href="{{ route('transactions.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back to list
        </a>
        <form action="{{ route('transactions.destroy', $transaction) }}" method="POST" class="d-inline"
              onsubmit="return confirm('Delete this transaction record? Stock will NOT be reversed.');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-outline-danger">
                <i class="bi bi-trash me-1"></i> Delete record
            </button>
        </form>
    </div>
</div>
@endsection
