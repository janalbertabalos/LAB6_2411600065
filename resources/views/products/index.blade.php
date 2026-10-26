@extends('layouts.app')

@section('title', 'Products')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-primary">
            <i class="bi bi-box-seam me-2"></i>Inventory Products
        </h1>
        <a href="{{ route('products.create') }}" class="btn btn-accent">
            <i class="bi bi-plus-lg me-1"></i> Add Product
        </a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-primary text-white">
                        <tr>
                            <th scope="col">SKU</th>
                            <th scope="col">Name</th>
                            <th scope="col">Category</th>
                            <th scope="col" class="text-center">Qty</th>
                            <th scope="col" class="text-end">Unit Price</th>
                            <th scope="col">Status</th>
                            <th scope="col" class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($products as $product)
                            <tr>
                                <td><code class="text-secondary">{{ $product->sku }}</code></td>
                                <td>
                                    <a href="{{ route('products.show', $product) }}" class="text-decoration-none fw-medium text-dark">
                                        {{ $product->name }}
                                    </a>
                                </td>
                                <td>
                                    <span class="badge bg-light-accent text-dark">{{ $product->category }}</span>
                                </td>
                                <td class="text-center">{{ $product->quantity }}</td>
                                <td class="text-end">${{ number_format($product->unit_price, 2) }}</td>
                                <td>
                                    @if ($product->isOutOfStock())
                                        <span class="badge bg-danger">Out of Stock</span>
                                    @elseif ($product->isLowStock())
                                        <span class="badge bg-warning text-dark">Low Stock</span>
                                    @else
                                        <span class="badge bg-success">In Stock</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('products.show', $product) }}"
                                           class="btn btn-outline-primary" title="View">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('products.edit', $product) }}"
                                           class="btn btn-outline-secondary" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('products.destroy', $product) }}"
                                              method="POST" class="d-inline"
                                              onsubmit="return confirm('Are you sure you want to delete this product?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger" title="Delete">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox display-4 d-block mb-2"></i>
                                    No products found. <a href="{{ route('products.create') }}">Add your first product</a>.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($products->hasPages())
            <div class="card-footer bg-white border-0">
                {{ $products->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
