@extends('layouts.app')

@section('title', 'Record Transaction')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="d-flex align-items-center mb-4">
                <a href="{{ route('transactions.index') }}" class="btn btn-outline-secondary btn-sm me-3">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <div>
                    <h1 class="h3 mb-0 fw-bold text-primary">Record Inventory Transaction</h1>
                    <p class="text-muted mb-0 small">Stock levels update automatically</p>
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('transactions.store') }}" id="transactionForm">
                        @csrf

                        <div class="mb-3">
                            <label for="product_id" class="form-label fw-semibold">Product <span class="text-danger">*</span></label>
                            <select name="product_id" id="product_id" class="form-select @error('product_id') is-invalid @enderror" required>
                                <option value="">— Select product —</option>
                                @foreach($products as $p)
                                    <option value="{{ $p->id }}"
                                            data-qty="{{ $p->quantity }}"
                                            @selected(old('product_id') == $p->id)>
                                        {{ $p->name }} ({{ $p->sku }}) — Stock: {{ $p->quantity }}
                                    </option>
                                @endforeach
                            </select>
                            @error('product_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div id="currentStockInfo" class="form-text mt-1"></div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Transaction Type <span class="text-danger">*</span></label>
                            <div class="d-flex flex-wrap gap-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="type" id="type_in"
                                           value="stock_in" @checked(old('type', 'stock_in') === 'stock_in') required>
                                    <label class="form-check-label" for="type_in">
                                        <span class="badge bg-success">Stock In</span>
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="type" id="type_out"
                                           value="stock_out" @checked(old('type') === 'stock_out')>
                                    <label class="form-check-label" for="type_out">
                                        <span class="badge bg-danger">Stock Out</span>
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="type" id="type_adj"
                                           value="adjustment" @checked(old('type') === 'adjustment')>
                                    <label class="form-check-label" for="type_adj">
                                        <span class="badge bg-warning text-dark">Adjustment</span>
                                    </label>
                                </div>
                            </div>
                            @error('type')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="quantity" class="form-label fw-semibold">Quantity <span class="text-danger">*</span></label>
                            <input type="number" name="quantity" id="quantity" min="1"
                                   class="form-control @error('quantity') is-invalid @enderror"
                                   value="{{ old('quantity') }}" required>
                            @error('quantity')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div id="qtyHint" class="form-text"></div>
                        </div>

                        <div class="mb-3">
                            <label for="reference_number" class="form-label fw-semibold">Reference Number</label>
                            <input type="text" name="reference_number" id="reference_number"
                                   class="form-control @error('reference_number') is-invalid @enderror"
                                   value="{{ old('reference_number') }}"
                                   placeholder="PO-2024-001, SO-7788, etc." maxlength="100">
                            @error('reference_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="notes" class="form-label fw-semibold">Notes</label>
                            <textarea name="notes" id="notes" rows="3"
                                      class="form-control @error('notes') is-invalid @enderror"
                                      maxlength="1000">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check2-circle me-1"></i> Save Transaction
                            </button>
                            <a href="{{ route('transactions.index') }}" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const productSelect = document.getElementById('product_id');
    const qtyInput = document.getElementById('quantity');
    const stockInfo = document.getElementById('currentStockInfo');
    const qtyHint = document.getElementById('qtyHint');
    const typeRadios = document.querySelectorAll('input[name="type"]');

    function updateStockInfo() {
        const opt = productSelect.options[productSelect.selectedIndex];
        const qty = opt ? parseInt(opt.dataset.qty || 0, 10) : null;
        if (qty !== null && productSelect.value) {
            stockInfo.innerHTML = '<i class="bi bi-box-seam me-1"></i>Current system stock: <strong>' + qty + '</strong>';
            const type = document.querySelector('input[name="type"]:checked')?.value;
            if (type === 'stock_out') {
                qtyInput.max = qty;
                qtyHint.textContent = 'Maximum allowed for stock-out: ' + qty;
            } else {
                qtyInput.removeAttribute('max');
                qtyHint.textContent = '';
            }
        } else {
            stockInfo.textContent = '';
            qtyHint.textContent = '';
            qtyInput.removeAttribute('max');
        }
    }

    productSelect.addEventListener('change', updateStockInfo);
    typeRadios.forEach(r => r.addEventListener('change', updateStockInfo));
    updateStockInfo();
});
</script>
@endpush
