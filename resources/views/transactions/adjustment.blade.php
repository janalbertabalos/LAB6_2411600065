@extends('layouts.app')

@section('title', 'Stock Adjustment')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="d-flex align-items-center mb-4">
                <a href="{{ route('transactions.index') }}" class="btn btn-outline-secondary btn-sm me-3">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <div>
                    <h1 class="h3 mb-0 fw-bold text-primary">Stock Adjustment</h1>
                    <p class="text-muted mb-0 small">Reconcile physical count with system quantity</p>
                </div>
            </div>

            @if(session('info'))
                <div class="alert alert-info">{{ session('info') }}</div>
            @endif

            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('transactions.adjustment.process') }}" id="adjForm">
                        @csrf

                        <div class="mb-3">
                            <label for="product_id" class="form-label fw-semibold">Product <span class="text-danger">*</span></label>
                            <select name="product_id" id="product_id" class="form-select @error('product_id') is-invalid @enderror" required>
                                <option value="">— Select product —</option>
                                @foreach($products as $p)
                                    <option value="{{ $p->id }}" data-qty="{{ $p->quantity }}" @selected(old('product_id') == $p->id)>
                                        {{ $p->name }} ({{ $p->sku }})
                                    </option>
                                @endforeach
                            </select>
                            @error('product_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Current System Quantity</label>
                            <input type="text" id="system_qty" class="form-control bg-light" readonly value="—">
                        </div>

                        <div class="mb-3">
                            <label for="physical_count" class="form-label fw-semibold">Physical Count <span class="text-danger">*</span></label>
                            <input type="number" name="physical_count" id="physical_count" min="0"
                                   class="form-control @error('physical_count') is-invalid @enderror"
                                   value="{{ old('physical_count') }}" required>
                            @error('physical_count')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div id="diffPreview" class="form-text"></div>
                        </div>

                        <div class="mb-3">
                            <label for="reason" class="form-label fw-semibold">Reason <span class="text-danger">*</span></label>
                            <select name="reason" id="reason" class="form-select @error('reason') is-invalid @enderror" required>
                                <option value="">— Select reason —</option>
                                <option value="Physical count discrepancy" @selected(old('reason') === 'Physical count discrepancy')>Physical count discrepancy</option>
                                <option value="Damaged goods" @selected(old('reason') === 'Damaged goods')>Damaged goods</option>
                                <option value="Theft / Loss" @selected(old('reason') === 'Theft / Loss')>Theft / Loss</option>
                                <option value="System error correction" @selected(old('reason') === 'System error correction')>System error correction</option>
                                <option value="Other" @selected(old('reason') === 'Other')>Other</option>
                            </select>
                            @error('reason')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="notes" class="form-label fw-semibold">Additional Notes</label>
                            <textarea name="notes" id="notes" rows="2" class="form-control" maxlength="1000">{{ old('notes') }}</textarea>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-warning">
                                <i class="bi bi-sliders me-1"></i> Apply Adjustment
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
    const systemQty = document.getElementById('system_qty');
    const physical = document.getElementById('physical_count');
    const diffPreview = document.getElementById('diffPreview');

    function update() {
        const opt = productSelect.options[productSelect.selectedIndex];
        const qty = opt && productSelect.value ? parseInt(opt.dataset.qty, 10) : null;
        systemQty.value = qty !== null ? qty : '—';
        if (qty !== null && physical.value !== '') {
            const diff = parseInt(physical.value, 10) - qty;
            const sign = diff > 0 ? '+' : '';
            diffPreview.innerHTML = 'Difference: <strong class="' + (diff < 0 ? 'text-danger' : diff > 0 ? 'text-success' : '') + '">' + sign + diff + '</strong>';
        } else {
            diffPreview.textContent = '';
        }
    }

    productSelect.addEventListener('change', update);
    physical.addEventListener('input', update);
    update();
});
</script>
@endpush
