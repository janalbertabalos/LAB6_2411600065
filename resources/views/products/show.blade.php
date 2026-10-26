@extends('layouts.app')

@section('title', $product->name)

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-primary">
            <i class="bi bi-box me-2"></i>{{ $product->name }}
        </h1>
        <div class="btn-group">
            <a href="{{ route('products.edit', $product) }}" class="btn btn-outline-secondary">
                <i class="bi bi-pencil me-1"></i> Edit
            </a>
            <form action="{{ route('products.destroy', $product) }}" method="POST" class="d-inline"
                  onsubmit="return confirm('Are you sure you want to delete this product?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger">
                    <i class="bi bi-trash me-1"></i> Delete
                </button>
            </form>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Product Details</h5>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-4 text-muted">SKU</dt>
                        <dd class="col-sm-8"><code>{{ $product->sku }}</code></dd>

                        <dt class="col-sm-4 text-muted">Category</dt>
                        <dd class="col-sm-8">
                            <span class="badge bg-light-accent text-dark">{{ $product->category }}</span>
                        </dd>

                        <dt class="col-sm-4 text-muted">Description</dt>
                        <dd class="col-sm-8">{{ $product->description ?: '—' }}</dd>

                        <dt class="col-sm-4 text-muted">Supplier</dt>
                        <dd class="col-sm-8">{{ $product->supplier ?: '—' }}</dd>

                        <dt class="col-sm-4 text-muted">Created</dt>
                        <dd class="col-sm-8">{{ $product->created_at->format('M d, Y H:i') }}</dd>

                        <dt class="col-sm-4 text-muted">Last Updated</dt>
                        <dd class="col-sm-8">{{ $product->updated_at->format('M d, Y H:i') }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm border-0 mb-3">
                <div class="card-body text-center">
                    <p class="text-muted mb-1 small">Current Stock</p>
                    <h2 class="display-5 fw-bold text-primary mb-2">{{ $product->quantity }}</h2>
                    @if ($product->isOutOfStock())
                        <span class="badge bg-danger fs-6">Out of Stock</span>
                    @elseif ($product->isLowStock())
                        <span class="badge bg-warning text-dark fs-6">Low Stock</span>
                    @else
                        <span class="badge bg-success fs-6">In Stock</span>
                    @endif
                    <hr>
                    <div class="d-flex justify-content-between small">
                        <span class="text-muted">Reorder Level</span>
                        <span class="fw-medium">{{ $product->reorder_level }}</span>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Unit Price</span>
                        <span class="fw-bold">${{ number_format($product->unit_price, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Inventory Value</span>
                        <span class="fw-bold text-accent">${{ number_format($product->inventory_value, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Transaction History -->
    <div class="card shadow-sm border-0 mt-4">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Transaction History</h5>
            <a href="{{ route('transactions.create') }}?product_id={{ $product->id }}" class="btn btn-sm btn-outline-light">
                <i class="bi bi-plus-lg me-1"></i> New Transaction
            </a>
        </div>
        <div class="table-responsive">
            <table class="table table-sm table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>Type</th>
                        <th class="text-end">Qty</th>
                        <th>Reference</th>
                        <th>User</th>
                        <th>Notes</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($product->transactions()->with('user')->latest()->limit(50)->get() as $t)
                        <tr>
                            <td class="small text-nowrap">{{ $t->created_at->format('M d, Y H:i') }}</td>
                            <td><span class="badge bg-{{ $t->type_badge }}">{{ $t->type_label }}</span></td>
                            <td class="text-end fw-bold {{ $t->type === 'stock_out' ? 'text-danger' : ($t->type === 'stock_in' ? 'text-success' : 'text-warning') }}">
                                {{ $t->type === 'stock_out' ? '−' : ($t->type === 'stock_in' ? '+' : '±') }}{{ $t->quantity }}
                            </td>
                            <td class="small">{{ $t->reference_number ?? '—' }}</td>
                            <td class="small">{{ $t->user->name ?? '—' }}</td>
                            <td class="small text-muted">{{ Str::limit($t->notes, 40) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">No transactions recorded for this product yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        <a href="{{ route('products.index') }}" class="btn btn-outline-primary">
            <i class="bi bi-arrow-left me-1"></i> Back to Products
        </a>
        <a href="{{ route('transactions.create') }}" class="btn btn-primary">
            <i class="bi bi-arrow-left-right me-1"></i> Record Transaction
        </a>
    </div>
</div>
@endsection
